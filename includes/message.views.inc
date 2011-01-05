<?php
// $Id$

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
  );

  foreach ($handlers as $handler => $parent) {
    $data['handlers'][$handler] = array(
      'parent' => $parent,
      'path' => drupal_get_path('module', 'message') .'/includes/views',
    );
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
    'title' => t('Message unique ID'),
    'help' => t('The unique ID for each activity'),
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

  $data['message']['uid'] = array(
    'title' => t('User'),
    'help' => t('The user that is associated with the message.'),
    'relationship' => array(
      'base' => 'users',
      'handler' => 'views_handler_relationship',
      'label' => t('Message user'),
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


  return $data;
}