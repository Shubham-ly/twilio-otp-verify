<?php

namespace Drupal\otp_verify\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Handling verify otp code form.
 *
 * @package Drupal\otp_verify\Form.
 */
class VerificationForm extends FormBase {

  /**
   * Get unique form Id.
   *
   * (@inheritdoc)
   */
  public function getFormId() {
    return 'otp_verification_form';
  }

  /**
   * Build form structure.
   *
   * (@inheritdoc)
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Verification Code'),
      '#required' => TRUE,
      '#default_value' => '',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Verify'),
    ];

    return $form;
  }

  /**
   * Make form values validation.
   *
   * (@inheritdoc)
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    if (empty($form_state->getValue('code'))) {
      $form_state->setErrorByName('code', $this->t('Invalid Code'));
    }
  }

  /**
   * Save form data.
   *
   * (@inheritdoc)
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $verification_code_service = \Drupal::service('otp_verify.verification_code');
    $code = $form_state->getValue('code');
    if ($verification_code_service->checkOtpCode($code)) {
      // Verify otp in DB.
      $user = $verification_code_service->verify($code);
      // user_login_finalize($user);
      // Redirect to home.
      \Drupal::messenger()->addMessage(t('Verified  successfully'));
      $form_state->setRedirect('<front>');
    }
  }

}
