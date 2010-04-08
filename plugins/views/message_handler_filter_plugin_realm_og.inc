<?php
// $Id: message_handler_filter_plugin_realm_og.inc,v 1.3 2010/04/07 14:28:49 amitaibu Exp $

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
  function init(&$view, $options) {
    parent::init(&$view, $options);
    // Get the plugin realm into the Views object.
    $this->message_plugin = message_get_plugin('message_plugin_og');
  }
}
