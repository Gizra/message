<?php
// $Id$

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
  // Simple example:
  if ($message_instances->name == 'foo') {
    // Hide the message instance.
    $message_instances->hide = TRUE;
  }

  // Advanced example:
  // Unfiy similar message instances to a single one:
  // - The message name is "create_new_content"
  // - The message itself is "created <a href="@link">@title</a>."
  // - The callback of the @content argument returns <a href="foo">bar</a>
  // - We will replace the callback with a value that unifies the messages
  //   and override the original message with a new one.
  $join = array();
  foreach($message_instances as $key => $value) {
    if ($value->name == 'create_new_content') {
      $join[$key] = $value;
    }
  }

  // Check if we have several similar message instances.
  if (count($join) > 1) {
    // Create our value with all the titles, instead of the single title, replace
    // it with the callback of the first message instance, and remove all the
    // other similar message instances.
    $join_message = array();
    foreach ($join as $value) {
      $link = call_user_func_array($value->arguments['@link']['callback'], $value->arguments['@link']['callback arguments']);
      $title = $value->arguments['@title'];
      $join_message[] = '<a href="'. $link .'">' . $title . '</a>';
    }

    $first = TRUE;
    // Keep the first message, and remove all the other. Override the message
    // as-well.
    foreach ($join as $key => $value) {
      if ($first) {
        $message_instances[$key]->arguments = array();
        $message_instances[$key]->arguments['!override_arguments'] = implode(', ', $join_message);
        // If i18n module is enabled, this message will also be registered for
        // translation.
        // The name "multiple" will be appended to the original name, resulting
        // with "create_new_content_multiple".
        message_override_message_instance($message_instances[$key], 'multiple', 'created !override_arguments');
        $first = FALSE;
      }
      else {
        // Hide message, instead of removing it, so it remains statically
        // cahced.
        $message_instances[$key]->hide = TRUE;
      }
    }
  }

}

/**
 * @} End of "addtogroup hooks".
 */