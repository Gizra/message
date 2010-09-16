<?php

/**
 * Implementation of hook_ctools_plugin_api().
 */
function message_flag_example_ctools_plugin_api() {
  module_load_include('inc', 'message_flag_example', 'message_flag_example.defaults');
  $args = func_get_args();
  return call_user_func_array('_message_flag_example_ctools_plugin_api', $args);
}

/**
 * Implementation of hook_flag_default_flags().
 */
function message_flag_example_flag_default_flags() {
  module_load_include('inc', 'message_flag_example', 'message_flag_example.defaults');
  $args = func_get_args();
  return call_user_func_array('_message_flag_example_flag_default_flags', $args);
}

/**
 * Implementation of hook_message_default_messages().
 */
function message_flag_example_message_default_messages() {
  module_load_include('inc', 'message_flag_example', 'message_flag_example.defaults');
  $args = func_get_args();
  return call_user_func_array('_message_flag_example_message_default_messages', $args);
}

/**
 * Implementation of hook_views_default_views().
 */
function message_flag_example_views_default_views() {
  module_load_include('inc', 'message_flag_example', 'message_flag_example.features.views');
  $args = func_get_args();
  return call_user_func_array('_message_flag_example_views_default_views', $args);
}
