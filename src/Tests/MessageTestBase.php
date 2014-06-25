<?php

/**
 * @file
 * Definition of Drupal\message\Tests\MessageTestBase.
 */

namespace Drupal\message\Tests;

use Drupal\message\Controller\MessageController;
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
   * @var \Drupal\Core\Entity\EntityAccessControllerInterface
   */
  protected $accessController;

  function setUp() {
    parent::setUp();
  }

  /**
   * Create a message text easily.
   *
   * @param String $type
   *  The machine name of the message.
   * @param String $label
   *  The human readable name of the message.
   * @param String $description
   *  The description of the message.
   * @param array $text
   *  The text of the message. Should be text.
   * @param string $langcode
   *  The langcode of the message. Optional.
   *
   * @return \Drupal\message\Entity\MessageType
   *  The message type.
   */
  protected function createMessageType($type, $label, $description, array $text, $langcode = '') {
    $messageType = MessageController::MessageTypeCreate($type);
    $messageType->label = $label;
    $messageType->description = $description;
    $messageType->setText($text, $langcode);
    $messageType->save();

    return $messageType;
  }

  /**
   * Load a message type easily.
   * @param $type
   *  The type of the message.
   *
   * @return \Drupal\message\Entity\MessageType
   *  The message Object.
   */
  protected function loadMessageType($type) {
    return MessageController::MessageTypeLoad($type);
  }

  /**
   * Return a config setting.
   *
   * @param string $config
   *  The config value.
   * @param string $storage
   *  The storing of the configuration. Default to message.message.
   *
   * @return mixed
   *  The value of the config.
   */
  protected function getConfig($config, $storage = 'message.message') {
    return \Drupal::config($storage)->get($config);
  }

  /**
   * Set a config value.
   *
   * @param string $config
   *  The config name.
   * @param string $value
   *  The config value.
   * @param string $storage
   *  The storing of the configuration. Default to message.message.
   */
  protected function configSet($config, $value, $storage = 'message.message') {
    \Drupal::config($storage)->set($config, $value);
  }

}
