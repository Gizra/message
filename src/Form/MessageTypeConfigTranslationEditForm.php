<?php

/**
 * @file
 * Contains \Drupal\message\Form\MessageTypeConfigTranslationEditForm.
 */

namespace Drupal\message\Form;

use Drupal\message\Entity\MessageType;
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

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);

    /** @var MessageType $entity */
    $entity = $form_state['#entity'];
    $texts = $form_state['values']['config_names']['message.type.' . $entity->gettype()]['text']['translation']['text'];

    // todo: Handle weight order.
    $message_text = array();
    foreach ($texts as $text) {
      if (empty($text['value'])) {
        continue;
      }

      $message_text[] = $text['value'];
    }

    $entity
      ->setText($message_text, $form_state['config_translation_language']->id)
      ->save();

    drupal_set_message($this->t('Successfully updated @language translation.', array('@language' => $this->language->name)));
  }
}
