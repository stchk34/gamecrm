<?php

namespace Drupal\k1_theme_switcher\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\k1_theme_switcher\EntityThemeTrait;
use Drupal\k1_theme_switcher\SelectThemeInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the select theme entity class.
 *
 * @ContentEntityType(
 *   id = "select_theme",
 *   label = @Translation("Select Theme"),
 *   label_collection = @Translation("Select Themes"),
 *   label_singular = @Translation("select theme"),
 *   label_plural = @Translation("select themes"),
 *   label_count = @PluralTranslation(
 *     singular = "@count select themes",
 *     plural = "@count select themes",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\k1_theme_switcher\SelectThemeListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\k1_theme_switcher\SelectThemeAccessControlHandler",
 *     "storage" = "Drupal\k1_theme_switcher\SelectThemeStorage",
 *     "form" = {
 *       "default" = "Drupal\k1_theme_switcher\Form\SelectThemeForm",
 *       "add" = "Drupal\k1_theme_switcher\Form\SelectThemeForm",
 *       "edit" = "Drupal\k1_theme_switcher\Form\SelectThemeForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "select_theme",
 *   admin_permission = "administer select theme",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   links = {
 *     "collection" = "/admin/structure/theme_switcher/select-theme",
 *     "add-form" = "/select-theme/add",
 *     "canonical" = "/select-theme/{select_theme}",
 *     "edit-form" = "/select-theme/{select_theme}/edit",
 *     "delete-form" = "/select-theme/{select_theme}/delete",
 *   },
 * )
 */
class SelectTheme extends ContentEntityBase implements SelectThemeInterface {

  use EntityOwnerTrait;
  use EntityThemeTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::themeBaseFieldDefinitions();

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User'))
      ->setSetting('target_type', 'user')
      ->setRequired(TRUE)
      ->setDefaultValueCallback(static::class . '::getDefaultEntityOwner')
      ->addConstraint('UniqueField')
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
