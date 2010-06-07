<?php
// $Id$

$plugin = array(
  'name' => 'message_ctools_export_ui',
  'schema' => 'message',
  'menu' => array(
    'menu item' => 'message',
  ),
  'title' => t('Message'),
  'form' => array(
    'settings' => 'message_ctools_export_ui_form',
  ),
  'handler' => array(
    'class' => 'message_ctools_export_ui',
    // The parent is the plugin name, not the class name.
    'parent' => 'ctools_export_ui',
  ),
);

function message_ctools_export_ui_form(&$form, $plugin_name, $op, $export) {
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