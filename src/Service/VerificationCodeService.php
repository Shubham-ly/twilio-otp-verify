<?php

namespace Drupal\otp_verify\Service;

use Drupal\Core\Database\Connection;
use Drupal\user\UserInterface;
use Drupal\user\Entity\User;

/**
 * Verification handler.
 *
 * @package Drupal\otp_verify\Service.
 */
class VerificationCodeService {
  /**
   * Database table name.
   *
   * @var string
   */
  private $table = 'otp_verify';
  /**
   * GuzzleHttp\Client definition.
   *
   * @var GuzzleHttp\Client
   */
  private $httpClient;

  /**
   * Drupal\Core\Entity\Query\Sql\QueryFactory definition.
   *
   * @var Drupal\Core\Entity\Query\Sql\QueryFactory
   */
  private $queryFactory;

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var Drupal\Core\Entity\EntityTypeManager
   */
  private $entityTypeManager;

  /**
   * Drupal\Core\Database\Connection definition.
   *
   * @var Drupal\Core\Database\Connection
   */
  private $connection;

  /**
   * Constructor.
   *
   * @param Drupal\Core\Database\Connection $connection
   *   Database connection object.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * Generate random integer code.
   *
   * @return int
   *   Return unique otp code.
   */
  private function generateOtpCode(): int {
    $code = mt_rand(100000, 999999);
    if ($this->checkCodeExists($code)) {
      $this->generateOtpCode();
    }
    return $code;
  }

  /**
   * Store user otp code in database table.
   *
   * @param int $user_id
   *   User id.
   *
   * @return int
   *   The code generated.
   */
  public function storeCodeInDataBase($user_id): int {
    // Remove old code from DB.
    $this->removeOldCode($user_id);
    // Generate new code for user.
    $code = $this->generateOtpCode();
    // Save code to database table.
    $fields = [
      'uid' => $user_id,
      'code' => $code,
    ];
    $this->connection
      ->insert($this->table)
      ->fields($fields)
      ->execute();
    return $code;
  }

  /**
   * Get verification message.
   *
   * @param int $code
   *   Otp code.
   *
   * @return string
   *   Verification Message.
   */
  public function getVerificationMessage($code): string {
    $config = \Drupal::config('otp_verify.settings');
    $message = $config->get('verification_message_text');
    return $message . $code;
  }

  /**
   * Check if otp code exists in DB.
   *
   * @param int $code
   *   Otp code.
   *
   * @return bool
   *   Return true if code exists in database table.
   */
  private function checkCodeExists($code): bool {
    try {
      $result = $this->connection
        ->select($this->table, 't')
        ->fields('t', ['id'])
        ->condition('t.code', $code)
        ->range(0, 1)
        ->execute()
        ->fetchField();
      if ($result) {
        return TRUE;
      }
      return FALSE;
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Remove old otp user code.
   *
   * @param int $user_id
   *   Use id.
   */
  private function removeOldCode($user_id): void {
    $this->connection->delete($this->table)
      ->condition('uid', $user_id)
      ->execute();
  }

  /**
   * Check if user is verified.
   *
   * @param int $user_id
   *   User id.
   *
   * @return bool
   *   Return true if user verified.
   */
  public function isVerified($user_id): bool {
    $record = $this->connection
      ->query("SELECT uid FROM {$this->table} WHERE uid = :uid AND verified = 1",
        [':uid' => $user_id])
      ->fetchField();
    if ($record) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Check if code is valid.
   *
   * @param int $code
   *   Otp code.
   *
   * @return bool
   *   Return true if code is valid.
   */
  public function checkOtpCode($code): bool {
    $record = $this->connection->query("SELECT uid FROM {$this->table} WHERE code = :code",
      [
        ':code' => $code,
      ]
    )
      ->fetchField();
    if ($record) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Verify user based on otp code.
   *
   * @param int $code
   *   Otp code.
   *
   * @return \Drupal\user\UserInterface
   *   Return use object after activated.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function verify($code) {
    $this->connection
      ->update("{$this->table}")
      ->fields([
        'verified' => 1,
      ])->condition('code', $code)
      ->execute();
    // Make user active.
    $user_id = $this->connection
      ->query("SELECT uid FROM {$this->table} WHERE code = :code", [':code' => $code])
      ->fetchField();
    /** @var \Drupal\user\UserInterface $user */
    $user = User::load($user_id);
    $user->activate();
    $user->save();
    return $user;
  }

}
