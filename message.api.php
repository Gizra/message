<?php
// $Id:$

/**
 * @file
 * Hooks provided by the Message module.
 *
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter the message instances.
 *
 * @param $message_instances
 *   The loaded message instances passed by reference.
 */
function hook_message_instances_alter(&$message_instances) {
  if ($message_instances->name == 'foo') {
    // Hide the message instance.
    $message_instances->hide = TRUE;
  }

}

/**
 * @} End of "addtogroup hooks".
 */