<?php

namespace Drupal\k1_theme_switcher;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\UserInterface;

/**
 * Interface for available storage.
 */
interface AvailableThemeStorageInterface {

  /**
   * The that return available theme by user.
   */
  public function getAvailableThemesByUser(AccountProxyInterface|UserInterface $user);

}
