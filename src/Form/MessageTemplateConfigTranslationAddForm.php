<?php

namespace Drupal\message\Form;

/**
 * Defines a form for adding configuration translations.
 */
class MessageTemplateConfigTranslationAddForm extends MessageTemplateConfigTranslationBaseForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'message_template_config_translation_add_form';
  }

}
