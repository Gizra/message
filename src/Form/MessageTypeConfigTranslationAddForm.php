<?php

/**
 * @file
 * Contains \Drupal\message\Form\MessageTypeConfigTranslationAddForm.
 */

namespace Drupal\message\Form;
use Drupal\message\Controller\MessageController;
use Drupal\message\FormElement\MessageTypeMultipleTextField;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines a form for adding configuration translations.
 */
class MessageTypeConfigTranslationAddForm extends MessageTypeConfigTranslationBaseForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'message_type_config_translation_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, Request $request = NULL, $plugin_id = NULL, $langcode = NULL) {
    $form = parent::buildForm($form, $form_state, $request, $plugin_id, $langcode);

    // Get the name of the message type.
    $names = $this->mapper->getConfigNames();
    $name = reset($names);
    $translation = &$form['config_names'][$name]['text']['translation'];

    $multiple = new MessageTypeMultipleTextField(MessageController::MessageTypeLoad('example_user_register'));

    $translation = array(
      'translation' => $multiple->textField($form_state),
    );

    $form['#title'] = $this->t('Add @language translation for %label', array(
      '%label' => $this->mapper->getTitle(),
      '@language' => $this->language->name,
    ));
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);
    drupal_set_message($this->t('Successfully saved @language translation.', array('@language' => $this->language->name)));
  }
}
