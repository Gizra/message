<?php

/**
 * @file
 * Provide views data for message.module.
 */

/**
 * Implements hook_views_data().
 */
function message_views_data() {
  $data = array();

 // message table

  $data['message']['table']['group']  = t('Message');
  $data['message']['table']['base'] = array(
    'field' => 'mid',
    'title' => t('Message'),
    'help' => t('Logged messages.'),
  );
  $data['message']['table']['entity type'] = 'message';
  $data['message']['table']['wizard_id'] = 'message';

  // message field
  $data['message']['mid'] = array(
    'title' => t('Message ID'),
    'help' => t('The mid of a Message.'),
    'field' => array(
      'id' => 'numeric',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'argument' => array(
      'id' => 'message',
      'name field' => 'name',
      'zero is null' => TRUE,
    ),
    'filter' => array(
      'title' => t('Message ID'),
      'help' => t('Filter by the message ID.'),
      'id' => 'mid',
      'numeric' => TRUE,
    ),
  );

  // Term name field
  $data['message']['type'] = array(
    'title' => t('Message type'),
    'help' => t('The message type.'),
    'field' => array(
      'id' => 'type',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'filter' => array(
      'id' => 'string',
      'help' => t('The message type.'),
    ),
    'argument' => array(
      'id' => 'string',
      'help' => t('The message type.'),
      'many to one' => TRUE,
      'empty field name' => t('Uncategorized'),
    ),
  );

  $data['message']['text'] = array(
    'title' => t('The message text'),
    'help' => t('The text of the message'),
    'field' => array(
      'id' => 'get_text',
      'real field' => 'mid',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
  );

  return $data;
}
