<?php
// $Id: message_plugin_og_admin.inc,v 1.1 2010/06/07 10:54:11 amitaibu Exp $

$plugin = array(
  'name' => 'message_plugin_og_admin',
  'realm' => 'og_admin',
  'title' => t('Organic groups admin realm'),
  'description' => t("Determine access by a user's membership and administrator role in a group."),
);

function message_plugin_og_admin_accessible_ids($account) {
  $gids = array();
  foreach ($account->og_groups as $key => $value) {
    if (!empty($value['is_admin'])) {
      $gids[] = $key;
    }
  }
  return $gids;
}
