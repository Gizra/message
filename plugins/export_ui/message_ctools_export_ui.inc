<?php
// $Id: message_ctools_export_ui.inc,v 1.1 2010/06/07 10:54:11 amitaibu Exp $

$plugin = array(
  'schema' => 'message',
  'menu' => array(
    'menu item' => 'message',
  ),
  'title' => t('Message'),
);

function message_ctools_export_ui_form(&$form, $plugin, $op, $export) {
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