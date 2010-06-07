<?php
// $Id: message_plugin.inc,v 1.2 2010/05/30 07:56:21 amitaibu Exp $

/**
 * Base class for a message plugin.
 */
class message_plugin {
  var $plugin;
  var $message_realms;

  /**
   * Constructor. Do not override.
   */
  function __construct($plugin, $info) {
    $this->plugin = $plugin;
    $this->message_realms = !empty($info['message_instance']) ? message_realm_load_multiple_by_message($info['message_instance']->iid) : array();
    $this->account = $info['account'];
  }

  /**
   * Determine if the current user has access or not.
   */
  function access() {
    $access = FALSE;
    // Base plugin might not have a realm.
    if ($realm = $this->plugin->realm && !empty($this->message_realms[$this->plugin->realm])) {
      $access = array_intersect($this->get_accessible_id_array(), $this->message_realms[$this->plugin->realm]);
    }
    return $access;
  }

  /**
   * Get array of IDs a user may access.
   *
   * This is used in Views handlers.
   */
  function get_accessible_id_array() {
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
