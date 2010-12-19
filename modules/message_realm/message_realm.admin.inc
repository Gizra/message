<?php
// $Id:$

/**
 * @file
 */

function message_realm_admin_fields($form, &$form_state) {
  $item = menu_get_item();
  $bundle = $item['map'][4]->name;
  $options = array();

  if (!field_info_instance('message', MESSAGE_REALM_FIELD_MESSAGE_REALM, $bundle)) {
    $options[MESSAGE_REALM_FIELD_MESSAGE_REALM] = t('Message realm');
  }

  if (!field_info_instance('message', MESSAGE_REALM_FIELD_MESSAGE_REALM_ID, $bundle)) {
    $options[MESSAGE_REALM_FIELD_MESSAGE_REALM_ID] = t('Message realm id');
  }

  $form = array();
  if ($options) {
    $form['field'] = array(
      '#title' => t('Realm fields'),
      '#type' => 'select',
      '#options' => $options,
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Add field'),
    );
    $form['bundle'] = array(
      '#type' => 'value',
      '#value' => $bundle,
    );
  }
  else {
    $form['readonly'] = array(
      '#markup' => t('All message realm fields were added.'),
    );
  }

  return $form;
}

function message_realm_admin_fields_submit($form, &$form_state) {
  $field = $form_state['values']['field'];
  $bundle = $form['bundle']['#value'];

  if ($field == MESSAGE_REALM_FIELD_MESSAGE_REALM) {
    $label = t('Message realm');
    $desc = t('The realm the message will be assigned to.');
  }
  else {
    $label = t('Message realm ID');
    $desc = t('The realm ID the message will be assigned to.');
  }

  $instance = array(
    'field_name' => $field,
    'bundle' => $bundle,
    'entity_type' => 'message',
    'label' => $label,
    'description' => $desc,
  );

  if (field_create_instance($instance)) {
    drupal_set_message(t('Added %label to %bundle.', array('%label' => $label, '%bundle' => $bundle)));
  }
}