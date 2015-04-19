<?php

/**
 * @file
 * Definition of Drupal\message\Tests\MessageTestBase.
 */

namespace Drupal\message\Tests;

use Drupal\message\Entity\MessageType;
use Drupal\simpletest\WebTestBase;

/**
 * Holds set of tools for the message testing.
 */
abstract class MessageTestBase extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('message');

  /**
   * The node access controller.
   *
   * @var \Drupal\Core\Entity\EntityAccessControlHandlerInterface
   */
  protected $accessController;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Create a message text easily.
   *
   * @param string $type
   *   The machine name of the message.
   * @param string $label
   *   The human readable name of the message.
   * @param string $description
   *   The description of the message.
   * @param array $text
   *   The text of the message. Should be text.
   * @param string $langcode
   *   The langcode of the message. Optional.
   *
   * @return \Drupal\message\Entity\MessageType
   *  The message type.
   */
  protected function createMessageType($type, $label, $description, array $text, $langcode = '') {
    $message_type = MessageType::Create(array(
      'type' => $type,
      'label' => $label,
      'description' => $description,
      'text' => $text,
    ));
    $message_type->save();
    return $message_type;
  }

  /**
   * Load a message type easily.
   *
   * @param string $type
   *   The type of the message.
   *
   * @return \Drupal\message\Entity\MessageType
   *   The message Object.
   */
  protected function loadMessageType($type) {
    return MessageType::load($type);
  }

  /**
   * Return a config setting.
   *
   * @param string $config
   *   The config value.
   * @param string $storage
   *   The storing of the configuration. Default to message.message.
   *
   * @return mixed
   *   The value of the config.
   */
  protected function getConfig($config, $storage = 'message.settings') {
    return \Drupal::config($storage)->get($config);
  }

  /**
   * Set a config value.
   *
   * @param string $config
   *   The config name.
   * @param string $value
   *   The config value.
   * @param string $storage
   *   The storing of the configuration. Default to message.message.
   */
  protected function configSet($config, $value, $storage = 'message.settings') {
    \Drupal::configFactory()->getEditable($storage)->set($config, $value);
  }
}
