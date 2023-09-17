<?php

namespace Drupal\k1_theme_switcher;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\UserInterface;

/**
 * Available theme storage.
 */
class AvailableThemeStorage extends SqlContentEntityStorage implements AvailableThemeStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function getAvailableThemesByUser(AccountProxyInterface|UserInterface $user) {
    return $this->loadByProperties([
      'uid' => $user->id(),
    ]);
  }

}
