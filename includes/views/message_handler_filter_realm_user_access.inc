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
    $plugins = message_get_plugin_messages();
    if (!empty($this->options['value'])) {
      foreach ($this->options['value'] as $plugin_name => $value) {
        $plugin = $plugins[$plugin_name];
        // Get all the relms and realm IDs a user can access.
        if ($ids = message_get_accessiable_realm_ids_by_plugin($plugin)) {
          $accessiable_ids[$plugin_name] = $ids;
        }
      }
    }
    $clause = array();
    if ($accessiable_ids) {
      foreach ($accessiable_ids as $plugin_name => $ids) {
        $ids = implode(', ', $ids);

        $realm_field = $table . '.' . $this->real_field;
        $realm_id_field = $table . '.message_realm_realm_id';

        $clause[] = "($realm_field IN ($ids) AND $realm_id_field = '$plugin_name')";
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
