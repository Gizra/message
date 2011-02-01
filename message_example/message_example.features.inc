<?php

/**
 * Implementation of hook_flag_default_flags().
 */
function message_example_flag_default_flags() {
  $flags = array();
  // Exported flag: "Message example flag node".
  $flags['message_example_flag_node'] = array(
    'content_type' => 'node',
    'title' => 'Message example flag node',
    'global' => '0',
    'types' => array(
      '0' => 'article',
    ),
    'flag_short' => 'Watch content',
    'flag_long' => '',
    'flag_message' => '',
    'unflag_short' => 'Unwatch content',
    'unflag_long' => '',
    'unflag_message' => '',
    'unflag_denied_text' => '',
    'link_type' => 'toggle',
    'roles' => array(
      'flag' => array(
        '0' => '2',
      ),
      'unflag' => array(
        '0' => '2',
      ),
    ),
    'show_on_page' => 1,
    'show_on_teaser' => 1,
    'show_on_form' => 0,
    'access_author' => '',
    'i18n' => 0,
    'api_version' => 2,
    'module' => 'message_example',
    'locked' => array(
      '0' => 'name',
    ),
  );
  // Exported flag: "Message example flag user".
  $flags['message_example_flag_user'] = array(
    'content_type' => 'user',
    'title' => 'Message example flag user',
    'global' => '0',
    'types' => array(),
    'flag_short' => 'Watch user',
    'flag_long' => '',
    'flag_message' => '',
    'unflag_short' => 'Unwatch user',
    'unflag_long' => '',
    'unflag_message' => '',
    'unflag_denied_text' => '',
    'link_type' => 'toggle',
    'roles' => array(
      'flag' => array(
        '0' => '2',
      ),
      'unflag' => array(
        '0' => '2',
      ),
    ),
    'show_on_profile' => 1,
    'access_uid' => '',
    'api_version' => 2,
    'module' => 'message_example',
    'locked' => array(
      '0' => 'name',
    ),
  );
  return $flags;
}

/**
 * Implementation of hook_views_api().
 */
function message_example_views_api() {
  return array(
    'api' => '3.0-alpha1',
  );
}
