<?php

/**
 * Implementation of hook_ctools_plugin_api().
 */
function message_example_ctools_plugin_api() {
  list($module, $api) = func_get_args();
  if ($module == "message" && $api == "message") {
    return array("version" => 1);
  }
}

/**
 * Implementation of hook_message_default_messages().
 */
function message_example_message_default_messages() {
  $export = array();
  $message = new stdClass;
  $message->disabled = FALSE; /* Edit this to true to make a default message disabled initially */
  $message->api_version = 1;
  $message->name = 'node_insert';
  $message->description = 'Create a new content - and show teaser.';
  $message->tag = '';
  $message->message = 'created <a href="@link">@title</a>. <br />!teaser';
  $message->language = 'en';

  $export['node_insert'] = $message;

  $message = new stdClass;
  $message->disabled = FALSE; /* Edit this to true to make a default message disabled initially */
  $message->api_version = 1;
  $message->name = 'node_update';
  $message->description = 'Update existing content.';
  $message->tag = '';
  $message->message = 'updated <a href="@link">@title</a>.';
  $message->language = 'en';

  $export['node_update'] = $message;

  $message = new stdClass;
  $message->disabled = FALSE; /* Edit this to true to make a default message disabled initially */
  $message->api_version = 1;
  $message->name = 'comment_insert';
  $message->description = 'Create a new comment';
  $message->tag = '';
  $message->message = 'commented "<a href="@link">@title</a>" about @node-title.';
  $message->language = 'en';

  $export['comment_insert'] = $message;
  return $export;
}
