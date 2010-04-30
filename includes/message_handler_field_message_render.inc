<?php
// $Id: message_handler_field_message_render.inc,v 1.1 2010/04/02 20:01:17 amitaibu Exp $

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
  function option_definition() {
    $options = parent::option_definition();
    $options['skip_access'] = array('default' => FALSE);
    return $options;
  }

  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);

    $form['skip_access'] = array(
    '#type' => 'checkbox',
    '#title' => t('Skip access'),
    '#options' => array(FALSE => t('No'), TRUE => t('Yes')),
    '#default_value' => !empty($this->options['skip_access']),
    '#description' => t('Determine if access should be checked before showing a message. You may disable this and use filters to filter out unaccessable messages from the user.'),
    );
  }

  function render($values) {
    if (!empty($values->iid)) {
      return message_show_message($values->iid, $this->options['skip_access']);
    }
  }
}
