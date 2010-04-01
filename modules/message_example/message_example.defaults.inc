<?php

/**
 * Helper to implementation of hook_ctools_plugin_api().
 */
function _message_example_ctools_plugin_api() {
  $args = func_get_args();
  $module = array_shift($args);
  $api = array_shift($args);
  if ($module == "message" && $api == "message") {
    return array("version" => 3);
  }
}

/**
 * Helper to implementation of hook_message_default_messages().
 */
function _message_example_message_default_messages() {
  $export = array();
  $message = new stdClass;
  $message->disabled = FALSE; /* Edit this to true to make a default message disabled initially */
  $message->api_version = 3;
  $message->name = 'create_new_content';
  $message->description = 'Create a new content.';
  $message->tag = '';
  $message->message = '@node created.';
  $message->language = 'en';

  $translatables['create'] = array(
  );

  $export['create'] = $message;
  return $export;
}

/**
 * Helper to implementation of hook_rules_defaults().
 */
function _message_example_rules_defaults() {
  return array(
  );
}
