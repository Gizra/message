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
   *   The Unix timestamp.
   *
   * @return $this
   */
  public function setCreatedTime($timestamp);

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
   *   The arguments of the message.
   */
  public function getArguments();

  /**
   * Set the arguments of the message.
   *
   * @param array $values
   *   Array of arguments.
   * @code
   *   $values = array(
   *     '@name_without_callback' => 'John doe',
   *     '@name_with_callback' => array(
   *       'callback' => 'User::load',
   *       'arguments' => array(1),
   *     ),
   *   );
   * @endcode
   *
   * @return $this
   */
  public function setArguments(array $values);

  /**
   * Set the language that should be used.
   *
   * @param string $language
   *   The language to load from the message type when fetching the text.
   */
  public function setLanguage($language);

  /**
   * Replace arguments with their placeholders.
   *
   * @param $langcode
   *   The language code.
   * @param NULL|int $delta
   *   The delta of the message to return. If NULL all the message text will be
   *   returned.
   *
   * @return array
   *   The message text.
   */
  public function getText($langcode = Language::LANGCODE_NOT_SPECIFIED, $delta = NULL);

  /**
   * Delete multiple message.
   *
   * @param array $ids
   *   The messages IDs to delete.
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

  /**
   * Convert message contents to a string.
   *
   * @return string
   *   The message contents.
   */
  public function __toString();

}
