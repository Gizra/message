<?php
// $Id: message_plugin_og.inc,v 1.1 2010/06/07 10:54:11 amitaibu Exp $

if (module_exists('flag')) {
  // Message plugin deceleration.
  $plugin = array(
    // We don't declare a "realm" as we just want this plugin to add a views
    // handler.
    'realm' => FALSE,
    'title' => t('Flag realm'),
    'description' => t("Determine access by a flaggings of a user."),
  );
}

/**
* Get all the flaggings a user did in a certain flag.
*/
function message_plugin_flag_accessible_ids($account, $flag_name) {
  $access = array();
  if ($flag = flag_get_flag($flag_name)) {
    // Get all the flaggings of the user in the flag.
    if (($flaggings = flag_get_user_flags($flag->content_type, NULL, $account->uid)) && !empty($flaggings[$flag_name])) {
      $access = array_keys($flaggings[$flag_name]);
    }
  }
  return $access;
}

/**
 * Plugin delegated implementation of hook_views_handlers().
 */
function message_plugin_flag_views_handlers() {
  return array(
    'message_handler_filter_realm_user_access_flag' => array(
      'parent' => 'views_handler_filter_in_operator',
    ),
  );
}

/**
 * Plugin delegated implementation of hook_views_data().
 */
function message_plugin_flag_views_data() {
  $data = array();

  $data['message']['message_realm_flag'] = array(
    'title' => t('Accessible realms by flag'),
    'help' => t('The realms a user may access based on their flaggings.'),

    // This is a dummy field, so point it to a real field.
    'real field' => 'mid',

    'filter' => array(
      'handler' => 'message_handler_filter_realm_user_access_flag',
    ),
  );
  return $data;
}