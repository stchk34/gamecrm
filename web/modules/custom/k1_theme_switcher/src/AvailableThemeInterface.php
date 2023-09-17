<?php

namespace Drupal\k1_theme_switcher;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining an available theme entity type.
 */
interface AvailableThemeInterface extends ContentEntityInterface, EntityOwnerInterface {

}
