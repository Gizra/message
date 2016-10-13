<?php

namespace Drupal\Tests\message\Kernel;

use Drupal\Component\Utility\Unicode;
use Drupal\KernelTests\KernelTestBase;
use Drupal\message\Entity\Message;
use Drupal\simpletest\UserCreationTrait;

/**
 * Kernel tests for the Message entity.
 *
 * @group Message
 */
class MessageTest extends KernelTestBase {

  use \Drupal\Tests\message\Kernel\MessageTemplateCreateTrait;
  use UserCreationTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['message', 'user', 'system'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->installEntitySchema('message');
    $this->installEntitySchema('user');
    $this->installSchema('system', ['sequences']);
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
    $template = $this->createMessageTemplate(Unicode::strtolower($this->randomMachineName()), $this->randomString(), $this->randomString(), []);
    $message = Message::create(['template' => $template->id()]);
    $account = $this->createUser();
    $message->setOwner($account);
    $this->assertEquals($account->id(), $message->getOwnerId());

    $owner = $message->getOwner();
    $this->assertEquals($account->id(), $owner->id());
  }

}
