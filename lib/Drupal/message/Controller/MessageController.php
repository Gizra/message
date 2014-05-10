<?php

/**
 * @file
 * Contains \Drupal\message\Controller\MessageController.
 */

namespace Drupal\message\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\message\Entity\MessageType;

/**
 * Returns responses for System Info routes.
 */
class MessageController extends ControllerBase {

  /**
   * This is the sandbox page. Here we will all the thing we need to do during
   * the message develoment to D8.
   */
  public function page() {
    $types = array(
      array(
        'type' => 'testing',
        'label' => 'Testing',
        'text' => array(
          'this is testing. created at @time',
        ),
      ),
      array(
        'type' => 'foo',
        'label' => 'Foo',
        'text' => array(
          'this is testing. created by @name',
        ),
      ),
      array(
        'type' => 'bar',
        'label' => 'Bar',
        'text' => array(
          'this is testing. created by @name',
        ),
      ),
      array(
        'type' => 'roy',
        'label' => 'Roy',
        'text' => array(
          'this is testing. created by @name',
        ),
      ),
      array(
        'type' => 'segall',
        'label' => 'Segall',
        'text' => array(
          'this is testing. created by @name',
        ),
      ),
    );

    foreach ($types as $type) {
      // Create type.
      if (!$message_type = entity_load('message_type', $type['type'])) {
        $message_type = entity_create('message_type', $type);
        $message_type->save();
      }
    }

    $message_types = entity_load_multiple('message_type');

    $output[] = "<h2>" . t('Message types') . "</h2>";

    foreach ($message_types as $message_type) {
      $output[] = $message_type->id() . ' ' . $message_type->label() . ' ' .  $message_type->getText();
    }

    entity_create('message', array('type' => 'testing', 'arguments' => array('time' => time())))->save();
    $messages = entity_load_multiple('message');

    $output[] = "<h2>" . t('Message IDs') . "</h2>";
    foreach ($messages as $message) {
      $output[] = $message->id();
    }

    return implode("<br />", $output);
  }

  /**
   * Loading a message type.
   *
   * @param String $type
   *  The message type.
   *
   * @return MessageType
   */
  public static function MessageTypeLoad($type) {
    return entity_load('message_type', $type);
  }
}
