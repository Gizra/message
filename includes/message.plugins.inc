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

  $plugins['message_plugin_user'] = array(
    'handler' => array(
      'path' => drupal_get_path('module', 'message') .'/plugins',
      'file' => 'message_plugin_user.inc',
      'class' => 'message_plugin_user',
      'parent' => 'message_plugin',
    ),
    'realm' => 'user',
    'title' => t('USer realm'),
    'description' => t("Determine access by a user's ID."),
  );

  $plugins['message_plugin_role'] = array(
    'handler' => array(
      'path' => drupal_get_path('module', 'message') .'/plugins',
      'file' => 'message_plugin_role.inc',
      'class' => 'message_plugin_role',
      'parent' => 'message_plugin',
    ),
    'realm' => 'role',
    'title' => t('Role realm'),
    'description' => t("Determine access by a user's role."),
  );

  // TODO: Move to contrib folder?
  if (module_exists('og')) {
    $plugins['message_plugin_og'] = array(
      'handler' => array(
        'path' => drupal_get_path('module', 'message') .'/plugins',
        'file' => 'message_plugin_og.inc',
        'class' => 'message_plugin_og',
        'parent' => 'message_plugin',
      ),
      'realm' => 'og',
      'title' => t('Organic groups realm'),
      'description' => t("Determine access by a user's membership in a group."),
    );

    $plugins['message_plugin_og_admin'] = array(
      'handler' => array(
        'path' => drupal_get_path('module', 'message') .'/plugins',
        'file' => 'message_plugin_og_admin.inc',
        'class' => 'message_plugin_og_admin',
        'parent' => 'message_plugin',
      ),
      'realm' => 'og_admin',
      'title' => t('Organic groups admin realm'),
      'description' => t("Determine access by a user's membership and administrator role in a group."),
    );
  }

  return $plugins;
}
