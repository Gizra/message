<?php

/**
 * @file
 * Provides support for the Views module.
 */

/**
 * Implements hook_views_handlers().
 */
function message_views_handlers() {
  $data = array();

  // Add message's core handlers.
  $handlers = array(
    'message_handler_field_message_render' => 'views_handler_field',

    'message_handler_field_realm' => 'views_handler_field',
    'message_handler_filter_realm' => 'views_handler_filter_in_operator',
    'message_handler_argument_realm' => 'views_handler_argument',
    'message_handler_filter_realm_user_access' => 'views_handler_filter_in_operator',
  );

  foreach ($handlers as $handler => $parent) {
    $data['handlers'][$handler] = array(
      'parent' => $parent,
      'path' => drupal_get_path('module', 'message') .'/includes',
    );
  }

  // Allow plugins to declare their handlers. Handlers must define their path.
  foreach (message_get_plugin_messages() as $plugin_name => $plugin) {
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
function message_views_data() {
  $data = array();

  $data['message']['table']['group'] = t('Message');
  $data['message']['table']['base'] = array(
    'field' => 'mid',
    'title' => t('Message'),
    'help' => t('Messages that are related to enteties. Can be used as activity stream.'),
  );

  // Database columns.
  $data['message']['mid'] = array(
    'title' => t('Activity Unique ID'),
    'help' => t('The unique id for each activity'),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );

  $data['message']['name'] = array(
    'title' => t('Message name'),
    'help' => t('Reference to a message in the message table.'),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );

  $data['message']['entity_type'] = array(
    'title' => t('Entity type'),
    'help' => t('The type of entity being acted upon (e.g. node, user).'),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );

  $data['message']['etid'] = array(
    'title' => t('Entity ID'),
    'help' => t('Entity ID used to maintain the relationship between the message and the entity that is related to it.'),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );

  $data['message']['uid'] = array(
    'title' => t('User'),
    'help' => t('The user that is associated with the message.'),
    'relationship' => array(
      'base' => 'users',
      'handler' => 'views_handler_relationship',
      'label' => t('Message user'),
    ),
  );

  $data['message']['extra_identifier'] = array(
    'title' => t('Extra identifier'),
    'help' => t('An optional identifier that can group message instances together.'),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );

  $data['message']['timestamp'] = array(
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

  $data['message']['render'] = array(
    'title' => t('Show message'),
    'help' => t('Show a message.'),
    // This is a dummy field, so point it to a real field.
    'real field' => 'mid',

    'field' => array(
      'handler' => 'message_handler_field_message_render',
    ),
  );

  // Allow plugins to declare their handlers. Handlers must define their path.
  foreach (message_get_plugin_messages() as $plugin_name => $plugin) {
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
function message_field_views_data_alter(&$data) {
  if (!empty($data['field_data_message_realm'])) {
    $data['field_data_message_realm']['message_realm_realm']['filter']['handler'] = 'message_handler_filter_realm';
    $data['field_data_message_realm']['message_realm_realm']['field']['handler'] = 'message_handler_field_realm';
    $data['field_data_message_realm']['message_realm_realm']['argument']['handler'] = 'message_handler_argument_realm';

    // Define the message access realm here, as we want to use the almost the
    // same filter definition.
    $data['field_data_message_realm']['message_realm_user_access'] = array(
      'group' => t('Message'),
      'title' => t('Message realm access'),
      'title short' => t('Message realm access'),
      'help' => t('Realm access'),
    );
    $data['field_data_message_realm']['message_realm_user_access']['filter'] = $data['field_data_message_realm']['message_realm_realm']['filter'];
    $data['field_data_message_realm']['message_realm_user_access']['filter']['handler'] = 'message_handler_filter_realm_user_access';
  }
}