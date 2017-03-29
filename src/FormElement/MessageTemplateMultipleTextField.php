<?php

namespace Drupal\message\FormElement;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\message\Entity\MessageTemplate;

/**
 * Helper class to construct a multiple text field form.
 */
class MessageTemplateMultipleTextField {

  /**
   * The message template we handling.
   *
   * @var \Drupal\message\Entity\MessageTemplate
   */
  protected $entity;

  /**
   * The name of the ajax callback.
   *
   * @var string
   *  Each form holds the text elements in a different location. When
   *  constructing this class we need to supply the name of the callback.
   *
   * @see MessageTemplateConfigTranslationAddForm::addMoreAjax();
   */
  protected $callback;

  /**
   * Constructing the element.
   *
   * @param \Drupal\message\Entity\MessageTemplate $entity
   *   A message template.
   * @param string $callback
   *   The name of the ajax callback.
   * @param string $langcode
   *   The language of the message. Used for the message translation form.
   */
  public function __construct(MessageTemplate $entity, $callback, $langcode = '') {
    $this->entity = $entity;
    $this->callback = $callback;
    $this->langcode = $langcode ? $langcode : \Drupal::languageManager()->getCurrentLanguage()->getId();
  }

  /**
   * Return the message text element.
   */
  public function textField(&$form, FormStateInterface $form_state, $text = []) {
    // Creating the container.
    $form['text'] = [
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
    ];

    if (\Drupal::moduleHandler()->moduleExists('token')) {
      $form['token_tree'] = [
        '#theme' => 'token_tree_link',
        '#token_types' => 'all',
        '#show_restricted' => TRUE,
        '#theme_wrappers' => ['form_element'],
      ];
    }

    $form['add_more'] = [
      '#type' => 'button',
      '#value' => t('Add another item'),
      '#href' => '',
      '#add_more' => TRUE,
      '#ajax' => [
        'callback' => $this->callback,
        'wrapper' => 'message-text',
      ],
    ];

    // Building the multiple form element; Adding first the the form existing
    // text.
    $start_key = 0;
    foreach ($this->entity->get('text') as $item) {
      $form['text'][$start_key] = $this->singleElement($start_key, $item);
      $start_key++;
    }

    // Increase number of elements if requested, or none exist.
    $trigger_element = $form_state->getTriggeringElement();
    if (!empty($trigger_element['#add_more']) || !$start_key) {
      $form['text'][] = $this->singleElement($start_key + 1, ['value' => '']);
    }
  }

  /**
   * Return a single text area element.
   *
   * @param int $delta
   *   Delta for the element.
   * @param array $text
   *   Array containing 'value' and optionally 'format' for a text_format
   *   element.
   *
   * @return array
   *   A single form element.
   */
  protected function singleElement($delta, array $text) {
    $element = [
      '#type' => 'text_format',
      '#format' => isset($text['format']) ? $text['format'] : filter_default_format(),
      '#default_value' => $text['value'],
      '#rows' => 1,
    ];

    $element['_weight'] = [
      '#type' => 'weight',
      '#title' => t('Weight for row @number', ['@number' => $delta + 1]),
      '#title_display' => 'invisible',
      // Note: this 'delta' is the FAPI #type 'weight' element's property.
      '#delta' => $delta,
      '#default_value' => $delta,
    ];

    return $element;
  }

}
