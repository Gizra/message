<?php
// $Id$
/**
 * @file
 * Filter realm access by
 */

/**
 * Default argument plugin to extract the users being followed.
 */
class message_handler_filter_realm_user_access_flag extends views_handler_filter_in_operator {
  function get_value_options() {
    if (!isset($this->value_options)) {
      $this->value_title = t('Realm filter by flag');
      $options = array();
      foreach (flag_get_flags() as $flag) {
        $options[$flag->name] = check_plain($flag->title);
      }
      $this->value_options = $options;
    }
  }



  function query() {
    global $user;
    $account = clone($user);

    $this->ensure_my_table();
    $table = $this->table_alias;

    $accessiable_ids = array();
    if (!empty($this->options['value'])) {
      foreach ($this->options['value'] as $flag_name => $value) {
        if ($ids = message_plugin_flag_accessible_ids($account, $flag_name)) {
          $accessiable_ids[$flag_name] = $ids;
        }
      }
    }

    $clause = array();
    if ($accessiable_ids) {
      foreach ($accessiable_ids as $flag_name => $ids) {
        $flag = flag_get_flag($flag_name);
        $realm = $flag->content_type;
        $ids = implode(', ', $ids);

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
