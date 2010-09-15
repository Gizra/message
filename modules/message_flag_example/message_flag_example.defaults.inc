<?php

/**
 * Helper to implementation of hook_ctools_plugin_api().
 */
function _message_flag_example_ctools_plugin_api() {
  $args = func_get_args();
  $module = array_shift($args);
  $api = array_shift($args);
  if ($module == "message" && $api == "message") {
    return array("version" => 1);
  }
}

/**
 * Helper to implementation of hook_flag_default_flags().
 */
function _message_flag_example_flag_default_flags() {
$flags = array();
// Exported flag: "Message follow_user".
$flags[] = array (
  'content_type' => 'user',
  'name' => 'message_flag_example_user',
  'title' => 'Message follow_user',
  'global' => '0',
  'types' => 
  array (
  ),
  'flag_short' => 'Follow user',
  'flag_long' => '',
  'flag_message' => '',
  'unflag_short' => 'Unfollow user',
  'unflag_long' => '',
  'unflag_message' => '',
  'unflag_denied_text' => '',
  'link_type' => 'toggle',
  'roles' => 
  array (
    'flag' => 
    array (
      0 => 2,
    ),
    'unflag' => 
    array (
      0 => 2,
    ),
  ),
  'show_on_profile' => 1,
  'access_uid' => 'others',
  'module' => 'message_flag_example',
  'locked' => 
  array (
    0 => 'name',
  ),
  'api_version' => 2,
);
return $flags;
}

/**
 * Helper to implementation of hook_message_default_messages().
 */
function _message_flag_example_message_default_messages() {
  $export = array();
  $message = new stdClass;
  $message->disabled = FALSE; /* Edit this to true to make a default message disabled initially */
  $message->api_version = 1;
  $message->name = 'message_flag_example_user';
  $message->description = '';
  $message->tag = '';
  $message->message = 'and !friend are now friends.';
  $message->language = '';

  $export['message_flag_example_user'] = $message;
  return $export;
}
