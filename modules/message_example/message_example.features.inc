<?php
// $Id: message_example.features.inc,v 1.1 2010/04/02 20:01:17 amitaibu Exp $

/**
 * Implementation of hook_ctools_plugin_api().
 */
function message_example_ctools_plugin_api() {
  module_load_include('inc', 'message_example', 'message_example.defaults');
  $args = func_get_args();
  return call_user_func_array('_message_example_ctools_plugin_api', $args);
}

/**
 * Implementation of hook_message_default_messages().
 */
function message_example_message_default_messages() {
  module_load_include('inc', 'message_example', 'message_example.defaults');
  $args = func_get_args();
  return call_user_func_array('_message_example_message_default_messages', $args);
}

/**
 * Implementation of hook_rules_defaults().
 */
function message_example_rules_defaults() {
  module_load_include('inc', 'message_example', 'message_example.defaults');
  $args = func_get_args();
  return call_user_func_array('_message_example_rules_defaults', $args);
}
