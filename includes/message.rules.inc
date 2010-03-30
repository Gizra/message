<?php
// $Id: taxonomy.rules.inc,v 1.1.2.12 2009/08/05 17:20:41 fago Exp $


/**
 * @file
 * Rules integration for the Message module.
 */


/**
 * Implementation of hook_rules_data_type_info().
 */
function message_rules_data_type_info() {
  return array(
    'message' => array(
      'label' => t('message'),
      'class' => 'message_data_type_message',
      'savable' => FALSE,
      'identifiable' => TRUE,
      'module' => 'Message',
      'token type' => FALSE,
    ),
  );
}

/**
 * Defines the message data type.
 */
class message_data_type_message extends rules_data_type {
  function load($name) {
    return message_load($name);
  }

  function get_identifier() {
    $message = &$this->get();
    return $message->name;
  }
}


/**
 * Implementation of hook_rules_action_info().
 */
function message_rules_action_info() {
  $info = array();
  // Term actions.
  $info['message_action_load_message'] = array(
    'label' => t('Load a message'),
    'new variables' => array(
      'message' => array(
        'type' => 'message',
        'label' => t('Message'),
      ),
    ),
    'eval input' => array('message|message_text'),
    'help' => t('Loading a message will allow you to act on this term, for example you will be create a new message instance.'),
    'module' => 'Message',
  );

  $info['message_action_create_message_instance'] = array(
    'label' => t('Create a message instance'),
    'arguments' => array(
      'message' => array(
        'type' => 'message',
        'label' => t('Message'),
      ),
      'user' => array(
        'type' => 'user',
        'label' => t('User'),
      ),
    ),
    'eval input' => array('arguments', 'entity_type', 'eid', 'extra_identifier'),
    'module' => 'Message',
  );

  return $info;
}

/**
 * Action: Load a term.
 */
function message_action_load_message($settings) {
  if ($message = message_load(!empty($settings['message']['message_text']) ? $settings['message']['message_text'] : $settings['message']['message_select'])) {
    return array('message' => $message);
  }
}

/**
 * Action: Create a message instance.
 */
function message_action_create_message_instance($message, $account, $settings) {
  // Prepare the values to pass to message_instance_save();
  $message_instance = new stdClass();
  $args = array();

  // TODO: Add form validation on arguments.
  if (!empty($settings['message']['arguments'])) {
    $list = explode("\n", $settings['message']['arguments']);
    $list = array_map('trim', $list);

      foreach ($list as $opt) {
      if (strpos($opt, '|') !== FALSE) {
        list($key, $value) = explode('|', $opt);
        $message_instance->$args[$key] = check_plain($value);
      }
    }
  }

  // Populate other variables.
  unset($settings['message']['arguments']);
  foreach ($settings['message'] as $key => $value) {
    if (!empty($value)) {
      $message_instance->{$key} = $value;
    }
  }
  return array('message_instance' => $message_instance);
}