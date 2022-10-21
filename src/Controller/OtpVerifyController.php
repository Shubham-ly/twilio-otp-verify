<?php

namespace Drupal\otp_verify\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Otp Verify routes.
 */
class OtpVerifyController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
