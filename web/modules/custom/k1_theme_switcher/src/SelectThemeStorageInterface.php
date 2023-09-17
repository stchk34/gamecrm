<?php

namespace Drupal\k1_theme_switcher;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\UserInterface;

/**
 * Interface for select theme storage.
 */
interface SelectThemeStorageInterface {

  /**
   * The method that return select theme by user.
   */
  public function getSelectThemeByUser(UserInterface|AccountProxyInterface $user);

}
