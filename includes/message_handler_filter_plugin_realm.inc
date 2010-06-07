<?php
// $Id$

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
    $this->message_plugin = message_get_plugin_message($this->definition['message plugin']);
  }

  function can_expose() {
    return FALSE;
  }

  function access() {
    // Don't execute query if user has no access.
    $this->message_accessiable_ids = message_get_accessiable_ids_by($this->message_plugin);
    return $this->message_accessiable_ids;
  }

  function query() {
    global $user;

    $this->ensure_my_table();
    $table = $this->table_alias;

    $ids = implode(', ', $this->message_accessiable_ids);
    $realm = $this->message_plugin['realm'];

    $this->query->add_where($this->options['group'], "$table.realm_id IN ($ids) AND $table.realm = '$realm'");
  }
}
