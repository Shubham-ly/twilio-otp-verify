<?php

namespace Drupal\otp_verify\Service;

use Drupal\otp_verify\Service\Gateway\TwilioGateway;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Handler sms gateways messages.
 *
 * @package Drupal\otp_verify\Service.
 */
class SmsService {
  /**
   * Drupal\otp_verify\Service\SMSGateways\TwilioGateway definition.
   *
   * @var Drupal\otp_verify\Service\Gateway\TwilioGateway
   */
  private $smsGateway;

  /**
   * Constructor.
   *
   * @param Drupal\otp_verify\Service\Gateway\TwilioGateway $smsGateway
   *   Sms gateway type.
   */
  public function __construct(TwilioGateway $smsGateway) {
    $this->smsGateway = $smsGateway;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('otp_verify.twilio')
    );
  }

  /**
   * Send sms message to user phone with verification message.
   *
   * @param string $phone
   *   User phone number.
   * @param string $message
   *   Verification message context.
   *
   * @throws \Exception
   */
  public function sendSmsMessage($phone, $message): bool {
    return $this->smsGateway->send($phone, $message);
  }

}
