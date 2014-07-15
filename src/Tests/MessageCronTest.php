<?php

/**
 * @file
 * Definition of Drupal\message\Tests\MessageCron.
 */

namespace Drupal\message\Tests;

use Drupal\user\Entity\User;

/**
 * Test message purging upon cron
 *
 * @group Message
 */
class MessageCron extends MessageTestBase {

  /**
   * @var User
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Testing the deletion of messages in cron according to settings.
   */
  public function testMessageCron() {
  }
}
