<?php

namespace Drupal\telegram_bot\Service;

use Drupal;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\telegram_bot\Commands\UserCommands\StartCommand;
use Drupal\user\UserInterface;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Request;

/**
 * @link https://core.telegram.org/bots/api
 *
 * @package Drupal\telegram_bot\Services
 */
class TelegramBotManager implements TelegramBotManagerInterface {

  /**
   * Telegram Bot Token.
   *
   * @var mixed
   */
  protected $botToken;

  /**
   * Telegram Bot Username.
   *
   * @var mixed
   */
  protected $botUsername;

  /**
   * LoggerFactory Object.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * The ConfigFactory Object.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Constructs a New TelegramBotManager Object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   ConfigFactory Interface.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   LoggerChannel Interface.
   *
   * @throws TelegramException
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    LoggerChannelFactoryInterface $logger_factory
  ) {
    $this->loggerFactory = $logger_factory;
    $this->configFactory = $config_factory;
    $config = $config_factory->get('telegram_bot.settings');
    $this->botUsername = $config->get('name');
    $this->botToken = $config->get('token');
    $this->connect();
  }

  /**
   * Checks if Our Bot is Alive.
   *
   * @throws TelegramException
   */
  public function connect(string $botToken = NULL, string $botUsername = NULL): Telegram {
    // An Telegram Object.
    return new Telegram(
      $this->botToken ?: $botToken,
      $this->botUsername ?: $botUsername,
    );
  }

  /**
   * Getter for Bot Api Key.
   *
   * @return array|mixed|null
   *   Returns Api Key.
   */
  public function getApiKey() {
    return $this->botToken;
  }

  /**
   * Collects Command List and Handles Webhook.
   *
   * @throws TelegramException
   */
  public function handle() {
    // Connects to bot.
    $telegram = $this->connect();
    // Database configuration.
    $telegram->addCommandsPath('/www/web/modules/custom/telegram_bot/src/Commands/UserCommands');
    // Add commands classes.
    $telegram->addCommandClasses([
      StartCommand::class,
    ]);

    // Handle Webhook.
    try {
      $telegram->handle();
    }
    catch (TelegramException $e) {
      $this
        ->loggerFactory
        ->get('telegram_bot')
        ->warning(
          "Telegram Bot: There's an Exception: @warning.",
          [
            '@warning' => $e->getMessage(),
          ]
        );
    }
  }

  /**
   * Sets Webhook.
   *
   * @throws TelegramException
   */
  public function setWebhook(): void {
    // Connect to bot.
    $telegram = $this->connect();
    // Set Webhook through route.
    if (str_contains(Drupal::request()->getSchemeAndHttpHost(), 'http')) {
      $site_url = str_replace("http", "https", Drupal::request()
        ->getSchemeAndHttpHost());
    }
    else {
      $site_url = Drupal::request()->getSchemeAndHttpHost();
    }
    $site_url = $site_url . '/webhook';
    $telegram->setWebhook($site_url, ['drop_pending_updates' => TRUE]);
  }

  /**
   * Deletes Webhook.
   *
   * @throws TelegramException
   */
  public function deleteWebhook(): void {
    // Connect to bot.
    $telegram = $this->connect();
    // Delete webhook.
    $telegram->deleteWebhook();
  }

  /**
   * Sends Message to Dedicated User.
   *
   * @param string $message
   *   Message to Send.
   * @param string $chat_id
   *   User to Receive.
   *
   * @return bool
   *   Message Sending Status Sent/Unsent.
   * @throws TelegramException
   *
   */
  public function sendMessage(string $message, string $chat_id): bool {
    // Connect to Bot.
    $this->connect();
    // Get Chat ID to Send Message.
    $request = [
      'chat_id' => $chat_id,
      'text' => $message,
      'parse_mode' => 'HTML',
    ];
    // Send Request.
    $result = Request::sendMessage($request);
    // Check if it's OK.
    return $result->isOk();
  }

  /**
   * Generate telegram bot start command with login query params.
   *
   * @param \Drupal\user\UserInterface $account
   *   User Entity.
   *
   * @return string
   *   Returns URL With Start Command.
   */
  public function invitingUrlForUser(UserInterface $account): string {
    // To update, you need run cache rebuild(on the site!).
    $datetime = strtotime(date('m-Y', Drupal::time()->getCurrentTime()));
    return "https://t.me/" . $this->botUsername . "?start=" . user_pass_rehash($account, $datetime) . "-" . $account->id();
  }

  /**
   * Returns the logout URL for the Telegram bot.
   *
   * @return bool
   *   Message Sending Status Sent/Unsent
   * @throws TelegramException
   *
   */
  public function getLogoutUrl(): string {
    // Get the base URL of the Drupal site.
    $baseUrl = Drupal::request()->getSchemeAndHttpHost();

    // Return the homepage URL.
    return $baseUrl;
  }

}
