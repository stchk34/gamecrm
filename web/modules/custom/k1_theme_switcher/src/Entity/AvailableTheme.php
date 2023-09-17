<?php

namespace Drupal\k1_theme_switcher\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\k1_theme_switcher\AvailableThemeInterface;
use Drupal\k1_theme_switcher\EntityThemeTrait;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the available theme entity class.
 *
 * @ContentEntityType(
 *   id = "available_theme",
 *   label = @Translation("Available theme"),
 *   label_collection = @Translation("Available themes"),
 *   label_singular = @Translation("available theme"),
 *   label_plural = @Translation("available themes"),
 *   label_count = @PluralTranslation(
 *     singular = "@count available themes",
 *     plural = "@count available themes",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\k1_theme_switcher\AvailableThemeListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\k1_theme_switcher\AvailableThemeAccessControlHandler",
 *     "storage" = "Drupal\k1_theme_switcher\AvailableThemeStorage",
 *     "form" = {
 *       "add" = "Drupal\k1_theme_switcher\Form\AvailableThemeForm",
 *       "edit" = "Drupal\k1_theme_switcher\Form\AvailableThemeForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "available_theme",
 *   admin_permission = "administer available theme",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   links = {
 *     "collection" = "/admin/structure/theme_switcher/available-theme",
 *     "add-form" = "/available-theme/add",
 *     "canonical" = "/available-theme/{available_theme}",
 *     "edit-form" = "/available-theme/{available_theme}/edit",
 *     "delete-form" = "/available-theme/{available_theme}/delete",
 *   },
 * )
 */
class AvailableTheme extends ContentEntityBase implements AvailableThemeInterface {

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
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the available theme was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
