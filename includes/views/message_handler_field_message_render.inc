<?php
// $Id: message_handler_field_message_render.inc,v 1.2 2010/06/07 10:54:11 amitaibu Exp $

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
      $output = $message->view();
      // Render only the "message_render", making sure other fields added to
      // the entity aren't printed as well.
      return render($output['message'][$message->mid]['message_render']);
    }
  }
}
