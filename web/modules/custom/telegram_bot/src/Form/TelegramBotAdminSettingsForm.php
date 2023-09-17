<?php

namespace Drupal\telegram_bot\Form;

use Drupal;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\telegram_bot\Service\TelegramBotManagerInterface;
use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Telegram Bot Settings for Our Site.
 */
class TelegramBotAdminSettingsForm extends ConfigFormBase {

  /**
   * The Telegram Bot Manager.
   *
   * @var TelegramBotManagerInterface
   */
  protected $telegramBotManager;

  /**
   * Config Object.
   *
   * @var ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->telegramBotManager = $container->get('telegram_bot.manager');
    $instance->configFactory = $container->get('config.factory');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'telegram_bot_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['telegram_bot.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get our configs.
    $config = $this->configFactory->get('telegram_bot.settings');

    $form['config'] = [
      '#type' => 'details',
      '#title' => $this->t('Configure Telegram Bot'),
      '#open' => TRUE,
    ];
    $form['config']['telegram_bot_name'] = [
      '#type' => 'textfield',
      '#default_value' => $config->get('name'),
      '#required' => TRUE,
      '#title' => $this->t('Telegram Bot Username'),
    ];
    $form['config']['telegram_bot_token'] = [
      '#type' => 'textfield',
      '#default_value' => $config->get('token'),
      '#required' => TRUE,
      '#title' => $this->t('Telegram Bot Token'),
    ];
    // Set Webhook Input.
    $form['config']['set_webhook'] = [
      '#type' => 'submit',
      '#value' => $this->t('Set Webhook'),
      '#submit' => ['::setWebhook'],
      '#disabled' => !$config->get('token'),
    ];
    // Delete Webhook Input.
    $form['config']['delete_webhook'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete Webhook'),
      '#submit' => ['::deleteWebhook'],
      '#disabled' => !$config->get('token'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * Sets Webhook for Our Telegram.
   */
  public function setWebhook(): void {
    try {
      $config = $this->config('telegram_bot.settings');
      $this->telegramBotManager->setWebhook();
      // Notifying admin that webhook was set.
      $message = 'The Webhook Was Set on ' . Drupal::request()
          ->getSchemeAndHttpHost() . '/webhook';
      $this->messenger()->addStatus($this->t('Webhook was Set!'));
    }
    catch (TelegramException $e) {
      $this->messenger()->addError($e->getMessage());
    }
  }

  /**
   * Deletes Webhook for Our Telegram.
   */
  public function deleteWebhook(): void {
    try {
      $config = $this->config('telegram_bot.settings');
      $this->telegramBotManager->deleteWebhook();
      $message = 'The Webhook Was Deleted from ' . Drupal::request()
          ->getSchemeAndHttpHost();
      // Notifying admin that webhook was removed.
      $this->messenger()->addStatus($this->t('Webhook was Deleted!'));
    }
    catch (TelegramException $e) {
      $this->messenger()->addError($e->getMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get form values and save it as config.
    $this->config('telegram_bot.settings')
      ->set('name', $form_state->getValue('telegram_bot_name'))
      ->set('token', $form_state->getValue('telegram_bot_token'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
