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
   * Create a message type.
   *
   * @param String $type
   *  The message type.
   *
   * @return MessageType
   *  A message type object ready to be save.
   *
   * todo: Move to MessageType::create().
   */
  public static function MessageTypeCreate($type) {
    return entity_create('message_type', array('type' => $type));
  }

  /**
   * Loading a message type.
   *
   * @param String $type
   *  The message type.
   *
   * @return MessageType
   *
   * todo: Move to MessageType::load().
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
   *
   *    * todo: Move to MessageType::loadMultiple().
   */
  public static function MessageTypeLoadMultiple(array $types) {
    return entity_load_multiple('message_type', $types);
  }

  /**
   * Create a message.
   *
   * @param Array $values
   *  The message type.
   *
   * @return Message
   *  A message type object ready to be save.
   *
   * todo: Move to Message::create().
   */
  public static function MessageCreate($values) {
    return entity_create('message', $values);
  }

  /**
   * Load a message.
   *
   * @param Integer $id
   *  The message ID.
   *
   * @return Message
   *
   * todo: Move to Message::load().
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
   *
   * todo: Move to Message::loadMultiple().
   */
  public static function MessageLoadMultiple(array $ids) {
    return entity_load_multiple('message', $ids);
  }

  /**
   * Delete multiple message.
   *
   * @param $ids
   *  The messages IDs.
   *
   * todo: Move to Message::deleteMultiple().
   */
  public static function MessageDeleteMultiple($ids) {
    \Drupal::entityManager()->getStorage('message')->delete($ids);
  }

  /**
   * Delete after finishing with the message arguemnts.
   */
  public static function timeArguments($timestamp) {
    return date('d-m-Y H:i', $timestamp);
  }
}
