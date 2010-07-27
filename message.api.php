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
 * Alter the message instances
 *
 * @param $messages
 *   The loaded message instances passed by reference.
 */
function hook_message_instances_alter(&$messages) {
  // Unfiy similar message instances to a single one:
  // - The message name is "create_new_content"
  // - The message itself is "created <a href="@link">@title</a>."
  // - The message itself is "created @content"
  // - The callback of the @content argument returns <a href="foo">bar</a>
  // - We will replace the callback with a value that unifies the messages
  $join = array();
  foreach($messages as $key => $value) {
    if ($value->name == 'create_new_content') {
      $join[$key] = $value;
    }
  }




}

/**
 * @} End of "addtogroup hooks".
 */