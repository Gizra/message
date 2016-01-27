<?php

/**
 * @file
 * Contains \Drupal\message\MessageInterface.
 */

namespace Drupal\message;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Language\Language;

/**
 * Provides an interface defining a Message entity.
 */
interface MessageInterface extends ContentEntityInterface {

  /**
   * Set the message type.
   *
   * @param \Drupal\message\MessageTypeInterface $type
   *
   * @return $this
   */
  public function setType(MessageTypeInterface $type);

  /**
   * Get the type of the message type.
   *
   * @return \Drupal\message\MessageTypeInterface
   */
  public function getType();

  /**
   * Retrieve the time stamp of the message.
   *
   * @return int
   *   The Unix timestamp.
   */
  public function getCreatedTime();

  /**
   * Setting the timestamp.
   *
   * @param int $timestamp
   *  The Unix timestamp.
   *
   * @return $this
   */
  public function setCreatedTime($timestamp);

  /**
   * Retrieve the message owner object.
   *
   * @return \Drupal\user\UserInterface
   *  The user object.
   */
  public function getAuthor();

  /**
   * Retrieve the author ID.
   *
   * @return int
   *  The author ID.
   */
  public function getAuthorId();

  /**
   * Set the author ID.s
   *
   * @param int $uid
   *  The user ID.
   *
   * @return $this
   *  The message object.
   */
  public function setAuthorId($uid);

  /**
   * Return the UUID.
   *
   * @return string
   */
  public function getUUID();

  /**
   * Retrieve the message arguments.
   *
   * @return array
   *  The arguments of the message.
   */
  public function getArguments();

  /**
   * Set the arguments of the message.
   *
   * @param array $values
   *   Array of arguments.
   *   @code
   *   $values = array(
   *     '@name_without_callback' => 'John doe',
   *     '@name_with_callback' => array(
   *       'callback' => 'User::load',
   *       'arguments' => array(1),
   *     ),
   *   );
   *  @endcode
   *
   * @return $this
   */
  public function setArguments(array $values);

  /**
   * Replace arguments with their placeholders.
   *
   * @param $langcode
   *   Optional; The language to get the text in. If not set the current language
   *   will be used.
   * @param $options
   *   Optional; Array to be passed to MessageType::getText().
   *
   * @return string
   *  The message text.
   */
  public function getText($langcode = Language::LANGCODE_NOT_SPECIFIED, array $options = array());

  /**
   * Delete multiple message.
   *
   * @param array $ids
   *  The messages IDs to delete.
   */
  public static function deleteMultiple($ids);

  /**
   * Run a EFQ over messages from a given type.
   *
   * @param string $type
   *   The entity type.
   *
   * @return array
   *   Array of message IDs.
   */
  public static function queryByType($type);
}
