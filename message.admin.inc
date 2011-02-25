<?php

/**
 * @file
 * Message type editing UI.
 */

/**
 * UI controller.
 */
class MessageTypeUIController extends EntityDefaultUIController {

  /**
   * Overrides hook_menu() defaults.
   */
  public function hook_menu() {
    $items = parent::hook_menu();
    $items[$this->path]['description'] = 'Manage message types, including fields.';
    return $items;
  }
}

/**
 * Generates the message type editing form.
 */
function message_type_form($form, &$form_state, $message_type, $op = 'edit') {
  if ($op == 'clone') {
    $message_type->label .= ' (cloned)';
    $message_type->name .= '_clone';
  }

  $form['description'] = array(
    '#title' => t('Description'),
    '#type' => 'textfield',
    '#default_value' => $message_type->description,
    '#description' => t('The human-readable description of this message type.'),
    '#required' => TRUE,
  );
  // Machine-readable type name.
  $form['name'] = array(
    '#type' => 'machine_name',
    '#default_value' => isset($message_type->name) ? $message_type->name : '',
    '#disabled' => entity_has_status('message_type', $message_type, ENTITY_IN_CODE),
    '#machine_name' => array(
      'exists' => 'message_type_load',
      'source' => array('description'),
    ),
    '#description' => t('A unique machine-readable name for this message type. It must only contain lowercase letters, numbers, and underscores.'),
  );

  field_attach_form('message_type', $message_type, $form, $form_state);

  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save message type'),
    '#weight' => 40,
  );

  if (!entity_has_status('message_type', $message_type, ENTITY_IN_CODE) && $op != 'add') {
    $form['actions']['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Delete message type'),
      '#weight' => 45,
      '#limit_validation_errors' => array(),
      '#submit' => array('message_type_form_submit_delete')
    );
  }
  return $form;
}

/**
 * Validation callback.
 */
function message_type_form_validate($form, &$form_state) {
  entity_form_field_validate('message_type', $form, $form_state);
}

/**
 * Form API submit callback for the type form.
 */
function message_type_form_submit(&$form, &$form_state) {
  $message_type = entity_ui_form_submit_build_entity($form, $form_state);
  // Save and go back.
  $message_type->save();
  $form_state['redirect'] = 'admin/structure/messages';
}

/**
 * Form API submit callback for the delete button.
 */
function message_type_form_submit_delete(&$form, &$form_state) {
  $form_state['redirect'] = 'admin/structure/messages/manage/' . $form_state['message_type']->name . '/delete';
}
