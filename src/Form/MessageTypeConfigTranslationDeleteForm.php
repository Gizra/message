<?php

/**
 * @file
 * Contains \Drupal\message\Form\ConfigTranslationDeleteForm.
 */

namespace Drupal\message\Form;

use Drupal\config_translation\Form\ConfigTranslationDeleteForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\message\Entity\MessageType;

/**
 * Builds a form to delete configuration translation.
 */
class MessageTypeConfigTranslationDeleteForm extends ConfigTranslationDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'message_type_config_translation_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Get the message type.
    $configs = $this->mapper->getConfigData();
    $config = reset($configs);

    // Get the message object, remove the translation and update the message.
    MessageType::load($config['type'])
      ->setText(NULL, $this->language->getId())
      ->save();

    parent::submitForm($form, $form_state);
  }

}
