<?php

$plugin = array(
  'title' => t('User realm'),
  'description' => t("Determine access by a user's ID."),
);

function message_plugin_user_accessible_ids($account) {
  return array($account->uid);
}