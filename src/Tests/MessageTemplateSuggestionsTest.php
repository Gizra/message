<?php

/**
 * @file
 * Contains \Drupal\message\Tests\MessageTemplateSuggestionsTest.
 */

namespace Drupal\message\Tests;
use Drupal\message\Entity\Message;
use Drupal\user\Entity\User;

/**
 * Tests message template suggestions.
 *
 * @group Message
 */
class MessageTemplateSuggestionsTest extends MessageTestBase {

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
   * Tests if template_preprocess_message() generates the correct suggestions.
   */
  function testMessageThemeHookSuggestions() {
    $type = 'dummy_message';
    // Create message to be rendered.
    $message_type = $this->createMessageType($type, 'Dummy message', '', array('[message:author:name]'));
    $message = Message::create(array('type' => $message_type->id()))
      ->setAuthorId($this->user->id());

    $message->save();
    $view_mode = 'full';

    // Simulate theming of the message.
    $build = \Drupal::entityManager()->getViewBuilder('message')->view($message, $view_mode);

    $variables['elements'] = $build;
    $suggestions = \Drupal::moduleHandler()->invokeAll('theme_suggestions_message', array($variables));

    $this->assertEqual($suggestions, array('message__full', 'message__' . $type, 'message__' . $type . '__full', 'message__' . $message->id(), 'message__' . $message->id() . '__full'), 'Found expected message suggestions.');
  }

}
