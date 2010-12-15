<?php
// $Id$

$plugin = array(
  'title' => t('Role realm'),
  'description' => t("Determine access by a user's role."),
);

function message_plugin_role_accessible_ids($account) {
  return array_keys($account->roles);
}