<?php
// $Id: message.views.inc,v 1.4 2010/06/07 10:54:11 amitaibu Exp $

/**
 * @file
 * Provides support for the Views module.
 */

/**
 * Implements hook_views_handlers().
 */
function message_realm_views_handlers() {
  $data = array();

  // Add message's core handlers.
  $handlers = array(
    'message_realm_handler_field_realm' => 'views_handler_field',
    'message_realm_handler_filter_realm' => 'views_handler_filter_in_operator',
    'message_realm_handler_argument_realm' => 'views_handler_argument',
    'message_realm_handler_filter_realm_user_access' => 'views_handler_filter_in_operator',
  );

  foreach ($handlers as $handler => $parent) {
    $data['handlers'][$handler] = array(
      'parent' => $parent,
      'path' => drupal_get_path('module', 'message') .'/includes',
    );
  }

  // Allow plugins to declare their handlers. Handlers must define their path.
  foreach (message_realm_get_plugin_message_realms() as $plugin_name => $plugin) {
    $plugin_handlers = array();
    if (!empty($plugin['views']['handlers callback'])) {
      $plugin_handlers = call_user_func($plugin['views']['handlers callback']);
    }

    $data['handlers'] = array_merge($data['handlers'], $plugin_handlers);
  }

  return $data;
}

/**
 * Implements hook_views_data().
 */
function message_realm_views_data() {
  $data = array();

  // Allow plugins to declare their handlers. Handlers must define their path.
  foreach (message_realm_get_plugin_message_realms() as $plugin_name => $plugin) {
    $plugin_data = array();
    if (!empty($plugin['views']['data callback'])) {
      $plugin_data = call_user_func($plugin['views']['data callback']);
    }
    $data = array_merge_recursive($plugin_data, $data);
  }

  return $data;
}

/**
 * Implements hook_field_views_data_alter().
 */
function message_realm_field_views_data_alter(&$data) {
  if (!empty($data['field_data_message_realm'])) {
    $data['field_data_message_realm']['message_realm_realm']['filter']['handler'] = 'message_realm_handler_filter_realm';
    $data['field_data_message_realm']['message_realm_realm']['field']['handler'] = 'message_realm_handler_field_realm';
    $data['field_data_message_realm']['message_realm_realm']['argument']['handler'] = 'message_realm_handler_argument_realm';

    // Define the message access realm here, as we want to use the almost the
    // same filter definition.
    $data['field_data_message_realm']['message_realm_user_access'] = array(
      'group' => t('Message'),
      'title' => t('Message realm access'),
      'title short' => t('Message realm access'),
      'help' => t('Realm access'),
    );
    $data['field_data_message_realm']['message_realm_user_access']['filter'] = $data['field_data_message_realm']['message_realm_realm']['filter'];
    $data['field_data_message_realm']['message_realm_user_access']['filter']['handler'] = 'message_realm_handler_filter_realm_user_access';
  }
}