<?php

namespace Drupal\telegram_bot\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;
use Drupal\telegram_bot\Service\TelegramBotManagerInterface;
use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for the Webhook Endpoint.
 */
class TelegramController extends ControllerBase {

  /**
   * The Telegram Bot Manager.
   *
   * @var TelegramBotManagerInterface
   */
  protected $telegramBotManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->telegramBotManager = $container->get('telegram_bot.manager');
    return $instance;
  }

  /**
   * Handles Telegram Webhooks.
   *
   *
   * @return Response Returns Response.
   *   Returns Response.
   */
  public function handle(): Response {
    try {
      $this->telegramBotManager->handle();
      return new Response();
    }
    catch (TelegramException $e) {
      Drupal::logger('telegram_bot')
        ->error(
          "Telegram Bot: There's an Error for Telegram: @error.",
          [
            '@error' => $e->getMessage(),
          ]
        );
      return new Response('Error processing the Telegram request.', 500);
    }
  }

}
