<?php
// $Id: $

/**
 * @file
 * Contains the flag Ops field handler.
 */

/**
 * Views field handler for the Flag operations links (flag/unflag).
 *
 * @ingroup views
 */
class message_handler_field_message_render extends views_handler_field {
  function render($values) {
    if (!empty($values->iid)) {
      return message_show_message($values->iid);
    }
  }
}
