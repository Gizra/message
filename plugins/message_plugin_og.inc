<?php
// $Id: context_condition_node.inc,v 1.1.2.2 2010/02/09 04:38:01 yhahn Exp $

/**
 * Expose node views/node forms of specific node types as a context condition.
 */
class message_plugin_og extends message_plugin {

  function access() {
    // Check if the user has access to the realm by their ID.
    return array_intersect(array_keys($this->account->og_groups), $this->messages[$this->realm]);
  }

  function get_views_handlers() {
    return array(
      'message_handler_filter_plugin_realm_og' => array(
        'parent' => 'views_handler_filter',
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
