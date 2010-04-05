<?php
// $Id: message_handler_filter_plugin_realm_og.inc,v 1.1 2010/04/02 20:01:17 amitaibu Exp $

/**
 * @file
 * Contains the organic groups message filter handler.
 */

/**
 * Handler to filter by organic groups realm.
 *
 * @ingroup views
 */
class message_handler_filter_plugin_realm_og extends message_handler_filter_plugin_realm {
  function init() {
    // Get the plugin realm into the Views object.
    $plugin = 'message_plugin_og';
    parent::init();
  }
}
