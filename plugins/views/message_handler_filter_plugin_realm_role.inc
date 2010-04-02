<?php
// $Id$

/**
 * @file
 * Contains the role message filter handler.
 */

/**
 * Handler to filter by role realm.
 *
 * @ingroup views
 */
class message_handler_filter_plugin_realm_role extends message_handler_filter_plugin_realm {
  function init(&$view, $options) {
    parent::init(&$view, $options);
    // Get the plugin realm into the Views object.
    $this->message_plugin = message_get_plugin('message_plugin_role');
  }
}
