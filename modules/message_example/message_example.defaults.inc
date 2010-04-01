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
  $message->name = 'create_new_content';
  $message->description = 'Create a new content.';
  $message->tag = '';
  $message->message = '@node created.';
  $message->language = 'en';

  $translatables['create_new_content'] = array(
    t('Create a new content.'),
  );

  $export['create_new_content'] = $message;
  $message = new stdClass;
  $message->disabled = FALSE; /* Edit this to true to make a default message disabled initially */
  $message->api_version = 3;
  $message->name = 'update_existing_content';
  $message->description = 'Update existing content.';
  $message->tag = '';
  $message->message = '@node updated.';
  $message->language = 'en';

  $translatables['update_existing_content'] = array(
    t('Update existing content.'),
  );

  $export['update_existing_content'] = $message;
  return $export;
}

/**
 * Helper to implementation of hook_rules_defaults().
 */
function _message_example_rules_defaults() {
  return array(
    'rules' => array(
      'message_example_2' => array(
        '#type' => 'rule',
        '#set' => 'event_node_insert',
        '#label' => 'Add message on new content creation',
        '#active' => 1,
        '#weight' => '0',
        '#categories' => array(
          '0' => 'message',
          'message_example' => 'message_example',
        ),
        '#status' => 'default',
        '#conditions' => array(),
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
              'eval input' => array(
                '0' => 'message|message_text',
              ),
              'module' => 'Message',
            ),
            '#name' => 'message_action_load_message',
            '#settings' => array(
              'message' => array(
                'message_select' => 'create_new_content',
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
              'arguments' => '@node|[node:title]',
              'entity_type' => 'node',
              'eid' => '[node:nid]',
              'extra_identifier' => '',
              '#argument map' => array(
                'message' => 'message',
                'user' => 'author',
                'message_instance' => 'message_instance',
              ),
              '#eval input' => array(
                'token_rules_input_evaluator' => array(
                  'arguments' => array(
                    '0' => 'node',
                  ),
                  'eid' => array(
                    '0' => 'node',
                  ),
                ),
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
              'eval input' => array(
                '0' => 'arguments',
                '1' => 'entity_type',
                '2' => 'eid',
                '3' => 'extra_identifier',
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
                '0' => 'realm_id',
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
      'message_example_3' => array(
        '#type' => 'rule',
        '#set' => 'event_node_update',
        '#label' => 'Add message on existing content update',
        '#active' => 1,
        '#weight' => '0',
        '#categories' => array(
          '0' => 'message',
          'message_example' => 'message_example',
        ),
        '#status' => 'default',
        '#conditions' => array(),
        '#actions' => array(
          '0' => array(
            '#info' => array(
              'label' => 'Load a message',
              'new variables' => array(
                'message' => array(
                  'type' => 'message',
                  'label' => 'Message',
                ),
              ),
              'eval input' => array(
                '0' => 'message|message_text',
              ),
              'module' => 'Message',
            ),
            '#name' => 'message_action_load_message',
            '#settings' => array(
              'message' => array(
                'message_select' => 'update_existing_content',
                'message_text' => '',
              ),
              '#argument map' => array(
                'message' => 'message',
              ),
            ),
            '#type' => 'action',
            '#weight' => 0,
          ),
          '1' => array(
            '#type' => 'action',
            '#settings' => array(
              'arguments' => '@node|[node:title]',
              'entity_type' => 'node',
              'eid' => '[node:nid]',
              'extra_identifier' => '',
              '#argument map' => array(
                'message' => 'message',
                'user' => 'author',
                'message_instance' => 'message_instance',
              ),
              '#eval input' => array(
                'token_rules_input_evaluator' => array(
                  'arguments' => array(
                    '0' => 'node',
                  ),
                  'eid' => array(
                    '0' => 'node',
                  ),
                ),
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
              'eval input' => array(
                '0' => 'arguments',
                '1' => 'entity_type',
                '2' => 'eid',
                '3' => 'extra_identifier',
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
                '0' => 'realm_id',
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
