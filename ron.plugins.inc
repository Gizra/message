<?php
// $Id: context.plugins.inc,v 1.1.2.4 2010/02/09 17:05:22 yhahn Exp $

/**
 * Context plugins.
 */
function _ron_ron_plugins() {
  $plugins = array();

  $plugins['ron_plugin'] = array(
    'handler' => array(
      'path' => drupal_get_path('module', 'ron') .'/plugins',
      'file' => 'ron_plugin.inc',
      'class' => 'ron_plugin',
    ),
  );

  $plugins['ron_plugin_role'] = array(
    'handler' => array(
      'path' => drupal_get_path('module', 'ron') .'/plugins',
      'file' => 'ron_plugin_role.inc',
      'class' => 'ron_plugin_role',
      'parent' => 'ron_plugin',
    ),
  );

  return $plugins;
}
