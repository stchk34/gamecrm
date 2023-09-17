<?php

namespace Drupal\k1_theme_switcher\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the select theme entity edit forms.
 */
class SelectThemeForm extends ContentEntityForm {

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
          ->addStatus($this->t('New select theme %label has been created.', $message_arguments));
        $this->logger('k1_theme_switcher')
          ->notice('Created new select theme %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()
          ->addStatus($this->t('The select theme %label has been updated.', $message_arguments));
        $this->logger('k1_theme_switcher')
          ->notice('Updated select theme %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.select_theme.canonical', ['select_theme' => $entity->id()]);

    return $result;
  }

}
