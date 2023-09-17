<?php

namespace Drupal\k1_theme_switcher;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the available theme entity type.
 */
class AvailableThemeAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        if ($entity->getOwnerId() == $account->id()) {
          return AccessResult::allowedIfHasPermission($account, 'view own available themes');
        }
        return AccessResult::allowedIfHasPermission($account, 'view available theme');

      case 'update':
        return AccessResult::allowedIfHasPermissions(
          $account,
          ['edit available theme', 'administer available theme'],
          'OR',
        );

      case 'delete':
        return AccessResult::allowedIfHasPermissions(
          $account,
          ['delete available theme', 'administer available theme'],
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
      ['create available theme', 'administer available theme'],
      'OR',
    );
  }

}
