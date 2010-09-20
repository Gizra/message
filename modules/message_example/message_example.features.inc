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

  $message = new stdClass;
  $message->disabled = FALSE; /* Edit this to true to make a default message disabled initially */
  $message->api_version = 1;
  $message->name = 'message_example_flag_user';
  $message->description = '';
  $message->tag = '';
  $message->message = 'and !friend are now friends.';
  $message->language = '';

  $export['message_example_flag_user'] = $message;

  $message = new stdClass;
  $message->disabled = FALSE; /* Edit this to true to make a default message disabled initially */
  $message->api_version = 1;
  $message->name = 'message_example_flag_node';
  $message->description = '';
  $message->tag = '';
  $message->message = 'is following <a href="@link">@title</a>.';
  $message->language = '';

  $export['message_example_flag_node'] = $message;

  return $export;
}

/**
 * Implementation of hook_flag_default_flags().
 */
function message_example_flag_default_flags() {
$flags = array();
// Exported flag: "Message follow user".
$flags[] = array (
  'content_type' => 'user',
  'name' => 'message_example_flag_user',
  'title' => 'Message follow user',
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
  'module' => 'message_example',
  'locked' =>
  array (
    0 => 'name',
  ),
  'api_version' => 2,
);
return $flags;
}
