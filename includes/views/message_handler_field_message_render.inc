<?php

/**
 * @file
 * Contains the message field handler.
 */

/**
 * Views field handler for rendering a message.
 *
 * @ingroup views
 */
class message_handler_field_message_render extends views_handler_field {

  /**
   * Set default field name to render.
   */
  function option_definition() {
    $options = parent::option_definition();
    $options['field_name'] = array('default' => MESSAGE_FIELD_MESSAGE_TEXT);
    return $options;
  }

  /**
   * Provide form to select a field name to render.
   */
  function options_form(&$form, &$form_state) {
    $options = array();
    foreach (field_info_instances('message_type') as $bundle => $instances) {
      foreach ($instances as $field_name => $instance) {
        if (!empty($options[$field_name])) {
          continue;
        }
        $field = field_info_field($field_name);
        if (!in_array($field['type'], array('text_long', 'text'))) {
          continue;
        }
        $options[$field_name] = $instance['label'];
      }
    }
    // Get all the text fields attached to a message-type.
    $form['field_name'] = array(
      '#type' => 'select',
      '#title' => t('Field name'),
      '#description' => t('Select the field name to render.'),
      '#options' => $options,
      '#default_value' => $this->options['field_name'],
      '#required' => TRUE,
    );
  }

  function render($values) {
    if (!empty($values->mid) && $message = message_load($values->mid)) {
      $options = array('field name' => $this->options['field_name']);
      return $message->getText(NULL, $options);
    }
  }
}
