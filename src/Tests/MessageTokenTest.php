<?php

/**
 * @file
 * Definition of Drupal\message\Tests\MessageTokenTest.
 */

namespace Drupal\message\Tests;

use Drupal\message\Entity\Message;
use Drupal\message\Entity\MessageType;
use Drupal\user\Entity\User;

/**
 * Test the Message and tokens integration.
 */
class MessageTokenTest extends MessageTestBase {

  /**
   * @var User
   */
  private $user;

  /**
   * @var MessageType
   */
  private $messageType;

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Message tokens',
      'description' => 'Test the Message and tokens integration.',
      'group' => 'Message',
    );
  }

  /**
   * {@inheritdoc}
   */
  function setUp() {
    parent::setUp();

    $this->user = $this->drupalcreateuser();
    $this->messageType = $this->createMessageType('dummy_message', 'Dummy message', '', array('[message:author:name]'));
  }

  /**
   * Test token replacement in a message type.
   */
  function testTokens() {

    $message = Message::create(array('type' => $this->messageType->id()))
      ->setAuthorId($this->user->id());

    $message->save();

    $this->assertEqual($message->getText(), $this->user->label(), 'The message rendered the author name.');
  }
}
