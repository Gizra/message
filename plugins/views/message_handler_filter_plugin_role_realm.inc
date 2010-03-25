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
class message_handler_filter_plugin_role_realm extends views_handler_filter {
  function query() {
    global $user;

    $this->ensure_my_table();
    // TODO: How how to get table alias?
    $table = $this->query->ensure_table('message_realm');


    $roles = implode(', ', array_keys($user->roles));
    $this->query->add_where($this->options['group'], "message_realm_message.realm_id IN ($roles) AND message_realm_message.realm = 'role'");
  }
}
