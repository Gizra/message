<?php

/**
 * Helper to implementation of hook_ctools_plugin_api().
 */
function _message_example_ctools_plugin_api() {
  $args = func_get_args();
  $module = array_shift($args);
  $api = array_shift($args);
  if ($module == "message" && $api == "message") {
    return array("version" => 3);
  }
}

/**
 * Helper to implementation of hook_message_default_messages().
 */
function _message_example_message_default_messages() {
  $export = array();
  $message = new stdClass;
  $message->disabled = FALSE; /* Edit this to true to make a default message disabled initially */
  $message->api_version = 3;
  $message->name = 'create';
  $message->description = '';
  $message->tag = '';
  $message->message = '@node created.';
  $message->language = 'en';

  $translatables['create'] = array(
  );

  $export['create'] = $message;
  return $export;
}

/**
 * Helper to implementation of hook_rules_defaults().
 */
function _message_example_rules_defaults() {
  return array(
    'rules' => array(
      'message_example_1' => array(
        '#type' => 'rule',
        '#set' => 'event_node_view',
        '#label' => 'Create message instance',
        '#active' => 1,
        '#weight' => '0',
        '#categories' => array(
          '0' => 'message',
          'message_example' => 'message_example',
        ),
        '#status' => 'default',
        '#conditions' => array(
          '0' => array(
            '#info' => array(
              'label' => 'Check a truth value',
              'arguments' => array(
                'boolean' => array(
                  'type' => 'boolean',
                  'label' => 'Truth value',
                ),
              ),
              'module' => 'Rules',
            ),
            '#name' => 'rules_condition_check_boolean',
            '#settings' => array(
              'boolean' => '<?php return $page; ?>',
              '#eval input' => array(
                'rules_input_evaluator_php' => array(
                  'boolean' => array(
                    '0' => 'page',
                  ),
                ),
              ),
            ),
            '#type' => 'condition',
            '#weight' => 0,
          ),
        ),
        '#actions' => array(
          '0' => array(
            '#weight' => 0,
            '#info' => array(
              'label' => 'Load a message',
              'new variables' => array(
                'message' => array(
                  'type' => 'message',
                  'label' => 'Message',
                ),
              ),
              'module' => 'Message',
            ),
            '#name' => 'message_action_load_message',
            '#settings' => array(
              'message' => array(
                'message_select' => 'create',
                'message_text' => '',
              ),
              '#argument map' => array(
                'message' => 'message',
              ),
            ),
            '#type' => 'action',
          ),
          '1' => array(
            '#type' => 'action',
            '#settings' => array(
              'message_instance' => array(
                'arguments' => '@node|[node:title]',
                'entity_type' => 'node',
                'eid' => '[node:nid]',
                'extra_identifier' => '',
              ),
              '#argument map' => array(
                'message' => 'message',
                'user' => 'author',
                'message_instance' => 'message_instance',
              ),
            ),
            '#name' => 'message_action_create_message_instance',
            '#info' => array(
              'label' => 'Create a message instance',
              'arguments' => array(
                'message' => array(
                  'type' => 'message',
                  'label' => 'Message',
                ),
                'user' => array(
                  'type' => 'user',
                  'label' => 'User',
                ),
              ),
              'new variables' => array(
                'message_instance' => array(
                  'type' => 'message_instance',
                  'label' => 'Message instance',
                ),
              ),
              'module' => 'Message',
            ),
            '#weight' => 0,
          ),
          '2' => array(
            '#weight' => 0,
            '#info' => array(
              'label' => 'Assign a message instance to realm',
              'arguments' => array(
                'message_instance' => array(
                  'type' => 'message_instance',
                  'label' => 'Message instance',
                ),
              ),
              'eval input' => array(
                '0' => 'realm',
                '1' => 'realm_id',
              ),
              'module' => 'Message',
            ),
            '#name' => 'message_action_assign_message_instance_to_realm',
            '#settings' => array(
              'realm' => 'role',
              'realm_id' => '2',
              '#argument map' => array(
                'message_instance' => 'message_instance',
              ),
            ),
            '#type' => 'action',
          ),
        ),
        '#version' => 6003,
      ),
    ),
  );
}
