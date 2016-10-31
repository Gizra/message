<?php

namespace Drupal\Tests\message\Kernel;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Language\Language;
use Drupal\KernelTests\KernelTestBase;
use Drupal\message\Entity\Message;
use Drupal\message\MessageInterface;
use Drupal\simpletest\UserCreationTrait;

/**
 * Kernel tests for the Message entity.
 *
 * @group Message
 *
 * @coversDefaultClass \Drupal\message\Entity\Message
 */
class MessageTest extends KernelTestBase {

  use MessageTemplateCreateTrait;
  use UserCreationTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['filter', 'message', 'user', 'system'];

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * A message template to test with.
   *
   * @var \Drupal\message\MessageTemplateInterface
   */
  protected $messageTemplate;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->installConfig(['filter']);
    $this->installEntitySchema('message');
    $this->installEntitySchema('user');
    $this->installSchema('system', ['sequences']);
    $this->entityTypeManager = $this->container->get('entity_type.manager');
    $this->messageTemplate = $this->createMessageTemplate(Unicode::strtolower($this->randomMachineName()), $this->randomString(), $this->randomString(), []);
  }

  /**
   * Tests attempting to create a message without a template.
   *
   * @expectedException \Drupal\message\MessageException
   */
  public function testMissingTemplate() {
    $message = Message::create(['template' => 'missing']);
    $message->save();
  }

  /**
   * Tests getting the user.
   */
  public function testGetOwner() {
    $message = Message::create(['template' => $this->messageTemplate->id()]);
    $account = $this->createUser();
    $message->setOwner($account);
    $this->assertEquals($account->id(), $message->getOwnerId());

    $owner = $message->getOwner();
    $this->assertEquals($account->id(), $owner->id());
  }

  /**
   * Tests for getText.
   *
   * @covers ::getText
   */
  public function testGetText() {
    // Test with missing message template.
    $message = $this->entityTypeManager->getStorage('message')->create(['template' => 'no_exists']);
    $this->assertEmpty($message->getText());

    // Non-existent delta.
    $message = $this->entityTypeManager->getStorage('message')->create(['template' => $this->messageTemplate->id()]);
    $this->assertEmpty($message->getText(Language::LANGCODE_NOT_SPECIFIED, 123));

    // Verify token clearing disabled.
    $this->messageTemplate->setSettings([
      'token options' => [
        'token replace' => TRUE,
        'clear' => FALSE,
      ],
    ]);
    $this->messageTemplate->set('text', [
      [
        'value' => 'foo [fake:token] and [message:author:name]',
        'format' => filter_default_format(),
      ],
    ]);
    $this->messageTemplate->save();

    $message = $this->entityTypeManager->getStorage('message')->create([
      'template' => $this->messageTemplate->id(),
    ]);
    $text = $message->getText();
    $this->assertEquals(1, count($text));
    $this->assertEquals('<p>foo [fake:token] and [message:author:name]</p>' . "\n", $text[0]);

    // Verify token clearing enabled.
    $this->messageTemplate->setSettings([
      'token options' => [
        'token replace' => TRUE,
        'clear' => TRUE,
      ],
    ]);
    $this->messageTemplate->save();
    $message = $this->entityTypeManager->getStorage('message')->create([
      'template' => $this->messageTemplate->id(),
    ]);
    $text = $message->getText();
    $this->assertEquals(1, count($text));
    $this->assertEquals('<p>foo  and </p>' . "\n", $text[0]);

    // Verify token replacement.
    $account = $this->createUser();
    $message->setOwner($account);
    $message->save();
    $text = $message->getText();
    $this->assertEquals(1, count($text));
    $this->assertEquals('<p>foo  and ' . $account->getUsername() . "</p>\n", $text[0]);

    // Disable token processing.
    $this->messageTemplate->setSettings([
      'token options' => [
        'token replace' => FALSE,
        'clear' => TRUE,
      ],
    ]);
    $this->messageTemplate->save();
    $text = $message->getText();
    $this->assertEquals(1, count($text));
    $this->assertEquals('<p>foo [fake:token] and [message:author:name]</p>' . "\n", $text[0]);
  }

  /**
   * Tests for getText argument handling.
   *
   * @covers ::getText
   */
  public function testGetTextArgumentProcessing() {
    $this->messageTemplate->setSettings([
      'token options' => [
        'token replace' => FALSE,
        'clear' => TRUE,
      ],
    ]);
    $this->messageTemplate->set('text', [
      [
        'value' => '@foo @replace and @no_replace',
        'format' => filter_default_format(),
      ],
      [
        'value' => 'some @foo other @replace',
        'format' => filter_default_format(),
      ],
    ]);
    $this->messageTemplate->save();
    $message = $this->entityTypeManager->getStorage('message')->create([
      'template' => $this->messageTemplate->id(),
      'arguments' => [
        [
          '@foo' => 'bar',
          '@replace' => [
            'pass message' => TRUE,
            'arguments' => [
              // When pass message is false, we'll use this text.
              'bar_replacement',
            ],
            'callback' => [static::class, 'argumentCallback'],
          ],
        ],
      ],
    ]);
    $message->save();
    $text = $message->getText();
    $this->assertEquals(2, count($text));
    $this->assertEquals('<p>bar bar_replacement_' . $message->id() . ' and @no_replace</p>' . "\n", $text[0]);
    $this->assertEquals('<p>some bar other bar_replacement_' . $message->id() . "</p>\n", $text[1]);

    // Do not pass the message.
    $message = $this->entityTypeManager->getStorage('message')->create([
      'template' => $this->messageTemplate->id(),
      'arguments' => [
        [
          '@foo' => 'bar',
          '@replace' => [
            'pass message' => FALSE,
            'arguments' => [
              // When pass message is false, we'll use this text.
              'bar_replacement',
            ],
            'callback' => [static::class, 'argumentCallback'],
          ],
        ],
      ],
    ]);
    $message->save();
    $text = $message->getText();
    $this->assertEquals(2, count($text));
    $this->assertEquals('<p>bar bar_replacement and @no_replace</p>' . "\n", $text[0]);
    $this->assertEquals('<p>some bar other bar_replacement' . "</p>\n", $text[1]);
  }

  /**
   * Test callback method for ::testGetTextArgumentProcessing().
   */
  public static function argumentCallback($arg_1, MessageInterface $message = NULL) {
    if ($message) {
      // Use the message ID appended to replacement text.
      $text = $arg_1 . '_' . $message->id();
    }
    else {
      $text = $arg_1;
    }
    return $text;
  }

}
