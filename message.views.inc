<?php

/**
 * @file
 * Provide views data for message.module.
 */

/**
 * Implements hook_views_data_alter().
 */
function message_views_data_alter(&$data) {
  $data['message']['get_text'] = array(
    'title' => t('Message text'),
    'help' => t('Get the message text'),
    'field' => array(
      // ID of the area handler plugin to use.
      'id' => 'get_text',
    ),
  );
}
