<?php
// $Id: context_condition_node.inc,v 1.1.2.2 2010/02/09 04:38:01 yhahn Exp $

/**
 * Expose node views/node forms of specific node types as a context condition.
 */
class message_plugin_role extends message_plugin {

  function get_accessibale_id_array() {
    return array_keys($this->account->roles);
  }

  function get_views_handlers() {
    return array(
      'message_handler_filter_plugin_realm_role' => array(
        'parent' => 'message_handler_filter_plugin_realm',
        'path' => drupal_get_path('module', 'message') .'/plugins/views',
      ),
    );
  }

  function get_views_data() {
    $data = array();

    $data['message_realm']['plugin_role_realm'] = array(
      'title' => t('Role realm'),
      'help' => t('Filter messages by role realm.'),
      // This is a dummy field, so point it to a real field.
      'real field' => 'iid',

      'filter' => array(
        'handler' => 'message_handler_filter_plugin_realm_role',
        'label' => t('Role'),
      ),
    );

    return $data;
  }

}
