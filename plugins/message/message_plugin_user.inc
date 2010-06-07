<?php
// $Id$

$plugin = array(
  'name' => 'message_plugin_user',
  'realm' => 'user',
  'title' => t('User realm'),
  'description' => t("Determine access by a user's ID."),
);

function message_plugin_user_accessible_ids($account) {
  return array($account->uid);
}