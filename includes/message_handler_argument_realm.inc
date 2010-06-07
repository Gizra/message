<?php
// $Id: message_handler_argument_realm.inc,v 1.2 2010/06/07 10:54:11 amitaibu Exp $
/**
 * @file
 * Argument handler to accept a realm.
 */

class message_handler_argument_realm extends views_handler_argument {
  function construct() {
    parent::construct('realm');
  }

  /**
   * Override the behavior of summary_name(). Get the user friendly version
   * of the realm.
   */
  function summary_name($data) {
    return $this->realm_type($data->{$this->name_alias});
  }

  /**
   * Override the behavior of title(). Get the user friendly version of the
   * realm.
   */
  function title() {
    return $this->realm_type($this->argument);
  }

  function realm_type($realm) {
    $output = '';
    ctools_include('plugins');
    $plugins = ctools_get_plugins('message', 'plugins');
    foreach ($plugins as $key => $value) {
      if ($value['realm'] == $realm) {
        $output = check_plain($value['title']);
        break;
      }
    }

    if (empty($output)) {
      return t('Unknown realm');
    }
    else {
      return $output;
    }
  }
}
