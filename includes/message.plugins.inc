<?php
// $Id: context.plugins.inc,v 1.1.2.4 2010/02/09 17:05:22 yhahn Exp $

/**
 * Context plugins.
 */
function _message_message_plugins() {
  $plugins = array();

  $plugins['message_plugin'] = array(
    'handler' => array(
      'path' => drupal_get_path('module', 'message') .'/plugins',
      'file' => 'message_plugin.inc',
      'class' => 'message_plugin',
    ),
    // Add message information about the realm, or FALSE if it is the base plugin.
    'realm' => FALSE,
    'title' => t('Base plugin'),
    'description' => '',
  );

  $plugins['message_plugin_role'] = array(
    'handler' => array(
      'path' => drupal_get_path('module', 'message') .'/plugins',
      'file' => 'message_plugin_role.inc',
      'class' => 'message_plugin_role',
      'parent' => 'message_plugin',
    ),
    'realm' => 'role',
    'title' => t('Basic role realm'),
    'description' => t("Determine access by a user's role."),
  );

  return $plugins;
}
