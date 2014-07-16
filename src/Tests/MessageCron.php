<?php

/**
 * @file
 * Definition of Drupal\message\Tests\MessageCron.
 */

namespace Drupal\message\Tests;

use Drupal\message\Entity\MessageType;
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
  public function testPurge() {
    $user = $this->drupalCreateUser();

    // Create a purgeable message type with max quota 2 and max days 0.
    $data = array(
      'data' => array(
        'purge' => array(
          'override' => TRUE,
          'enabled' => TRUE,
          'quota' => 2,
          'days' => 0,
        ),
      ),
    );

    /** @var MessageType $message_type */
    $message_type = MessageType::create(array('type' => 'dummy'))
      ->setData($data)
      ->save();

    // Make sure the purging data is actually saved.
    $this->assertEqual($message_type->getData('purge'), $data['data']['purge'], t('Purge settings are stored in message type.'));

  }
}
