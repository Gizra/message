<?php

/**
 * @file
 * Contains \Drupal\message\Form\MessageTypeConfigTranslationEditForm.
 */

namespace Drupal\message\Form;

use Symfony\Component\HttpFoundation\Request;

/**
 * Defines a form for editing message type configuration translations.
 */
class MessageTypeConfigTranslationEditForm extends MessageTypeConfigTranslationBaseForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'message_type_config_translation_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, Request $request = NULL, $plugin_id = NULL, $langcode = NULL) {
    $form = parent::buildForm($form, $form_state, $request, $plugin_id, $langcode);
    $form['#title'] = $this->t('Edit @language translation for %label', array(
      '%label' => $this->mapper->getTitle(),
      '@language' => $this->language->name,
    ));

    // todo: update the text field with the updated text.

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);
    drupal_set_message($this->t('Successfully updated @language translation.', array('@language' => $this->language->name)));
  }

}
