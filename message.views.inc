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
    'title' => t('Type'),
    'help' => t('The content type (for example, "blog entry", "forum post", "story", etc).'),
    'field' => array(
      // @see node_type
      'id' => 'message_type',
    ),
    'sort' => array(
      'id' => 'standard',
    ),
    'filter' => array(
      'id' => 'bundle',
    ),
    'argument' => array(
      'id' => 'node_type',
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

  $data['message']['created'] = array(
    'title' => t('Post date'),
    'help' => t('The date the content was posted.'),
    'field' => array(
      'id' => 'date',
    ),
    'sort' => array(
      'id' => 'date'
    ),
    'filter' => array(
      'id' => 'date',
    ),
  );

  $data['message']['uid'] = array(
    'title' => t('Author uid'),
    'help' => t('The user authoring the content. If you need more fields than the uid add the content: author relationship'),
    'relationship' => array(
      'title' => t('Content author'),
      'help' => t('Relate content to the user who created it.'),
      'id' => 'standard',
      'base' => 'users',
      'field' => 'uid',
      'label' => t('author'),
    ),
    'filter' => array(
      'id' => 'user_name',
    ),
    'argument' => array(
      'id' => 'numeric',
    ),
    'field' => array(
      'id' => 'user',
    ),
  );

  return $data;
}
