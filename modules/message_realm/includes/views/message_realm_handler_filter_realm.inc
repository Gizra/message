<?php
// $Id: message_handler_filter_realm.inc,v 1.2 2010/06/07 10:54:11 amitaibu Exp $
/**
 * @file
 * Filter by realm type.
 */
class message_realm_handler_filter_realm extends views_handler_filter_in_operator {
  function get_value_options() {
    if (!isset($this->value_options)) {
      $this->value_title = t('Realm type');
      $this->value_options = message_realm_get_realm_types();
    }
  }
}
