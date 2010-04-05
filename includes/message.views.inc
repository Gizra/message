<?php
// $Id: message.views.inc,v 1.1 2010/04/02 20:01:17 amitaibu Exp $

/**
 * @file
 * Provides support for the Views module.
 */

/**
 * Implementation of hook_views_handlers().
 */
function message_views_handlers() {
  $data = array();

  // Add message's core handlers.
  $data['handlers']['message_handler_field_message_render'] = array(
    'parent' => 'views_handler_field',
    'path' => drupal_get_path('module', 'message') .'/includes',
  );

  // Allow plugins to declare their handlers. Handlers must define their path.
  ctools_include('plugins');
  $plugins = ctools_get_plugins('message', 'plugins');
  foreach ($plugins as $name => $value) {
    $plugin = message_get_plugin($name);
    if ($plugin_handler = $plugin->get_views_handlers()) {
      $data['handlers'] = array_merge($data['handlers'], $plugin_handler);
    }
  }

  return $data;
}

/**
 * Implementation of hook_views_data().
 */
function message_views_data() {
  $data = array();

  $data['message_realm']['table']['group'] = t('Message realm');

  $data['message_instance']['table']['group'] = t('Message');
  $data['message_instance']['table']['base'] = array(
    'field' => 'iid',
    'title' => t('Message'),
    'help' => t('Messages that are related to enteties. Can be used as activity stream.'),
  );

  // Database columns
  $data['message_instance']['iid'] = array(
    'title' => t('Activity Unique ID'),
    'help' => t('The unique id for each activity'),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );

  $data['message_instance']['uid'] = array(
    'title' => t('User'),
    'help' => t('The user that is associated with the message.'),
    'relationship' => array(
      'base' => 'users',
      'handler' => 'views_handler_relationship',
      'label' => t('Message user'),
    ),
  );

  $data['message_instance']['iid'] = array(
    'title' => t('Message realm'),
    'help' => t('Relate a message to the message realm.'),
    'relationship' => array(
      'handler' => 'views_handler_relationship',
      'base' => 'message_realm',
      'base field' => 'iid',
      'label' => t('Message realm'),
    ),
  );

  $data['message_instance']['timestamp'] = array(
    'title' => t('Creation date'),
    'help' => t('The time that the message was created.'),
    'field' => array(
      'handler' => 'views_handler_field_date',
      'click sortable' => TRUE,
    ),
    'sort' => array(
      'handler' => 'views_handler_sort_date',
    ),
  );

  $data['message_instance']['render'] = array(
    'title' => t('Show message'),
    'help' => t('Show a message.'),
    // This is a dummy field, so point it to a real field.
    'real field' => 'iid',

    'field' => array(
      'handler' => 'message_handler_field_message_render',
    ),
  );

  // Allow plugins to declare their handlers. Handlers must define their path.
  ctools_include('plugins');
  $plugins = ctools_get_plugins('message', 'plugins');
  foreach ($plugins as $name => $value) {
    $plugin = message_get_plugin($name);
    if ($plugin_data = $plugin->get_views_data()) {
      $data = array_merge_recursive($plugin_data, $data);
    }
  }

  return $data;
}