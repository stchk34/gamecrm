<?php

namespace Drupal\k1_theme_switcher\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\k1_theme_switcher\AvailableThemeStorageInterface;
use Drupal\k1_theme_switcher\Entity\SelectTheme;
use Drupal\k1_theme_switcher\SelectThemeStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Select theme user form.
 */
class SelectThemeUserForm extends FormBase {

  /**
   * Select theme storage.
   *
   * @var \Drupal\k1_theme_switcher\SelectThemeStorageInterface
   */
  protected SelectThemeStorageInterface $selectThemeStorage;

  /**
   * Available theme storage.
   *
   * @var \Drupal\k1_theme_switcher\AvailableThemeStorageInterface
   */
  protected AvailableThemeStorageInterface $availableThemeStorage;

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected AccountProxyInterface $currentUser;

  /**
   * Theme handler.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected ThemeHandlerInterface $themeHandler;

  /**
   * Constructor for SelectThemeUserForm object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, AccountProxyInterface $current_user, ThemeHandlerInterface $theme_handler) {
    $this->selectThemeStorage = $entity_type_manager->getStorage('select_theme');
    $this->availableThemeStorage = $entity_type_manager->getStorage('available_theme');
    $this->currentUser = $current_user;
    $this->themeHandler = $theme_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('current_user'),
      $container->get('theme_handler'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'select_theme_user_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = [];
    $available_themes = $this->availableThemeStorage->getAvailableThemesByUser($this->currentUser);
    foreach ($available_themes as $available_theme) {
      $options[$available_theme->getTheme()] = $this->themeHandler->getName($available_theme->getTheme());
    }
    $default_themes = ['gamecrm', 'gamecrm_dark'];
    $select_theme = $this->selectThemeStorage->getSelectThemeByUser($this->currentUser)
      ?->getTheme();
    foreach ($default_themes as $default_theme) {
      $options[$default_theme] = $this->themeHandler->getName($default_theme);
    }
    $form['theme'] = [
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $select_theme ?? $default_theme,
      '#ajax' => [
        'callback' => '::changeTheme',
        'disable-refocus' => FALSE,
        'event' => 'change',
        'wrapper' => 'edit-output',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Changing theme...'),
        ],
      ],
    ];
    return $form;
  }

  /**
   * Change theme ajax callback.
   */
  public function changeTheme(array $form, FormStateInterface $form_state) {
    $select_theme = $this->selectThemeStorage->getSelectThemeByUser($this->currentUser);
    $theme = $form_state->getValue('theme');
    if ($select_theme) {
      $select_theme->setTheme($theme)->save();
    }
    else {
      SelectTheme::create([
        'uid' => $this->currentUser->id(),
        'theme' => $theme,
      ])->save();
    }
    $response = new AjaxResponse();
    $current_url = Url::fromRoute('<current>');
    $response->addCommand(new RedirectCommand($current_url->toString()));
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    return $form;
  }

}
