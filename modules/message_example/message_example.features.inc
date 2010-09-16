<?php

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
