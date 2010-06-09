<?php
// $Id$

$plugin = array(
  'title' => t('Base plugin'),
  'description' => '',
);

function message_plugin_access($plugin, $message_instance, $realm_ids, $account) {
  $access = FALSE;
  if ($plugin['realm'] && $realm_ids && $accessibale_ids = call_user_func($plugin['access']['accessible ids callback'], $account)) {
    $access = array_intersect($accessibale_ids, $realm_ids);
  }
  return $access;
}

function message_plugin_accessible_ids($account) {
  return array();
}
