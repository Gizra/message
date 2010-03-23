<?php
// $Id: context_condition_node.inc,v 1.1.2.2 2010/02/09 04:38:01 yhahn Exp $

/**
 * Expose node views/node forms of specific node types as a context condition.
 */
class ron_plugin_role extends ron_plugin {

  function access() {
    $access = FALSE;
    if (!empty($this->messages[$this->realm])) {
      // Check if the user has access to the realm by their ID.
      $access = array_intersect(array_keys($this->account->roles), $this->messages[$this->realm]);
    }
    return $access;
  }
}
