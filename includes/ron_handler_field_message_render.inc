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
class ron_handler_field_message_render extends views_handler_field {

  /**
   * Override base ::query(). The purpose here is to make it possible for the
   * render() method to know two things: what's the content ID, and whether
   * it's flagged.
   */
  function query() {

  }

  function render($values) {
    dpm($values);

  }
}
