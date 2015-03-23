<?php

/**
 * @file
 * Defines drush commands.
 */


/**
 * Implements hook_drush_command().
 */
function message_drush_command() {
  $items = array();
  $items['message-purge'] = array(
    'description' => t('Batch purge old messages (will only affect messages that are eligible for purging based on message settings).'),
    'arguments' => array(
      'purge_limit' => 'The total number of messages to purge. Defaults to 10,000. Set to -1 to purge all eligible messages (warning: this can cause memory limit errors for very large sets).',
      'loop_limit' => 'The number of messages to purge per loop. Defaults to 100.',
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_FULL,
    'aliases' => array('purge-messages'),
    'drupal dependencies' => array('message'),
    'callback' => 'drush_message_purge',
  );
  return $items;
}

/**
 * Drush command to batch purge old messages.
 */
function drush_message_purge() {
  drush_print("Starting message purge.");

  // Get drush params.
  $args = drush_get_arguments();
  $purge_limit = MESSAGE_PURGE_LIMIT_DRUSH_DEFAULT;
  $loop_limit = MESSAGE_PURGE_LOOP_DRUSH_DEFAULT;
  if (isset($args[1]) && is_numeric($args[1])) {
    $purge_limit = $args[1];
  }
  if (isset($args[2]) && is_numeric($args[2])) {
    $loop_limit = $args[2];
  }
  if ($purge_limit > 0) {
    drush_print("Purge Limit: $purge_limit");
  }
  else {
    drush_print("Purge Limit: no limit (purge all eligible messages)");
  }
  drush_print("Loop Limit: $loop_limit");

  $completed = 0;
  do {
    // Prevent over-purging.
    if ($purge_limit > 0 && $completed + $loop_limit > $purge_limit) {
      $loop_limit = $purge_limit - $completed;
    }

    // Do the actual purging.
    $purge_count = message_purge($loop_limit);

    // Stop if there are no more messages to purge.
    if ($purge_count == 0) {
      drush_print("No more messages to purge.");
      return;
    }

    // Provide feedback on progress.
    $completed += $purge_count;
    $percent_completed = round(100 * $completed/$purge_limit);
    if ($purge_limit > 0) {
      drush_print("Completed $completed of $purge_limit purges ($percent_completed%).");
    }
    else {
      drush_print("Completed $completed purges.");
    }

    // Stop when we reach purge limit.
    if ($purge_limit > 0 && $completed >= $purge_limit) {
      drush_print("Purge limit reached.");
      return;
    }
  } while (TRUE);
}