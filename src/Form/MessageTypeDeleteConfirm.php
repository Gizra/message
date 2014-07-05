<?php

/**
 * @file
 * Contains \Drupal\message\Form\MessageTypeDeleteConfirm.
 */

namespace Drupal\message\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Url;

/**
 * Provides a form for message type deletion.
 */
class MessageTypeDeleteConfirm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to delete the message type %type?', array('%type' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return new Url('message.overview_types');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, array &$form_state) {
    $this->entity->delete();
    $t_args = array('%name' => $this->entity->label());
    drupal_set_message(t('The message type %name has been deleted.', $t_args));
    watchdog('message', 'Deleted message type %name.', $t_args, WATCHDOG_NOTICE);

    $form_state['redirect_route'] = $this->getCancelRoute();
  }
}
