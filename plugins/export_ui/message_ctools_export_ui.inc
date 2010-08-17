<?php
// $Id$

$plugin = array(
  'schema' => 'message',
  'menu' => array(
    'menu item' => 'message',
  ),
  'title' => t('Message'),

  'title singular' => t('message'),
  'title plural' => t('messages'),
  'title singular proper' => t('Message'),
  'title plural proper' => t('Messages'),
);

function message_ctools_export_ui_form(&$form, &$form_state) {
  $export = $form_state['item'];
  $form += array('info' => array('description' => ''), 'message' => '');
  $form['info']['description'] = array(
    '#title' => t('Description'),
    '#type' => 'textfield',
    '#default_value' => !empty($export->description) ? $export->description : '',
    '#description' => t('The message description.'),
  );

  $form['message'] = array(
    '#title' => t('Message'),
    '#type' => 'textfield',
    '#default_value' => !empty($export->message) ? $export->message : '',
    '#description' => t('The message'),
    '#required' => TRUE,
  );
}