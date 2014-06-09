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
    $names = $this->mapper->getConfigData();
    $name = reset($names);
    $type = MessageController::MessageTypeLoad($name['type']);
    unset($type->text[$this->language->getId()]);
    $type->save();
    parent::submitForm($form, $form_state);
  }

}
