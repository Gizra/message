<?php

/**
 * @file
 * Contains \Drupal\node\NodeTypeForm.
 */

namespace Drupal\message;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\field\Entity\FieldInstanceConfig;

/**
 * Form controller for node type forms.
 */
class MessageTypeForm extends EntityForm {

  private $maxDelta;

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $form = parent::form($form, $form_state);

    $type = $this->entity;

    $form['label'] = array(
      '#title' => t('Label'),
      '#type' => 'textfield',
      '#default_value' => $type->label(),
      '#description' => t('The human-readable name of this message type. This text will be displayed as part of the list on the <em>Add message</em> page. It is recommended that this name begin with a capital letter and contain only letters, numbers, and spaces. This name must be unique.'),
      '#required' => TRUE,
      '#size' => 30,
    );

    $form['type'] = array(
      '#type' => 'machine_name',
      '#default_value' => $type->id(),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#disabled' => $type->isLocked(),
      '#machine_name' => array(
        'exists' => '\Drupal\message\Controller\MessageController::MessageTypeLoad',
        'source' => array('label'),
      ),
      '#description' => t('A unique machine-readable name for this message type. It must only contain lowercase letters, numbers, and underscores. This name will be used for constructing the URL of the %message-add page, in which underscores will be converted into hyphens.', array(
        '%message-add' => t('Add message'),
      )),
    );

    $form['description'] = array(
      '#title' => t('Description'),
      '#type' => 'textfield',
      '#default_value' => $this->entity->description,
      '#description' => t('The human-readable description of this message type.'),
      '#required' => TRUE,
    );

    // todo: check content translation.
    $form['language'] = array(
      '#title' => t('Field language'),
      '#description' => t('The language code that will be saved with the field values. This is used to allow translation of fields.'),
    );

    $this->textField($form, $form_state);

    $form['data'] = array(
      // Placeholder for other module to add their settings, that should be added
      // to the data column.
      '#tree' => TRUE,
    );


    $form['data']['token options']['clear'] = array(
      '#title' => t('Clear empty tokens'),
      '#type' => 'checkbox',
      '#description' => t('When this option is selected, empty tokens will be removed from display.'),
      '#default_value' => isset($this->entity->data['token options']['clear']) ? $this->entity->data['token options']['clear'] : FALSE,
    );

    $form['data']['purge'] = array(
      '#type' => 'fieldset',
      '#title' => t('Purge settings'),
    );

    $form['data']['purge']['override'] = array(
      '#title' => t('Override global settings'),
      '#type' => 'checkbox',
      '#description' => t('Override global purge settings for messages of this type.'),
      '#default_value' => !empty($this->entity->data['purge']['override']),
    );

    $states = array(
      'visible' => array(
        ':input[name="data[purge][override]"]' => array('checked' => TRUE),
      ),
    );

    $form['data']['purge']['enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Purge messages'),
      '#description' => t('When enabled, old messages will be deleted.'),
      '#states' => $states,
      '#default_value' => !empty($this->entity->data['purge']['enabled']) ? TRUE : FALSE,
    );

    $states = array(
      'visible' => array(
        ':input[name="data[purge][enabled]"]' => array('checked' => TRUE),
      ),
    );

    $form['data']['purge']['quota'] = array(
      '#type' => 'textfield',
      '#title' => t('Messages quota'),
      '#description' => t('Maximal (approximate) amount of messages of this type.'),
      '#default_value' => !empty($this->entity->data['purge']['quota']) ? $this->entity->data['purge']['quota'] : '',
      '#states' => $states,
    );

    $form['data']['purge']['days'] = array(
      '#type' => 'textfield',
      '#title' => t('Purge messages older than'),
      '#description' => t('Maximal message age in days, for messages of this type.'),
      '#default_value' => !empty($this->entity->data['purge']['days']) ? $this->entity->data['purge']['days'] : '',
      '#states' => $states,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, array &$form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = t('Save message type');
    $actions['delete']['#value'] = t('Delete message type');
    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, array &$form_state) {
    parent::validate($form, $form_state);
  }

  /**
   * Return the message text element.
   *
   * todo: add token selector, add ckeditor and convert to multiple field.
   */
  private function textField(&$form, &$form_state) {
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
        'callback' => array(get_class($this), 'addMoreAjax'),
        'wrapper' => 'message-text',
      ),
    );

    // Building the multiple form element; Adding first the the form existing
    // text.
    $start_key = 0;
    foreach ($this->entity->text as $text) {
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

  /**
   * Ajax callback for the "Add another item" button.
   *
   * This returns the new page content to replace the page content made obsolete
   * by the form submission.
   */
  public static function addMoreAjax(array $form, array $form_state) {
    return $form['text'];
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    // Until the parent method will do something we handle the saving by our
    // self.
    parent::save($form, $form_state);

    // Saving the message text values.
    $message_text = array();

    // todo: Handle weight order.
    foreach ($form_state['values']['text'] as $key => $text) {
      if (empty($text['value'])) {
        continue;
      }
      $message_text[] = $text['value'];
    }

    // Updating the message text.
    $this->entity->text = $message_text;

    // todo: When the parent method will do something remove as much code as we
    // can.
    $this->entity->save();

    $params = array(
      '@type' => $form_state['values']['label'],
    );

    drupal_set_message(t('The message type @type created successfully.', $params));

    $form_state['redirect'] = 'admin/structure/message';
  }
}
