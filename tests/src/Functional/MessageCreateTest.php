<?php

namespace Drupal\Tests\message\Functional;

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
   * @var \Drupal\user\Entity\User
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

    $template = 'dummy_message';
    // Create message to be rendered without setting owner.
    $message_template = $this->createMessageTemplate($template, 'Dummy message', '', ['[message:author:name]']);
    $message = Message::create(['template' => $message_template->id()]);

    $message->save();

    /* @var Message $message */
    $this->assertEqual($this->user->id(), $message->getOwnerId(), 'The default value for uid was set correctly.');
  }

}
