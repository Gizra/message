<?php
// $Id: flag_handler_filter_flagged.inc,v 1.1.2.5.2.3 2010/01/08 05:03:16 quicksketch Exp $

/**
 * @file
 * Contains the flagged content filter handler.
 */

/**
 * Handler to filter by role realm.
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
