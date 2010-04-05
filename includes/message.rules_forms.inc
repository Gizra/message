<?php
// $Id: message.rules_forms.inc,v 1.1 2010/04/02 20:01:17 amitaibu Exp $


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

  $sample = t("return array(\n '@link' => array('callback' => 'url', 'callback arguments' => \"node/\$node->nid\"),\n '@title' => \$node->title,\n);");
  $form['settings']['code'] = array(
    '#type' => 'textarea',
    '#title' => t('Arguments by code'),
    '#default_value' => $settings['code'],
    '#rows' => 5,
    '#description' => t('Advanced usage only: PHP code that returns the value to set. Should not include &lt;?php ?&gt; delimiters. If this field is filled out, the value returned by this code will override any value specified above in "Arguments". Expected format: <pre>@sample</pre>.', array('@sample' => $sample)),
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
 * Validate the chosen value or php code.
 */
function message_action_create_message_instance_validate($form, &$form_state) {
  if (isset($form_state['values']['settings']['code']) && ($php = $form_state['values']['settings']['code'])) {
    if (strpos($php, 'return') === FALSE) {
      form_set_error('code', t('You have to return the default value in the expected format.'));
    }
  }
}

function message_action_create_message_instance_submit(&$settings, $form, &$form_state) {
  $settings['code'] = $form_state['values']['settings']['code'];

  if (function_exists('rules_action_custom_php_submit')) {
    // Support adding variables to the php code, if php module is present.
    rules_action_custom_php_submit($settings, $form, $form_state);
  }
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