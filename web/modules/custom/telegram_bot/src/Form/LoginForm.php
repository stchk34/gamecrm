<?php

namespace Drupal\telegram_bot\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginForm extends FormBase {

  /**
   * The Telegram Bot Manager.
   *
   * @var \Drupal\telegram_bot\Service\TelegramBotManagerInterface
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
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'login_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $user = User::load(\Drupal::currentUser()->id());
    // If the user is anonymous or already logged in through Telegram.
    if (!$user->get('field_telegram_id')->isEmpty()) {
      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Logout from Telegram'),
        '#attributes' => [
          'target' => '_blank',
        ],
      ];
    }
    else {
      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Login in Telegram'),
        '#attributes' => [
          'target' => '_blank',
        ],
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user = User::load(\Drupal::currentUser()->id());
    // If the user is anonymous or already logged in through Telegram.
    if ($user == NULL || !$user->get('field_telegram_id')->isEmpty()) {
      //Logout from Telegram.
      $user->set('field_telegram_id', '');
      $user->save();

      $logout_url = $this->telegramBotManager->getLogoutUrl();
      $response = new RedirectResponse($logout_url);
      $response->send();
    }
    else {
      //Login in Telegram.
      $login_url = $this->telegramBotManager->invitingUrlForUser($user);
      $response = new TrustedRedirectResponse($login_url);
      $response->send();
    }
  }

}
