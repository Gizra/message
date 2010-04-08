<?php
// $Id: message_ui.admin.inc,v 1.1 2010/04/05 17:50:06 amitaibu Exp $

/**
 * Message listing form. Allows for quick creation of new messages.
 */
function message_ui_admin() {
  message_load(NULL, TRUE);

  $form = array(
    '#theme' => 'message_ui_admin',
    '#messages' => message_load(),
  );
  $form['name'] = array(
    '#title' => t('Name'),
    '#type' => 'textfield',
    '#required' => TRUE,
    '#maxlength' => 255,
    '#size' => 15,
    '#element_validate' => array('message_ui_form_name_validate'),
  );
  $form['tag'] = array(
    '#title' => t('Tag'),
    '#type' => 'textfield',
    '#required' => FALSE,
    '#maxlength' => 255,
    '#size' => 15,
  );
  $form['submit'] = array(
    '#value' => t('Create new message'),
    '#type' => 'submit',
    '#submit' => array('message_ui_admin_submit'),
  );
  return $form;
}

/**
 * message listing form submit handler.
 */
function message_ui_admin_submit(&$form, &$form_state) {
  ctools_include('export');
  $message = ctools_export_new_object('message');
  $message->name = $form_state['values']['name'];
  $message->tag = $form_state['values']['tag'];
  if (message_save($message)) {
    drupal_set_message(t('Saved message %title.', array('%title' =>  $message->name)));
  }
  else {
    drupal_set_message(t('Could not save message %title.', array('%title' =>  $message->name)), 'error');
  }
}

/**
 * Generates the omnibus message definition editing form.
 *
 * @param $op
 *   The type of form to build. Either "add", "view" or "edit"
 * @param $cid
 *   The db message identifier - required when $op == "edit"
 *
 * @return
 *   A Drupal form array.
 */
function message_ui_form(&$form_state, $op, $message = NULL) {
  ctools_include('export');

  $message = !$message ? ctools_export_new_object('message') : $message;
  switch ($op) {
    case 'add':
      drupal_set_title(t('Add a new message'));
      break;
    case 'edit':
      if ($message->export_type & EXPORT_IN_DATABASE) {
        drupal_set_title(t('Editing message %title', array('%title' => $message->name)));
      }
      else {
        drupal_set_title(t('Viewing message %title', array('%title' => $message->name)));
      }
      break;
    case 'clone':
      drupal_set_title(t('Cloning message %title', array('%title' => $message->name)));
      break;
  }

  $form = array(
    '#base' => 'message_ui_form',
    '#theme' => 'message_ui_form',
  );
  $form['message'] = array(
    '#type' => 'value',
    '#value' => $message,
  );

  // Core message definition
  $form['info'] = array(
    '#type' => 'fieldset',
    '#tree' => FALSE,
  );
  $form['info']['name'] = array(
    '#title' => t('Name'),
    '#type' => 'textfield',
    '#required' => TRUE,
    '#maxlength' => 255,
    '#default_value' => isset($message->name) ? $message->name : '',
    '#description' => t('Example: <code>create_content</code>') .'<br/>'. t('A system name for this message. May only contain lowercase letters, dashes, underscores, and numbers.'),
    '#element_validate' => ($op === 'edit') ? array() : array('message_ui_form_name_validate'),
  );
  if ($op === 'edit') {
    $form['info']['name']['#disabled'] = TRUE;
    $form['info']['name']['#value'] = $message->name;
  }
  $form['info']['tag'] = array(
    '#title' => t('Tag'),
    '#type' => 'textfield',
    '#required' => FALSE,
    '#maxlength' => 255,
    '#default_value' => isset($message->tag) ? $message->tag : '',
    '#description' => t('Example: <code>my_module</code>') .'<br/>'. t('A tag to group this message with others.'),
  );
  $form['info']['description'] = array(
    '#title' => t('Description'),
    '#type' => 'textfield',
    '#required' => FALSE,
    '#maxlength' => 255,
    '#default_value' => isset($message->description) ? $message->description: '',
    '#description' => t('The description of this message definition.'),
  );

  $form['message'] = array(
    '#title' => t('Message'),
    '#type' => 'textfield',
    '#required' => TRUE,
    '#maxlength' => 255,
    '#default_value' => isset($message->message) ? $message->message: '',
    '#description' => t('The message with placeholders.'),
  );


  // Buttons
  $form['buttons']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
  );
  $form['buttons']['delete'] = array(
    '#type' => 'submit',
    '#value' => t('Delete'),
    '#access' => $op === 'edit',
  );
  return $form;
}

/**
 * Provide a form to confirm one of the provided actions.
 */
function message_ui_confirm(&$form_state, $op = 'delete', $message) {
  $form = array();
  $form['message'] = array('#type' => 'value', '#value' => $message);
  $form['action'] = array('#type' => 'value', '#value' => $op);
  switch ($op) {
    case 'revert':
      $action = t('revert');
      $message = t('This action will permanently remove any customizations made to this message.');
      break;
    case 'delete':
      $action = t('delete');
      $message = t('This action will remove this message permanently from your site.');
      break;
    case 'disable':
      $action = t('disable');
      $message = '';
      break;
    case 'enable':
      $action = t('enable');
      $message = '';
      break;
  }
  $form = confirm_form($form,
    t('Are you sure you want to !action the message %title?', array('%title' => $message->name, '!action' => $action)),
    'admin/build/message',
    $message,
    drupal_ucfirst($action), t('Cancel')
  );
  return $form;
}

/**
 * Submit handler for the message_ui_confirm form.
 */
function message_ui_confirm_submit($form, &$form_state) {
  ctools_include('export');
  $message = $form_state['values']['message'];
  switch ($form_state['values']['action']) {
    case 'revert':
    case 'delete':
      message_delete($message);
      break;
    case 'disable':
      ctools_export_set_object_status($message);
      break;
    case 'enable':
      ctools_export_set_object_status($message, FALSE);
      break;
  }
  $form_state['redirect'] = 'admin/build/message';
}

/**
 * Page callback for import form. Switches form output to message form
 * if import submission has occurred.
 */
function message_ui_import_page() {
  if (!empty($_POST) && $_POST['form_id'] == 'message_ui_form') {
    return drupal_get_form('message_ui_form', 'add');
  }
  return drupal_get_form('message_ui_import');
}

/**
 * Import form. Provides simple helptext instructions and textarea for
 * pasting a message definition.
 */
function message_ui_import() {
  drupal_set_title(t('Import message'));
  $help = t('You can import a message definition by pasting the exported message object code into the field below.');
  $form = array();
  $form['help'] = array(
    '#type' => 'item',
    '#value' => $help,
  );
  $form['import'] = array(
    '#title' => t('message Object'),
    '#type' => 'textarea',
    '#rows' => 10,
    '#required' => TRUE,
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Import'),
  );
  return $form;
}

/**
 * Import form submit handler. Evaluates import code and transfers to
 * message definition form.
 */
function message_ui_import_submit($form, &$form_state) {
  $items = array();
  if ($import = $form_state['values']['import']) {
    ob_start();
    $message = eval($import);
    ob_end_clean();
  }
  if (is_object($message)) {
    if (isset($message->name) && $exists = message_load($message->name)) {
      drupal_set_message(t('A message with this name already exists. Please remove the existing message before importing this definition.'), 'error');
      $form_state['redirect'] = 'admin/build/message';
    }
    else {
      drupal_set_title(t('Add message'));
      $output = drupal_get_form('message_ui_form', 'add', (object) $message);
      print theme('page', $output);
      exit;
    }
  }
  else {
    drupal_set_message(t('An error occurred while importing. Please check your message definition.', 'error'));
    $form_state['redirect'] = 'admin/build/message';
  }
}

/**
 * Provides a form with an exported message definition for use in modules.
 *
 * @param $cid
 *   A message id.
 *
 * @return
 *   A FormAPI array.
 */
function message_ui_export(&$form_state, $message) {
  ctools_include('export');
  drupal_set_title(t('Export %title', array('%title' => $message->name)));
  return ctools_export_form($form_state, message_export_message($message), t('Export'));
}

/**
 * Validate message name values.
 */
function message_ui_form_name_validate($element, &$form_state) {
  // Check for string identifier sanity
  if (!preg_match('!^[a-z0-9_-]+$!', $element['#value'])) {
    form_set_error('name', t('The message name can only consist of lowercase letters, dashes, underscores, and numbers.'));
  }
  // Check for name collision
  else if ($exists = message_load($element['#value'])) {
    form_set_error('name', t('A message with this name already exists. Please choose another name or delete the existing message before creating a new one.'));
  }
}

/**
 * Produces a message object from submitted form values.
 *
 * @param $form
 *   A form array with submitted values
 *
 * @return
 *   A message object
 */
function message_ui_form_process($form) {
  ctools_include('export');
  $message = ctools_export_new_object('message');
  $message->name = isset($form['name']) ? $form['name'] : NULL;
  $message->description = isset($form['description']) ? $form['description'] : NULL;
  $message->message = isset($form['message']) ? $form['message'] : NULL;
  return $message;
}

/**
 * Submit handler for main message_ui form.
 */
function message_ui_form_submit($form, &$form_state) {
  switch ($form_state['clicked_button']['#id']) {
    // Send user to delete confirmation page
    case 'edit-delete':
      if (isset($form_state['values']['message']->name)) {
        $form_state['redirect'] = "admin/build/message/list/{$form_state['values']['message']->name}/delete";
      }
      return;
    // Process form values and save and/or update the message in the db
    case 'edit-submit':
      $message = message_ui_form_process($form_state['values']);
      $result = message_save($message);
      if ($result) {
        drupal_set_message(t('Saved message %title.', array('%title' =>  $message->name)));
      }
      else {
        drupal_set_message(t('Could not save message %title.', array('%title' =>  $message->name)), 'error');
      }
      break;
  }
  $form_state['redirect'] = 'admin/build/message';
}