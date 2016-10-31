<?php

namespace Drupal\message\Form;

use Drupal\config_translation\Form\ConfigTranslationDeleteForm;

/**
 * Builds a form to delete configuration translation.
 */
class MessageTemplateConfigTranslationDeleteForm extends ConfigTranslationDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'message_template_config_translation_delete_form';
  }

}
