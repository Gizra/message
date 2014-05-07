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
     // todo: try to create message type.
//     $type = entity_create('message_type', array('bar' => 'test', 'id' => 1))->save();

     entity_create('message', array('type' => 'bar'))->save();
     $messages = entity_load_multiple('message');
     foreach ($messages as $message) {
       $id[] = $message->id() . ' ' .  $message->bundle();
     }

     return implode("<br />", $id);
  }
}
