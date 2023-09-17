<?php

namespace Drupal\k1_theme_switcher\Theme;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;
use Drupal\k1_theme_switcher\SelectThemeStorageInterface;

/**
 * Theme switcher by user.
 */
class ThemeSwitcher implements ThemeNegotiatorInterface {

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected AccountProxyInterface $currentUser;

  /**
   * Select theme storage.
   *
   * @var \Drupal\k1_theme_switcher\SelectThemeStorageInterface
   */
  protected SelectThemeStorageInterface $selectThemeStorage;

  /**
   * The constructor for ThemeSwitcher object.
   */
  public function __construct(AccountProxyInterface $current_user, EntityTypeManagerInterface $entity_type_manager) {
    $this->currentUser = $current_user;
    $this->selectThemeStorage = $entity_type_manager->getStorage('select_theme');
  }

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    if ($route_match->getRouteName() == 'entity.user.canonical') {
      return TRUE;
    }
    if ($this->selectThemeStorage->getSelectThemeByUser($this->currentUser)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    if ($route_match->getRouteName() == 'entity.user.canonical') {
      $user = $route_match->getParameter('user');
      return $this->selectThemeStorage->getSelectThemeByUser($user)
        ?->getTheme();
    }
    else {
      return $this->selectThemeStorage->getSelectThemeByUser($this->currentUser)
        ?->getTheme();
    }
  }

}
