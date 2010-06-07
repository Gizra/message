<?php
// $Id: message_plugin_role.inc,v 1.2 2010/05/30 07:56:21 amitaibu Exp $

/**
 * * Message plugin for role realm.
 */
class message_plugin_role extends message_plugin {

  function get_accessible_id_array() {
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
