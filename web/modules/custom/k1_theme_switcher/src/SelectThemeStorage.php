<?php

namespace Drupal\k1_theme_switcher;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\UserInterface;

/**
 * Select theme storage.
 */
class SelectThemeStorage extends SqlContentEntityStorage implements SelectThemeStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function getSelectThemeByUser(AccountProxyInterface|UserInterface $user) {
    $select_theme = $this->loadByProperties([
      'uid' => $user->id(),
    ]);
    if (empty($select_theme)) {
      return NULL;
    }
    return reset($select_theme);
  }

}
