<?php

/**
 * @file
 * Contains the message field handler.
 */

/**
 * Views field handler for rendering a message.
 *
 * @ingroup views
 */
class message_handler_field_message_render extends views_handler_field {

  function render($values) {
    if (!empty($values->mid) && $message = message_load($values->mid)) {
      return $message->getText(NULL);
    }
  }
}
