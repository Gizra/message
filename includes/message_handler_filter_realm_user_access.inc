<?php
// $Id: message_handler_filter_plugin_realm.inc,v 1.1 2010/06/07 10:54:11 amitaibu Exp $

/**
 * @file
 * Contains the base message filter handler.
 */

/**
 * Base handler to filter realm.
 *
 * @ingroup views
 */
class message_handler_filter_realm_user_access extends views_handler_filter_in_operator {
  function get_value_options() {
    if (!isset($this->value_options)) {
      $this->value_title = t('Realm type');
      $this->value_options = message_get_realm_types();
    }
  }



  function query() {
    global $user;

    $this->ensure_my_table();
    $table = $this->table_alias;

    $accessiable_ids = array();
    if (!empty($this->options['value'])) {
      foreach ($this->options['value'] as $plugin_name => $value) {
        $plugin = message_get_plugin_message($plugin_name);
        // Get all the relms and realm IDs a user can access.
        if ($ids = message_get_accessiable_ids_by($plugin)) {
          $accessiable_ids[$plugin_name] = $ids;
        }
      }
    }
    $clause = array();
    if ($accessiable_ids) {
      foreach ($accessiable_ids as $plugin_name => $ids) {
        $plugin = message_get_plugin_message($plugin_name);
        $ids = implode(', ', $ids);
        $realm = $plugin['realm'];

        $clause[] = "($table.realm_id IN ($ids) AND $table.realm = '$realm')";
      }
    }
    else {
      // Make sure user doesn't see all existing records.
      // TODO: Is there a better way?
      $clause[] = 'TRUE = FALSE';
    }

    $clause = implode(' OR ', $clause);
    $this->query->add_where($this->options['group'], $clause);
  }
}
