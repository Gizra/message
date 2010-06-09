<?php
// $Id: message_plugin_user.inc,v 1.1 2010/06/07 10:54:11 amitaibu Exp $

$plugin = array(
  'realm' => 'user',
  'title' => t('User realm'),
  'description' => t("Determine access by a user's ID."),
);

function message_plugin_user_accessible_ids($account) {
  return array($account->uid);
}