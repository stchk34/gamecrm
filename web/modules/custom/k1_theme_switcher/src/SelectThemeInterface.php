<?php

namespace Drupal\k1_theme_switcher;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a select theme entity type.
 */
interface SelectThemeInterface extends ContentEntityInterface, EntityOwnerInterface {

}
