<?php

/**
 * @file
 * Contains \Drupal\message\Form\MessageTypeConfigTranslationAddForm.
 */

namespace Drupal\message\Form;
use Drupal\message\Entity\MessageType;
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
  public function submitForm(array &$form, array &$form_state) {
    parent::submitForm($form, $form_state);
    /** @var MessageType $entity */
    $entity = $form_state['#entity'];
    $texts = $form_state['values']['config_names']['message.type.' . $entity->type]['text']['translation']['text'];

    // todo: Handle weight order.
    $message_text = array();
    foreach ($texts as $text) {
      if (empty($text['value'])) {
        continue;
      }
      $message_text[] = $text['value'];
    }

    $entity->text[$form_state['config_translation_language']->id] = $message_text;
    $entity->save();
    drupal_set_message($this->t('Successfully saved @language translation.', array('@language' => $this->language->name)));
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, Request $request = NULL, $plugin_id = NULL, $langcode = NULL) {
    $form = parent::buildForm($form, $form_state, $request, $plugin_id, $langcode);
    $form['#title'] = $this->t('Add @language translation for %label', array(
      '%label' => $this->mapper->getTitle(),
      '@language' => $this->language->name,
    ));

    // todo: update the text field with the updated text.
    return $form;
  }

  /**
   * Ajax callback for the "Add another item" button.
   *
   * This returns the new page content to replace the page content made obsolete
   * by the form submission.
   */
  public static function addMoreAjax(array $form, array $form_state) {
    $configs = $form_state['config_translation_mapper']->getConfigData();
    $config = reset($configs);
    return $form['config_names']['message.type.' . $config['type']]['text']['translation']['text'];
  }
}
