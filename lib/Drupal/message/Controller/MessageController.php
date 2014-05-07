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
     $type = entity_create('message_type', array('name' => 'bar'))->save();
//     $foo = entity_create('message', array('type' => 'bar'));
//     $foo->save();

     entity_load('message');
  }
}
