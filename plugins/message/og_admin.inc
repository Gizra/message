<?php
// $Id$

if (module_exists('og')) {
  // Message plugin deceleration.
  $plugin = array(
    'title' => t('Organic groups admin realm'),
    'description' => t("Determine access by a user's membership and administrator role in a group."),
  );
}

function message_plugin_og_admin_accessible_ids($account) {
  $gids = array();
  foreach ($account->og_groups as $key => $value) {
    if (!empty($value['is_admin'])) {
      $gids[] = $key;
    }
  }
  return $gids;
}
