<?php
// $Id: context_condition.inc,v 1.1.2.2 2010/02/09 04:38:01 yhahn Exp $

/**
 * Base class for a message plugin.
 */
class message_plugin {
  var $plugin;
  var $message_realm;
  var $realm;

  /**
   * Constructor. Do not override.
   */
  function __construct($plugin, $info) {
    $this->plugin = $plugin;
    $this->message_realm = message_realm_load_multiple_by_message($info['message_id']);
    $this->realm = $plugin['realm'];
    $this->account = $info['account'];
  }

  /**
   * Determine if the current user has access or not.
   */
  function access() {
    return array_intersect($this->get_accessibale_id_array(), $this->messages[$this->realm]);
  }

  /**
   * Get array of IDs a user may access.
   *
   * This is used in Views handlers.
   */
  function get_accessibale_id_array() {
    return array();
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
    return array(
      'message_handler_filter_plugin_realm' => array(
        'parent' => 'views_handler_filter',
        'path' => drupal_get_path('module', 'message') .'/plugins/views',
      ),
    );
  }

  /**
   * Returns information needed for hook_views_data().
   */
  public function get_views_data() {
    return array();
  }

  /**
   * @} End of "addtogroup views".
   */

}
