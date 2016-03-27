<?php

/**
 * @file
 * Definition of Drupal\message\Tests\MessageTokenTest.
 */

namespace Drupal\message\Tests;

use Drupal\message\Entity\Message;
use Drupal\user\Entity\User;

/**
 * Test the Message and tokens integration.
 *
 * @group Message
 */
class MessageTokenTest extends MessageTestBase {

  /**
   * @var User
   *
   * The user object.
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
   * Test token replacement in a message type.
   */
  public function testTokens() {
    $message_type = $this->createMessageType('dummy_message', 'Dummy message', '', array('[message:author:name]'));
    $message = Message::create(array('type' => $message_type->id()))
      ->setOwner($this->user);

    $message->save();

    $this->assertEqual((string) $message, $this->user->label(), 'The message rendered the author name.');
  }

  /**
   * Test the hard coded tokens.
   */
  public function testHardCodedTokens() {
    $random_text = $this->randomString();
    $token_messages = array(
      'some text @{message:author} ' . $random_text,
      'some text %{message:author} ' . $random_text,
      'some text @{wrong:token} ' . $random_text,
    );

    $replaced_messages = array(
      'some text ' . $this->user->label() . ' ' . $random_text,
      'some text <em class="placeholder">' . $this->user->label() . '</em> ' . $random_text,
      'some text @{wrong:token} ' . $random_text,
    );

    // Create the message type.
    $message_type = $this->createMessageType('dummy_message', 'Dummy message', '', $token_messages);

    // Assert the arguments.
    $message = Message::create(array('type' => $message_type->id()))
      ->setOwner($this->user);

    $this->assertTrue($message->getArguments() == FALSE, 'No message arguments exist prior to saving the message.');
    $message->save();

    $arguments = $message->getArguments();
    $this->assertEqual(count(reset($arguments)), 2, 'Correct number of arguments added after saving the message.');

    // Assert message is rendered as expected.
    $this->assertEqual(implode("\n", $replaced_messages), (string) $message, 'The text is rendered as expected.');
  }
}
