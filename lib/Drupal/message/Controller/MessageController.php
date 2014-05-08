<?php

/**
 * @file
 * Contains \Drupal\message\Controller\MessageController.
 */

namespace Drupal\message\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for System Info routes.
 */
class MessageController extends ControllerBase {

  /**
   * This is the sandbox page. Here we will all the thing we need to do during
   * the message develoment to D8.
   */
  public function page() {
    // Create type.
    if (!$type = entity_load('message_type', 'testing')) {
      $type = entity_load('message_type', array('type' => 'testing', 'label' => 'Testing'));
      $type->save();
    }

    entity_create('message', array('type' => 'testing', 'arguments' => array('time' => time())))->save();
    $messages = entity_load_multiple('message');

    foreach ($messages as $message) {
      $id[] = $message->id();
    }

    return implode("<br />", $id);
  }
}
