<?php
// $Id: message_handler_filter_plugin_realm.inc,v 1.2 2010/04/24 14:17:20 amitaibu Exp $

/**
 * @file
 * Contains the base message filter handler.
 */

/**
 * Base handler to filter realm.
 *
 * @ingroup views
 */
class message_handler_filter_plugin_realm extends views_handler_filter {
  function init(&$view, $options) {
    parent::init(&$view, $options);
    // Get the plugin realm into the Views object.
    // @see message_handler_filter_plugin_realm::query().
    $this->message_plugin = message_get_plugin('message_plugin');
  }

  function can_expose() {
    return FALSE;
  }

  function access() {
    // Don't execute query if user has no access.
    return $this->message_plugin->get_accessible_id_array();
  }

  function query() {
    global $user;

    $this->ensure_my_table();
    // TODO: How how to get table alias?
    // $table = $this->query->ensure_table('message_realm');

    $table = 'message_realm_message_instance';

    $ids = implode(', ', $this->message_plugin->get_accessible_id_array());
    $realm = $this->message_plugin->plugin['realm'];

    $this->query->add_where($this->options['group'], "$table.realm_id IN ($ids) AND $table.realm = '$realm'");
  }
}
