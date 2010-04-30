<?php
// $Id: message_plugin_user.inc,v 1.1 2010/04/02 20:01:17 amitaibu Exp $

/**
 * Message plugin for user realm.
 */
class message_plugin_user extends message_plugin {

  function get_accessibale_id_array() {
    return array($this->account->uid);
  }

  function get_views_handlers() {
    return array(
      'message_handler_filter_plugin_realm_user' => array(
        'parent' => 'message_handler_filter_plugin_realm',
        'path' => drupal_get_path('module', 'message') .'/plugins/views',
      ),
    );
  }

  function get_views_data() {
    $data = array();

    $data['message_realm']['plugin_user_realm'] = array(
      'title' => t('User realm'),
      'help' => t('Filter messages by user realm.'),
      // This is a dummy field, so point it to a real field.
      'real field' => 'iid',

      'filter' => array(
        'handler' => 'message_handler_filter_plugin_realm_user',
        'label' => t('User'),
      ),
    );

    return $data;
  }

}
