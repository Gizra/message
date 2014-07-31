<?php

/**
 * @file
 * Contains \Drupal\message\Form\MessageTypeConfigTranslationBaseForm.
 */

namespace Drupal\message\Form;

use Drupal\config_translation\Form\ConfigTranslationFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\message\Entity\MessageType;
use Drupal\message\FormElement\MessageTypeMultipleTextField;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines a form for adding configuration translations.
 */
abstract class MessageTypeConfigTranslationBaseForm extends ConfigTranslationFormBase {
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL, $plugin_id = NULL, $langcode = NULL) {
    $form = parent::buildForm($form, $form_state, $request, $plugin_id, $langcode);

    // Get the name of the message type.
    $names = $this->mapper->getConfigNames();
    $name = reset($names);

    $form['config_names'][$name]['text'] = array(
      '#theme' => 'config_translation_manage_form_element',
      'source' => array(
        '#type' => 'item',
        '#title' => $this->t('Text'),
        '#markup' => $this->t('The message text'),
      ),
    );
    $translation = &$form['config_names'][$name]['text']['translation'];

    $configs = $form_state['config_translation_mapper']->getConfigData();
    $config = reset($configs);

    $entity = MessageType::load($config['type']);
    $form_state['#entity'] = $entity;
    $multiple = new MessageTypeMultipleTextField($entity, array(get_class($this), 'addMoreAjax'), $langcode);
    $multiple->textField($translation, $form_state);

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
