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
   * Set additional settings for the message type.
   */
  public function setSettings(array $settings);

  /**
   * Set a single setting by key.
   */
  public function setSetting($key, $setting);

  /**
   * Return the message type settings.
   *
   * @return array
   */
  public function getSettings();

  /**
   * Return a single setting by key.
   *
   * @param $key
   *   The key to return.
   * @param $default_value
   *   The default value to use in case the key is missing. Defaults to NULL.
   *
   * @return mixed
   *   The value of the setting or the default value if none found.
   */
  public function getSetting($key, $default_value = NULL);

  /**
   * Check if the message is new.
   *
   * @return bool
   */
  public function isLocked();

}
