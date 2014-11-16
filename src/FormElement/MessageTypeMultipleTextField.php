<?php

/**
 * @file
 *
 * Contains Drupal\message\FormElement.
 */
namespace Drupal\message\FormElement;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
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
   * Constructing the element.
   *
   * @param MessageType $entity
   *  A message type.
   * @param $callback
   *  The name of the ajax callback.
   * @param string $langcode
   *  The language of the message. Used for the message translation form.
   */
  public function __construct(MessageType $entity, $callback, $langcode = '') {
    $this->entity = $entity;
    $this->callback = $callback;
    $this->langcode = $langcode ? $langcode : \Drupal::languageManager()->getCurrentLanguage()->getId();
  }

  /**
   * Return the message text element.
   */
  public function textField(&$form, FormStateInterface $form_state) {
    // Creating the container.
    $form['text'] = array(
      '#type' => 'container',
      '#tree' => TRUE,
      '#theme' => 'field_multiple_value_form',
      '#caridnality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
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
    $MessageText = $this->entity->getText($this->langcode, array('text' => TRUE)) ? $this->entity->getText($this->langcode, array('text' => TRUE)) : array();

    foreach ($MessageText as $text) {

      if (is_array($text)) {
        continue;
      }

      $form['text'][$start_key] = $this->singleElement($start_key, $start_key, $text);
      $start_key++;
    }

    if (!$form_state->has('message_text')) {
      $form_state->set('message_text', $start_key);
    }

    if ($form_state->has('triggering_element')) {
      $form_state->set('message_text', $form_state->get('message_text') + 1);
    }

    for ($delta = $start_key; $delta <= $form_state->get('message_text'); $delta++) {
      // For multiple fields, title and description are handled by the wrapping
      // table.
      $form['text'][$delta] = $this->singleElement($form_state->get('message_text'), $delta);
    }
  }

  /**
   * Return a single text area element.
   */
  private function singleElement($max_delta, $delta, $text = '') {
    $element = array(
      '#type' => 'text_format',
      '#base_type' => 'textarea',
      '#default_value' => $text,
      '#rows' => 1,
    );

    $element['_weight'] = array(
      '#type' => 'weight',
      '#title' => t('Weight for row @number', array('@number' => $max_delta + 1)),
      '#title_display' => 'invisible',
      // Note: this 'delta' is the FAPI #type 'weight' element's property.
      '#delta' => $max_delta,
      '#default_value' => $delta,
      '#weight' => 100,
    );

    return $element;
  }
}
