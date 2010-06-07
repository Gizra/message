<?php
// $Id: message_plugin_og.inc,v 1.1 2010/06/07 10:54:11 amitaibu Exp $

// Message plugin deceleration.
$plugin = array(
  'name' => 'message_plugin_og',
  'realm' => 'og',
  'title' => t('Organic groups realm'),
  'description' => t("Determine access by a user's membership in a group."),
);

/**
* Get all groups a user belongs to.
*/
function message_plugin_og_accessible_ids($account) {
  return array_keys($account->og_groups);
}