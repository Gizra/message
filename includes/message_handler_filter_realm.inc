<?php
// $Id: message_handler_filter_realm.inc,v 1.1 2010/04/08 13:00:49 amitaibu Exp $
/**
 * @file
 * Filter by realm type.
 */
class message_handler_filter_realm extends views_handler_filter_in_operator {
  function get_value_options() {
    if (!isset($this->value_options)) {
      $this->value_title = t('Realm type');
      $this->value_options = message_get_realm_types();
    }
  }
}
