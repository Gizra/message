<?php
// $Id$

/**
 * @file
 * Contains the organic groups admin message filter handler.
 */

/**
 * Handler to filter by organic groups admin realm.
 *
 * @ingroup views
 */
class message_handler_filter_plugin_realm_og_admin extends message_handler_filter_plugin_realm {
  function init() {
    // Get the plugin realm into the Views object.
    $plugin = 'message_plugin_og_admin';
    parent::init();
  }
}
