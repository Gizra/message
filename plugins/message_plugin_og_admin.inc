<?php
// $Id: context_condition_node.inc,v 1.1.2.2 2010/02/09 04:38:01 yhahn Exp $

/**
 * Expose node views/node forms of specific node types as a context condition.
 */
class message_plugin_og_admin extends message_plugin {

  function access() {
    // Check if the user has access to the realm by their ID.
    return array_intersect(array_keys($this->account->og_groups), $this->messages[$this->realm]);
  }

  function get_accessibale_id_array() {
    return array_keys($this->account->og_groups);
    $gids = array();
    foreach ($user->og_groups as $key => $value) {
      if (!empty($value['is_admin'])) {
        $gids[] = $key;
      }
    }
    return $gids;
  }

  function get_views_handlers() {
    return array(
      'message_handler_filter_plugin_realm_og_admin' => array(
        'parent' => 'message_handler_filter_plugin_realm',
        'path' => drupal_get_path('module', 'message') .'/plugins/views',
      ),
    );
  }

  function get_views_data() {
    $data = array();

    $data['message_realm']['plugin_og_admin_realm'] = array(
      'title' => t('Organic groups admin realm'),
      'help' => t('Filter messages by organic groups admin realm.'),
      // This is a dummy field, so point it to a real field.
      'real field' => 'iid',

      'filter' => array(
        'handler' => 'message_handler_filter_plugin_realm_og_admin',
        'label' => t('Organic groups admin'),
      ),
    );

    return $data;
  }

}
