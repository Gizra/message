<?php
// $Id: views_handler_filter_node_type.inc,v 1.2 2009/06/02 17:57:48 merlinofchaos Exp $
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
