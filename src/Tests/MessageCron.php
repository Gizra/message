<?php

/**
 * @file
 * Definition of Drupal\message\Tests\MessageCron.
 */

namespace Drupal\message\Tests;

use Drupal\message\Entity\Message;
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
      'purge' => array(
        'override' => TRUE,
        'enabled' => TRUE,
        'quota' => 2,
        'days' => 0,
      ),
    );

    /** @var MessageType $message_type */
    $message_type = MessageType::create(array('type' => 'type1'));
    $message_type
      ->setData($data)
      ->save();

    // Make sure the purging data is actually saved.
    $this->assertEqual($message_type->getData('purge'), $data['purge'], t('Purge settings are stored in message type.'));

    // Create a purgeable message type with max quota 1 and max days 2.
    $data['purge']['quota'] = 1;
    $data['purge']['days'] = 2;
    $message_type = MessageType::create(array('type' => 'type2'));
    $message_type
      ->setData($data)
      ->save();

    // Create a non purgeable message type with max quota 1 and max days 10.
    $data['purge']['enabled'] = FALSE;
    $data['purge']['quota'] = 1;
    $data['purge']['days'] = 1;
    $message_type = MessageType::create(array('type' => 'type3'));
    $message_type
      ->setData($data)
      ->save();

    // Create messages.
    for ($i = 0; $i < 4; $i++) {
      Message::Create(array('type' => 'type1'))
        ->setCreatedTime(time() - 3 * 86400)
        ->setAuthorId($user->id())
        ->save();
    }

    for ($i = 0; $i < 3; $i++) {
      Message::Create(array('type' => 'type2'))
        ->setCreatedTime(time() - 3 * 86400)
        ->setAuthorId($user->id())
        ->save();
    }

    for ($i = 0; $i < 3; $i++) {
      Message::Create(array('type' => 'type3'))
        ->setCreatedTime(time() - 3 * 86400)
        ->setAuthorId($user->id())
        ->save();
    }

    // Trigger message's hook_cron().
    message_cron();

    // Four type1 messages were created. The first two should have been
    // deleted.
    $this->assertFalse(array_diff($this->queryMessages('type1'), array(3,4)), 'Two messages deleted due to quota definition.');

    // All type2 messages should have been deleted.
    $this->assertEqual($this->queryMessages('type2'), array(), 'Three messages deleted due to age definition.');

    // type3 messages should not have been deleted
    $this->assertFalse(array_diff($this->queryMessages('type3'), array(8, 9, 10)), 'Messages with disabled purging settings were not deleted.');
  }

  /**
   * Run a EFQ over messages from a given type.
   *
   * @param $type
   *  The entity type.
   *
   * @return Array
   *  Array of message IDs.
   */
  private function queryMessages($type) {
    return \Drupal::entityQuery('message')
      ->condition('type', $type)
      ->execute();
  }
}
