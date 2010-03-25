<?php
// $Id: context_condition.inc,v 1.1.2.2 2010/02/09 04:38:01 yhahn Exp $

/**
 * Base class for a message plugin.
 */
class message_plugin {
  var $plugin;
  var $title;
  var $description;
  var $message;

  /**
   * Constructor. Do not override.
   */
  function __construct($plugin, $info) {
    $this->plugin = $plugin;
    $this->messages = message_get_message_realm($info['message_id']);
    $this->account = $info['account'];
  }

  /**
   * Determine if the current user has access or not.
   */
  public function access() {
    // Default to no access control.
    return TRUE;
  }

  /**
   * @addtogroup views
   * @{
   * Methods that can be overridden to support the Views module.
   */

  /**
   * Returns information needed for hook_views_handlers().
   */
  public function get_views_handlers() {
    return array();
  }

  /**
   * @} End of "addtogroup views".
   */

}
