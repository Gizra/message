<?php
// $Id:$

/**
 * * Message plugin for organic groups realm.
 */
class message_plugin_og extends message_plugin {

  function get_accessibale_id_array() {
    return array_keys($this->account->og_groups);
  }

  function get_views_handlers() {
    return array(
      'message_handler_filter_plugin_realm_og' => array(
        'parent' => 'message_handler_filter_plugin_realm',
        'path' => drupal_get_path('module', 'message') .'/plugins/views',
      ),
    );
  }

  function get_views_data() {
    $data = array();

    $data['message_realm']['plugin_og_realm'] = array(
      'title' => t('Organic groups realm'),
      'help' => t('Filter messages by organic groups realm.'),
      // This is a dummy field, so point it to a real field.
      'real field' => 'iid',

      'filter' => array(
        'handler' => 'message_handler_filter_plugin_realm_og',
        'label' => t('Organic groups'),
      ),
    );

    return $data;
  }

}
