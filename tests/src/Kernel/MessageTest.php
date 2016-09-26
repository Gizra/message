<?php

namespace Drupal\Tests\message\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\message\Entity\Message;
use Drupal\message\Tests\MessageTemplateCreateTrait;

/**
 * Kernel tests for the Message entity.
 *
 * @group Message
 */
class MessageTest extends KernelTestBase {

  use MessageTemplateCreateTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['message', 'user'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->installEntitySchema('message');
    $this->installEntitySchema('user');
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

}
