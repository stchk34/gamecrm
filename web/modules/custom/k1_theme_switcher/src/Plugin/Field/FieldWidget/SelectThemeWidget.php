<?php

namespace Drupal\k1_theme_switcher\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\k1_theme_switcher\AvailableThemeStorageInterface;
use Drupal\k1_theme_switcher\SelectThemeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'select_theme_widget' widget.
 *
 * @FieldWidget(
 *   id = "select_theme_widget",
 *   label = @Translation("Select theme widget"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class SelectThemeWidget extends WidgetBase {

  /**
   * Theme handler.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected ThemeHandlerInterface $themeHandler;

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
   * The constructor for SelectThemeWidget object.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    ThemeHandlerInterface $theme_handler,
    EntityTypeManagerInterface $entity_type_manager,
    AccountProxyInterface $current_user
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->themeHandler = $theme_handler;
    $this->availableThemeStorage = $entity_type_manager->getStorage('available_theme');
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('theme_handler'),
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $options = [];
    $default_theme = $this->themeHandler->getDefault();
    if ($items->getEntity() instanceof SelectThemeInterface) {
      $available_themes = $this->availableThemeStorage
        ->getAvailableThemesByUser($this->currentUser);
      $options[$default_theme] = $this->themeHandler->getName($default_theme);
      foreach ($available_themes as $available_theme) {
        $options[$available_theme->getTheme()] = $this->themeHandler->getName($available_theme->getTheme());
      }
    }
    else {
      $themes = $this->themeHandler->listInfo();
      foreach ($themes as $theme_name => $theme) {
        $options[$theme_name] = $this->themeHandler->getName($theme_name);
      }
    }
    $element['value'] = $element +
      [
        '#type' => 'select',
        '#options' => $options,
        '#default_value' => $items[$delta]->value ?? $default_theme,
      ];

    return $element;
  }

}
