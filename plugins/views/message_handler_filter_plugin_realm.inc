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
class message_handler_filter_plugin_realm extends views_handler_filter {
  function init(&$view, $options) {
    parent::init(&$view, $options);
    // Get the plugin realm into the Views object.
    $this->message_plugin = message_get_plugin('message_plugin');
  }

  function query() {
    global $user;

    $this->ensure_my_table();
    // TODO: How how to get table alias?
    $table = $this->query->ensure_table('message_realm');

    $ids = implode(', ', $this->message_plugin->get_accessibale_id_array());
    $realm = $this->message_plugin->realm;

    $this->query->add_where($this->options['group'], "message_realm_message_instance.realm_id IN ($ids) AND message_realm_message.realm = '$realm'");
  }
}
