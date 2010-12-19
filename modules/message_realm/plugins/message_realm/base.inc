<?php
// $Id: message_plugin.inc,v 1.1 2010/06/07 10:54:11 amitaibu Exp $

$plugin = array(
  'realm' => FALSE,
  'title' => t('Base plugin'),
  'description' => '',
);

function message_realm_plugin_access($plugin, $message_instance, $realm_ids, $account) {
  $access = FALSE;
  if ($plugin['realm'] && $realm_ids && $accessibale_ids = call_user_func($plugin['access']['accessible ids callback'], $account)) {
    $access = array_intersect($accessibale_ids, $realm_ids);
  }
  return $access;
}

function message_realm_plugin_accessible_ids($account) {
  return array();
}


/**
 * Provide defaults for a message plugin.
 */
function message_realm_plugin_process(&$plugin, $info) {
  $plugin += array(
    'realm' => TRUE,
    'title' => $plugin['name'],
    'description' => '',

    'views' => array(
      // Optional callback for views handlers.
      'handlers callback' => function_exists('message_realm_plugin_' . $plugin['name'] . '_views_handlers') ? 'message_realm_plugin_' . $plugin['name'] . '_views_handlers' : FALSE,

      // Optional callback for views data.
      'data callback' => function_exists('message_realm_plugin_' . $plugin['name'] . '_views_data') ? 'message_realm_plugin_' . $plugin['name'] . '_views_data' : FALSE,
    ),
    'access' => array(
      'access callback' => function_exists('message_realm_plugin_' . $plugin['name'] . '_access') ? 'message_realm_plugin_' . $plugin['name'] . '_access' : 'message_realm_plugin_access',
      'accessible ids callback' => function_exists('message_realm_plugin_' . $plugin['name'] . '_accessible_ids') ? 'message_realm_plugin_' . $plugin['name'] . '_accessible_ids' : 'message_realm_plugin_accessible_ids',
    ),
  );
}
