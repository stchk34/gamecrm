<?php

namespace Drupal\k1_theme_switcher\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the available theme entity edit forms.
 */
class AvailableThemeForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);

    $entity = $this->getEntity();

    $message_arguments = ['%label' => $entity->toLink()->toString()];
    $logger_arguments = [
      '%label' => $entity->label(),
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()
          ->addStatus($this->t('New available theme %label has been created.', $message_arguments));
        $this->logger('k1_theme_switcher')
          ->notice('Created new available theme %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()
          ->addStatus($this->t('The available theme %label has been updated.', $message_arguments));
        $this->logger('k1_theme_switcher')
          ->notice('Updated available theme %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.available_theme.canonical', ['available_theme' => $entity->id()]);

    return $result;
  }

}
