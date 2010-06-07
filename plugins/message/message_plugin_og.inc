<?php
// $Id$

$plugin = array(
  'name' => 'message_plugin_og',
  'realm' => 'og',
  'title' => t('Organic groups realm'),
  'description' => t("Determine access by a user's membership in a group."),
);

function message_plugin_og_accessible_ids($account) {
  return array_keys($account->og_groups);
}
