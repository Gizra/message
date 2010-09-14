<?php
// $Id: message.views.inc,v 1.4 2010/06/07 10:54:11 amitaibu Exp $

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
 * Implementation of hook_views_data().
 */
function message_views_data() {
  $data = array();

  $data['message_realm']['table']['group'] = t('Message realm');

  $data['message_realm']['realm_id'] = array(
    'title' => t('Realm ID'),
    'help' => t('The realm ID the message belongs to.'),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    // Information for accepting a realm ID as a filter.
    'filter' => array(
      'handler' => 'views_handler_filter_string',
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string',
    ),
  );

  $data['message_realm']['realm_id_numeric'] = array(
    'title' => t('Realm ID (numeric)'),
    'help' => t('The realm ID the message belongs to in case it is numeric (thus allowing more Views features).'),
    // This is a dummy field, so point it to a real field.
    'real field' => 'realm_id',

    'field' => array(
      'handler' => 'views_handler_field',
      'click sortable' => TRUE,
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_numeric',
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_numeric',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );

  $data['message_realm']['message_plugin_realm'] = array(
    'title' => t('Accessible realms'),
    'help' => t('The realms a user may access.'),

    // This is a dummy field, so point it to a real field.
    'real field' => 'iid',

    'filter' => array(
      'handler' => 'message_handler_filter_realm_user_access',
    ),
  );

  $data['message_realm']['realm'] = array(
    'title' => t('Realm'),
    'help' => t('The type of the realm (e.g. "user realm").'),
    'field' => array(
      'handler' => 'message_handler_field_realm',
      'click sortable' => TRUE,
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'filter' => array(
      'handler' => 'message_handler_filter_realm',
    ),
    'argument' => array(
      'handler' => 'message_handler_argument_realm',
    ),
  );


  $data['message_instance']['table']['group'] = t('Message');
  $data['message_instance']['table']['base'] = array(
    'field' => 'iid',
    'title' => t('Message'),
    'help' => t('Messages that are related to enteties. Can be used as activity stream.'),
  );

  // Database columns.
  $data['message_instance']['iid'] = array(
    'title' => t('Activity Unique ID'),
    'help' => t('The unique id for each activity'),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
    'relationship' => array(
      'title' => t('Message realm'),
      'help' => t('Relate a message to the message realm.'),
      'handler' => 'views_handler_relationship',
      'base' => 'message_realm',
      'base field' => 'iid',
      'label' => t('Message realm'),
    ),
  );

  $data['message_instance']['name'] = array(
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

  $data['message_instance']['entity_type'] = array(
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

  $data['message_instance']['eid'] = array(
    'title' => t('Entity ID'),
    'help' => t('Entity ID used to maintain the relationship between the message and the entity that is related to it.'),
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

  $data['message_instance']['extra_identifier'] = array(
    'title' => t('Extra identifier'),
    'help' => t('An optional identifier that can group message instances together.'),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
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
  foreach (message_get_plugin_messages() as $plugin_name => $plugin) {
    $plugin_data = array();
    if (!empty($plugin['views']['data callback'])) {
      $plugin_data = call_user_func($plugin['views']['data callback']);
    }
    $data = array_merge_recursive($plugin_data, $data);
  }

  return $data;
}