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
class message_handler_filter_plugin_realm_og extends views_handler_filter {
  function query() {
    global $user;

    if ($gids = implode(', ', array_keys($user->og_groups))) {
      $this->query->add_where($this->options['group'], "message_realm_message.realm_id IN ($gids) AND message_realm_message.realm = 'og'");
    }
  }
}
