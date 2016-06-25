<?php

/**
 * @file
 * Contains \Drupal\message\Tests\MessageCreateTest.
 */

namespace Drupal\message\Tests;

use Drupal\message\Entity\Message;
use Drupal\user\Entity\User;

/**
 * Tests message creation and default values.
 *
 * @group Message
 */
class MessageCreateTest extends MessageTestBase {

  /**
   * The user object.
   *
   * @var User
   */
  private $user;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->user = $this->drupalcreateuser();
  }

  /**
   * Tests if message create sets the default uid to currently logged in user.
   */
  public function testMessageCreateDefaultValues() {
    // Login our user to create message.
    $this->drupalLogin($this->user);

    $type = 'dummy_message';
    // Create message to be rendered without setting owner.
    $message_type = $this->createMessageType($type, 'Dummy message', '', ['[message:author:name]']);
    $message = Message::create(['type' => $message_type->id()]);

    $message->save();

    /* @var Message $message */
    $this->assertEqual($this->user->id(), $message->getOwnerId(), 'The default value for uid was set correctly.');
  }

}
