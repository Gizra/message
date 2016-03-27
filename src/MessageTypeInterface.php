<?php

/**
 * @file
 * Contains \Drupal\message\MessageTypeInterface.
 */

namespace Drupal\message;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Language\Language;

/**
 * Provides an interface defining a Message type entity.
 */
interface MessageTypeInterface extends ConfigEntityInterface {

  /**
   * Set internal data array.
   *
   * @param array $data
   *
   * @return $this
   */
  public function setData(array $data);

  /**
   * @param string $key
   *   (optional) Key from the array.
   *
   * @return array
   *   A subset of data if `$key` is passed, otherwise the entire data array.
   */
  public function getData($key = '');

  /**
   * Set the message type description.
   *
   * @param string $description
   *
   * @return $this
   */
  public function setDescription($description);

  /**
   * Get the message type description.
   *
   * @return string
   */
  public function getDescription();

  /**
   * Set the message type label.
   *
   * @param string $label
   *
   * @return $this
   */
  public function setLabel($label);

  /**
   * Get the message type label.
   *
   * @return string
   */
  public function getLabel();

  /**
   * Set the message type.
   *
   * @param string $type
   *
   * @return $this
   */
  public function setType($type);

  /**
   * Get the message type.
   *
   * @return string
   */
  public function getType();

  /**
   * @param string $uuid
   *
   * @return $this
   */
  public function setUuid($uuid);

  /**
   * @return string
   */
  public function getUuid();

  /**
   * Retrieves the configured message text in a certain language.
   *
   * @param string $langcode
   *   The language code of the Message text field, the text should be
   *   extracted from.
   * @param array $options
   *   Array of options to pass to the metadata-wrapper:
   *   - 'delta': Optional; If set, returns the output only from a single delta
   *     of the message-text field.
   *
   * @todo: change this to something else.
   *
   * @return array
   *   An array of the text field values.
   */
  public function getText($langcode = Language::LANGCODE_NOT_SPECIFIED, $delta = NULL);

  /**
   * Check if the message is new.
   *
   * @return bool
   */
  public function isLocked();

}
