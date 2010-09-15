<?php
// $Id$

if (module_exists('flag')) {
  // Message plugin deceleration. We don't declare a "realm" as we just want
  // this plugin to add a views handler.
  $plugin = array(
    'title' => t('Flag realm'),
    'description' => t("Determine access by a flaggings of a user."),
    'views' => array(
      'handlers' => array(
        'message_handler_filter_realm_user_access_flag' => array(
          'parent' => 'views_handler_filter_in_operator',
          'path' => drupal_get_path('module', 'message') .'/includes',
        ),
      ),
    ),
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