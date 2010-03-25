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
class message_handler_filter_message_plugin extends views_handler_filter {
  function construct() {
    parent::construct();
    $this->additional_fields['uid'] = array('table' => 'users', 'field' => 'uid');
  }

  function query() {
    global $user;
    $roles = array_keys($user->roles);
    $placeholders = db_placeholders($roles);
    $this->query->add_where($this->options['group'], 'users.uid IN ('. $roles .')');
  }
}
