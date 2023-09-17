<?php

namespace Drupal\k1_theme_switcher;

use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Trait for theme field.
 */
trait EntityThemeTrait {

  /**
   * Field definition for theme field.
   */
  public static function themeBaseFieldDefinitions() {
    $fields['theme'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Theme'))
      ->setDescription(t('The available theme for user'))
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -6,
      ])
      ->setDisplayOptions('form', [
        'type' => 'select_theme_widget',
        'weight' => -6,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    return $fields;
  }

  /**
   * Get value for theme field.
   */
  public function getTheme() {
    return $this->get('theme')->value;
  }

  /**
   * Set value for theme field.
   */
  public function setTheme($theme) {
    return $this->set('theme', $theme);
  }

}
