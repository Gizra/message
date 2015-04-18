<?php

/**
 * @file
 * Provide views data for message.module.
 */

/**
 * Implements hook_views_data_alter().
 */
function message_views_data_alter(&$data) {
  $data['message']['message_bulk_form'] = array(
    'title' => t('Message operations bulk form'),
    'help' => t('Add a form element that lets you run operations on multiple messages.'),
    'field' => array(
      'id' => 'message_bulk_form',
    ),
  );

  $data['message']['get_text'] = array(
    'title' => t('Message text'),
    'help' => t('Get the message text'),
    'field' => array(
      // ID of the area handler plugin to use.
      'id' => 'get_text',
    ),
  );
}
