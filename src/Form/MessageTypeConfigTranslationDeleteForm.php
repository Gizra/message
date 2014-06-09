<?php

/**
 * @file
 * Contains \Drupal\config_translation\Form\ConfigTranslationDeleteForm.
 */

namespace Drupal\message\Form;
use Drupal\config_translation\Form\ConfigTranslationDeleteForm;
use Drupal\message\Controller\MessageController;


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
  public function submitForm(array &$form, array &$form_state) {

    // Get the message type.
    $configs = $this->mapper->getConfigData();
    $config = reset($configs);

    // Get the message object, remove the translation and update the message.
    $message_type = MessageController::MessageTypeLoad($config['type']);
    unset($message_type->text[$this->language->getId()]);
    $message_type->save();

    parent::submitForm($form, $form_state);
  }

}
