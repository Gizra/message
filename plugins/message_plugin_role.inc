<?php
// $Id: context_condition_node.inc,v 1.1.2.2 2010/02/09 04:38:01 yhahn Exp $

/**
 * Expose node views/node forms of specific node types as a context condition.
 */
class message_plugin_role extends message_plugin {

  public function access() {
    $access = FALSE;
    if (!empty($this->message_realm[$this->realm])) {
      // Check if the user has access to the realm by their ID.
      $access = array_intersect(array_keys($this->account->roles), $this->messages[$this->realm]);
    }
    return $access;
  }

  public function get_views_handlers() {
    return array(
      'message_handler_filter_message_plugin' => array(
        'parent' => 'views_handler_filter_boolean_operator',
        'path' => drupal_get_path('module', 'message') .'/plugins/views',
      ),
    );
  }

}
