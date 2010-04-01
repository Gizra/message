<?php
// $Id: taxonomy.rules_forms.inc,v 1.1.2.9 2009/07/10 13:05:33 fago Exp $


/**
 * @file Rules configuration forms for the taxonomy module.
 *
 * @addtogroup rules
 * @{
 */

/**
 * Action: Load a message configuration form.
 */
function message_action_load_message_form($settings, &$form, $form_state) {
  $options = array();

  if ($messages = message_load()) {
    foreach ($messages as $message) {
      $options[$message->name] = check_plain($message->name);
      if (!empty($message->description)) {
        $options[$message->name] .= ' ('. check_plain($message->description) .')';
      }
    }
  }

  $form['settings']['message'] = array(
    '#type' => 'fieldset',
    '#title' => t("Select a message"),
    '#description' => !empty($options) ? t('Select the message.') : t('There are no existing messages.'),
  );
  $form['settings']['message']['message_select'] = array(
    '#type' => 'select',
    '#title' => t('Message'),
    '#options' => $options,
    '#disabled' => empty($options),
  );

  $form['settings']['message']['message_text'] = array(
    '#type' => 'textarea',
    '#title' => t('Select by message id'),
    '#default_value' => !empty($settings['message']['message_text']) ? $settings['message']['message_text'] : '',
    '#disabled' => empty($options),
    '#description' => t('Optional: enter the message name that should be loaded . If this field is used "Select a message" field will be ignored.'),

  );
}

/**
 * Action: Create a message instance configuration form.
 */
function message_action_create_message_instance_form($settings, &$form, $form_state) {
  $form['settings']['arguments'] = array(
    '#type' => 'textarea',
    '#title' => t('Arguments'),
    '#default_value' => !empty($settings['arguments']) ? $settings['arguments'] : '',
    // TODO: Better description.
    '#description' => t('The replacement arguments. Each argument should be in a separate line. <code>@node|node/1</code>.'),
  );

  $form['settings']['entity_type'] = array(
    '#type' => 'textfield',
    '#title' => t('Entity type'),
    '#default_value' => !empty($settings['entity_type']) ? $settings['entity_type'] : '',
    '#description' => t('Optional; The type of entity being acted upon (e.g. node, user).'),
  );

  $form['settings']['eid'] = array(
    '#type' => 'textfield',
    '#title' => t('Entity ID'),
    '#default_value' => !empty($settings['eid']) ? $settings['eid'] : '',
    '#description' => t('Optional; Entity ID used to maintain the relationship between the message and the entity that is related to it.'),
  );

  $form['settings']['extra_identifier'] = array(
    '#type' => 'textfield',
    '#title' => t('Extra identifier'),
    '#default_value' => !empty($settings['extra_identifier']) ? $settings['extra_identifier'] : '',
    '#description' => t('Optional; An identifier that can group message instances together.'),
  );
}

/**
 * Action: Assign a message instance to realm configuration form.
 */
function message_action_assign_message_instance_to_realm_form($settings, &$form, $form_state) {
  // Get existing realms.
  $options = array();
  ctools_include('plugins');
  $plugins = ctools_get_plugins('message', 'plugins');
  foreach ($plugins as $name => $value) {
    if (!empty($value['realm'])) {
      $options[$value['realm']] = check_plain($value['title']);
    }
  }
  $form['settings']['realm'] = array(
    '#type' => 'select',
    '#title' => t('Realm'),
    '#default_value' => !empty($settings['realm']) ? $settings['realm'] : '',
    '#options' => $options,
    '#disabled' => !$options,
    '#required' => TRUE,
  );

  $form['settings']['realm_id'] = array(
    '#type' => 'textfield',
    '#title' => t('Realm ID'),
    '#default_value' => !empty($settings['realm_id']) ? $settings['realm_id'] : '',
    '#required' => TRUE,
  );
}