<?php

$plugin = array(
  'schema' => 'message',
  'menu' => array(
    'menu item' => 'message',
    'items' => array(
      // Add our own custom "Settings" page.
      'settings' => array(
        'path' => 'settings',
        'title' => 'Settings',
        'page callback' => 'drupal_get_form',
        'page arguments' => array('message_ctools_export_ui_settings'),
        'load arguments' => array('message_ctools_export_ui'),
        'access callback' => 'user_access',
        'access arguments' => array('administer site configuration'),
        // Set the file path to this include file.
        'file' => 'message_ctools_export_ui.inc',
        'file path' => drupal_get_path('module', 'message') . '/plugins/export_ui',

        'type' => MENU_LOCAL_TASK,
      ),
    ),

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

/**
 * Settings page.
 */
function message_ctools_export_ui_settings($form_state) {
  $form = array();

  $options = array(
    0 => t('Disable cleanup'),
    1 => t('Enable cleanup'),
  );
  $form['message_entity_delete_cleanup'] = array(
    '#title' => t('Cleanup upon entity delete'),
    '#description' => t('When a node, comment or a user is deleted, remove all the message instance and message realms that are associated with it. This cleanup is a simple one, and for more advanced buiness logic, and implementing module may disable this setting, and take care of cleanup themselves.'),
    '#type' => 'radios',
    '#options' => $options,
    '#default_value' => variable_get('message_entity_delete_cleanup', TRUE),
  );

  return system_settings_form($form);
}