<?php
// $Id:$

/**
 * @file
 * Field handler to translate a realm type into its readable form.
 */
class message_handler_field_realm extends views_handler_field {
  function render($values) {

    return $this->realm_type($values->{$this->field_alias});
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

