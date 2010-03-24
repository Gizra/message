<?php
// $Id: context_condition_node.inc,v 1.1.2.2 2010/02/09 04:38:01 yhahn Exp $

/**
 * Expose node views/node forms of specific node types as a context condition.
 */
class ron_plugin_role extends ron_plugin {

  public function access() {
    $access = FALSE;
    if (!empty($this->messages[$this->realm])) {
      // Check if the user has access to the realm by their ID.
      $access = array_intersect(array_keys($this->account->roles), $this->messages[$this->realm]);
    }
    return $access;
  }

  public function get_views_handler() {
    return array(
      'ron_handler_filter_ron_plugin' => array(
        'parent' => 'views_handler_filter_boolean_operator',
        'path' => drupal_get_path('module', 'ron') .'/plugins/views',
      ),
    );
  }

}
