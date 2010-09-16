<?php

/**
 * Helper to implementation of hook_ctools_plugin_api().
 */
function _message_example_ctools_plugin_api() {
  $args = func_get_args();
  $module = array_shift($args);
  $api = array_shift($args);
  if ($module == "message" && $api == "message") {
    return array("version" => 1);
  }
}

/**
 * Helper to implementation of hook_message_default_messages().
 */
function _message_example_message_default_messages() {
  $export = array();
  $message = new stdClass;
  $message->disabled = FALSE; /* Edit this to true to make a default message disabled initially */
  $message->api_version = 1;
  $message->name = 'create_new_content';
  $message->description = 'Create a new content - and show teaser.';
  $message->tag = '';
  $message->message = 'created <a href="@link">@title</a>. <br />!teaser';
  $message->language = 'en';

  $export['create_new_content'] = $message;
  $message = new stdClass;
  $message->disabled = FALSE; /* Edit this to true to make a default message disabled initially */
  $message->api_version = 1;
  $message->name = 'update_existing_content';
  $message->description = 'Update existing content.';
  $message->tag = '';
  $message->message = 'updated <a href="@link">@title</a>.';
  $message->language = 'en';

  $export['update_existing_content'] = $message;
  return $export;
}
