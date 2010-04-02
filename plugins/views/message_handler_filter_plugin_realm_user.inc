<?php
// $Id:$

/**
 * @file
 * Contains the user message filter handler.
 */

/**
 * Handler to filter by user ID realm.
 *
 * @ingroup views
 */
class message_handler_filter_plugin_realm_user extends message_handler_filter_plugin_realm {
  function init(&$view, $options) {
    parent::init(&$view, $options);
    // Get the plugin realm into the Views object.
    $this->message_plugin = message_get_plugin('message_plugin_user');
  }
}
