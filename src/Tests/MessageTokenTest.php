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
   */
  private $user;

  /**
   * {@inheritdoc}
   */
  function setUp() {
    parent::setUp();

    $this->user = $this->drupalcreateuser();
  }

  /**
   * Test token replacement in a message type.
   */
  function testTokens() {
    $messageType = $this->createMessageType('dummy_message', 'Dummy message', '', array('[message:author:name]'));
    $message = Message::create(array('type' => $messageType->id()))
      ->setAuthorId($this->user->id());

    $message->save();

    $this->assertEqual($message->getText(), $this->user->label(), 'The message rendered the author name.');
  }

  /**
   * Test the hard coded tokens.
   */
  public function testHardCodedTokens() {
    $random_text = $this->randomString();
    $token_messages = array(
      'some text @{message:author} ' . $random_text,
      'some text !{message:author} ' . $random_text,
      'some text %{message:author} ' . $random_text,
      'some text !{wrong:token} ' . $random_text
    );

    $replaced_messages = array(
      'some text ' . $this->user->label() . ' ' . $random_text,
      'some text ' . $this->user->label() . ' ' . $random_text,
      'some text <em class="placeholder">' . $this->user->label() . '</em> ' . $random_text,
      'some text !{wrong:token} ' . $random_text
    );

    // Create the message type.
    $messageType = $this->createMessageType('dummy_message', 'Dummy message', '', $token_messages);

    // Assert the arguments.
    $message = Message::create(array('type' => $messageType->id()))
      ->setAuthorId($this->user->id());

    $this->assertTrue($message->getArguments() == FALSE, 'No message arguments exist prior to saving the message.');
    $message->save();
    $this->assertEqual(count($message->getArguments()), 3, 'Correct number of arguments added after saving the message.');

    // Assert message is rendered as expected.
    $this->assertEqual(implode("\n", $replaced_messages), $message->getText(), 'The text rendered as expected.');
  }
}
