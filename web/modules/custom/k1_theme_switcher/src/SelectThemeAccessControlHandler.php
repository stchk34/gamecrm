<?php

namespace Drupal\k1_theme_switcher;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the select theme entity type.
 */
class SelectThemeAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        if ($entity->getOwnerId() == $account->id()) {
          return AccessResult::allowedIfHasPermission($account, 'view own select theme');
        }
        return AccessResult::allowedIfHasPermission($account, 'view select theme');

      case 'update':
        if ($entity->getOwnerId() == $account->id()) {
          return AccessResult::allowedIfHasPermission($account, 'edit own select theme');
        }
        return AccessResult::allowedIfHasPermissions(
          $account,
          ['edit select theme', 'administer select theme'],
          'OR',
        );

      case 'delete':
        return AccessResult::allowedIfHasPermissions(
          $account,
          ['delete select theme', 'administer select theme'],
          'OR',
        );

      default:
        // No opinion.
        return AccessResult::neutral();
    }

  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermissions(
      $account,
      ['create select theme', 'administer select theme'],
      'OR',
    );
  }

}
