<?php
// $Id$

if (module_exists('og')) {
  // Message plugin deceleration.
  $plugin = array(
    'realm' => 'og',
    'title' => t('Organic groups realm'),
    'description' => t("Determine access by a user's membership in a group."),
  );
}

/**
* Get all groups a user belongs to.
*/
function message_plugin_og_accessible_ids($account) {
  return !empty($account->og_groups) ? array_keys($account->og_groups) : array();
}