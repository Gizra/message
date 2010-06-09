<?php
// $Id: message_plugin_role.inc,v 1.1 2010/06/07 10:54:11 amitaibu Exp $

$plugin = array(
  'realm' => 'role',
  'title' => t('Role realm'),
  'description' => t("Determine access by a user's role."),
);

function message_plugin_role_accessible_ids($account) {
  return array_keys($account->roles);
}