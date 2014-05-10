<?php

/**
 * @file
 * Contains \Drupal\message\Controller\MessageController.
 */

namespace Drupal\message\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\message\Entity\MessageType;
use Drupal\message\Entity\Message;

/**
 * Returns responses for System Info routes.
 */
class MessageController extends ControllerBase {

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

  /**
   * Load message type multiple.
   *
   * @param Array $types
   *  Array of types.
   *
   * @return MessageType[]
   *  Array of message types.
   */
  public static function MessageTypeLoadMultiple(array $types) {
    return entity_load_multiple('message_type', $types);
  }

  /**
   * Load a message.
   *
   * @param Integer $id
   *  The message ID.
   *
   * @return Message
   */
  public static function MessageLoad($id) {
    return entity_load('message', $id);
  }

  /**
   * Load a message.
   *
   * @param Array $ids
   *  The message ID.
   *
   * @return Message[]
   */
  public static function MessageLoadMultiple(array $ids) {
    return entity_load('message', $ids);
  }
}
