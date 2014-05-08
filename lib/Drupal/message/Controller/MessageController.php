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
//     entity_create('message_type', array('type' => 'testing', 'label' => 'Testing'))->save();
//     entity_create('message', array('type' => 'bar'))->save();
     $messages = entity_load_multiple('message_type');
     foreach ($messages as $message) {
       $id[] = $message->id() . ' ' .  $message->label();
     }

     return implode("<br />", $id);
  }
}
