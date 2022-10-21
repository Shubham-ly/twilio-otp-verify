<?php

namespace Drupal\otp_verify\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Otp Verify settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  const CONFIG_NAME = 'otp_verify.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'otp_verify_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [self::CONFIG_NAME];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->getConfig();

    $form['sender_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Sender ID'),
      '#default_value' => $config->get('otp_verify.sender_id'),
      '#description' => $this->t('Your Account Sender ID.'),
      '#required' => TRUE,
    ];

    $form['token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your Auth Token'),
      '#default_value' => $config->get('otp_verify.token'),
      '#description' => $this->t('Your Auth Token.'),
      '#required' => TRUE,
    ];

    $form['verification_message_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Verification Message'),
      '#default_value' => $config->get('otp_verify.verification_message_text'),
      '#description' => $this->t('This is a verification message will send with code.'),
      '#required' => TRUE,
    ];

    $form['provider_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Provider phone number'),
      '#default_value' => $config->get('otp_verify.provider_number'),
      '#description' => $this->t('This is a provider phone number.'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('sender_id') == '') {
      $form_state->setErrorByName('sender_id', $this->t('The value is not correct.'));
    }
    if ($form_state->getValue('token') == '') {
      $form_state->setErrorByName('sender_id', $this->t('The value is not correct.'));
    }
    if ($form_state->getValue('provider_number') == '') {
      $form_state->setErrorByName('sender_id', $this->t('The value is not correct.'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config(self::CONFIG_NAME)
      ->set('otp_verify.sender_id', $form_state->getValue('sender_id'))
      ->set('otp_verify.token', $form_state->getValue('token'))
      ->set('otp_verify.provider_number', $form_state->getValue('provider_number'))
      ->set('otp_verify.verification_message_text', $form_state->getValue('verification_message_text'))
      ->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * Returns this modules configuration object.
   */
  protected function getConfig() {
    return $this->config(self::CONFIG_NAME);
  }

}
