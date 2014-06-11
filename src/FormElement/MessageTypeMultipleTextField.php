<?php

/**
 * @file
 *
 * Contains Drupal\message\FormElement.
 */
namespace Drupal\message\FormElement;

use Drupal\field\Entity\FieldInstanceConfig;
use Drupal\message\Entity\MessageType;

class MessageTypeMultipleTextField {

  /**
   * The message type we handling.
   *
   * @var \Drupal\message\Entity\MessageType
   */
  protected $entity;

  /**
   * The name of the ajax callback.
   *
   * @var String
   *  Each form holds the text elements in a different location. When
   *  constructing this class we need to supply the name of the callback.
   *
   * @see MessageTypeConfigTranslationAddForm::addMoreAjax();
   */
  protected $callback;

  /**
   * The max delta of elements.
   * @var Integer.
   */
  protected $maxDelta;

  /**
   * Constructing the element.
   *
   * @param MessageType $entity
   *  A message type.
   * @param $callback
   *  The name of the ajax callback.
   */
  public function __construct(MessageType $entity, $callback, $langcode = '') {
    $this->entity = $entity;
    $this->callback = $callback;
    $this->langcode = $langcode ? $langcode : \Drupal::languageManager()->getCurrentLanguage()->getId();
  }

  /**
   * Return the message text element.
   *
   * todo: add token selector.
   */
  public function textField(&$form, &$form_state) {
    // Creating the container.
    $form['text'] = array(
      '#type' => 'container',
      '#tree' => TRUE,
      '#theme' => 'field_multiple_value_form',
      '#caridnality' => FieldInstanceConfig::CARDINALITY_UNLIMITED,
      '#cardinality_multiple' => TRUE,
      '#field_name' => 'message_text',
      '#title' => t('Message text'),
      '#description' => t('Please enter the message text.'),
      '#prefix' => '<div id="message-text">',
      '#suffix' => '</div>',
    );

    $form['add_more'] = array(
      '#type' => 'button',
      '#value' => t('Add another item'),
      '#href' => '',
      '#ajax' => array(
        'callback' => $this->callback,
        'wrapper' => 'message-text',
      ),
    );

    // Building the multiple form element; Adding first the the form existing
    // text.
    $start_key = 0;
    $MessageText = !empty($this->entity->text[$this->langcode]) ? $this->entity->text[$this->langcode] : array();
    foreach ($MessageText as $text) {
      $form['text'][$start_key] = $this->singleElement($start_key, $text);
      $start_key++;
    }

    $form_state['storage']['message_text'] = isset($form_state['storage']['message_text']) ? $form_state['storage']['message_text'] : $start_key;

    if (!empty($form_state['triggering_element'])) {
      $form_state['storage']['message_text']++;
    }

    $this->maxDelta = $start_key;

    for ($delta = $start_key; $delta <= $form_state['storage']['message_text']; $delta++) {
      // For multiple fields, title and description are handled by the wrapping
      // table.
      $form['text'][$delta] = $this->singleElement($delta);
    }
  }

  /**
   * Return a single text area element.
   */
  private function singleElement($delta, $text = '') {
    $element = array(
      '#type' => 'text_format',
      '#base_type' => 'textarea',
      '#default_value' => $text,
      '#rows' => 2,
    );

    $element['_weight'] = array(
      '#type' => 'weight',
      '#title' => t('Weight for row @number', array('@number' => $this->maxDelta + 1)),
      '#title_display' => 'invisible',
      // Note: this 'delta' is the FAPI #type 'weight' element's property.
      '#delta' => $this->maxDelta,
      '#default_value' => $delta,
      '#weight' => 100,
    );

    return $element;
  }
}
