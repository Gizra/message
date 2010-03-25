<?php
// $Id:$

/**
 * @file
 * Provides support for the Views module.
 */

/**
 * Implementation of hook_views_handlers().
 */
function ron_views_handlers() {
  $data = array();

  // Add ron's core handlers.
  $data['handlers']['ron_handler_field_message_render'] = array(
    'parent' => 'views_handler_field',
    'path' => drupal_get_path('module', 'ron') .'/includes',
  );

  // Allow plugins to declare their handlers. Handlers should define the path
  // as-well.
  $plugins = ctools_get_plugins('ron', 'plugins');
  foreach ($plugins as $name => $value) {
    $plugin = ron_get_plugin($name);
    if ($handler = $plugin->get_views_handlers()) {
      $data['handlers'] = $handler;
    }
  }

  return $data;
}

/**
 * Implementation of hook_views_data().
 */
function ron_views_data() {
  $data = array();

 $data['ron']['table']['group'] = t('River of news');

  $data['ron']['table']['base'] = array(
    'field' => 'iid',
    'title' => t('River of news'),
    'help' => t('Activity are items that are recorded for a users activity within the site'),
  );

  // Database columns
  $data['ron']['iid'] = array(
    'title' => t('Activity Unique ID'),
    'help' => t('The unique id for each activity'),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );


  $data['ron']['timestamp'] = array(
    'title' => t('Activity Creation Date'),
    'help' => t('The time that the activity was created'),
    'field' => array(
      'handler' => 'views_handler_field_date',
      'click sortable' => TRUE,
    ),
    'sort' => array(
      'handler' => 'views_handler_sort_date',
    ),
  );

  // Database columns
  $data['ron']['render'] = array(
    'title' => t('Dummy'),
    'help' => t('The unique id for each activity'),
    'field' => array(
      'handler' => 'ron_handler_field_message_render',
    ),
    'sort' => array(
      'handler' => 'views_handler_sort',
    ),
  );
  return $data;
}