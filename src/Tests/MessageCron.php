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

    $this->account = $this->drupalCreateUser();
  }

  /**
   * Testing the deletion of messages in cron according to settings.
   */
  public function testPurge() {
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
        ->setAuthorId($this->account->id())
        ->save();
    }

    for ($i = 0; $i < 3; $i++) {
      Message::Create(array('type' => 'type2'))
        ->setCreatedTime(time() - 3 * 86400)
        ->setAuthorId($this->account->id())
        ->save();
    }

    for ($i = 0; $i < 3; $i++) {
      Message::Create(array('type' => 'type3'))
        ->setCreatedTime(time() - 3 * 86400)
        ->setAuthorId($this->account->id())
        ->save();
    }

    // Trigger message's hook_cron().
    message_cron();

    // Four type1 messages were created. The first two should have been
    // deleted.
    $this->assertFalse(array_diff(Message::queryByType('type1'), array(3,4)), 'Two messages deleted due to quota definition.');

    // All type2 messages should have been deleted.
    $this->assertEqual(Message::queryByType('type2'), array(), 'Three messages deleted due to age definition.');

    // type3 messages should not have been deleted
    $this->assertFalse(array_diff(Message::queryByType('type3'), array(8, 9, 10)), 'Messages with disabled purging settings were not deleted.');
  }

  function testPurgeRequestLimit() {
    // Set maximal amount of messages to delete.
    \Drupal::config('message.message')
      ->set('delete_cron_limit', 10)
      ->save();

    // Create a purgeable message type with max quota 2 and max days 0.
    $data = array(
      'purge' => array(
        'override' => TRUE,
        'enabled' => TRUE,
        'quota' => 2,
        'days' => 0,
      ),
    );

    MessageType::create(array('type' => 'type1'))
      ->setData($data)
      ->save();

    MessageType::create(array('type' => 'type2'))
      ->setData($data)
      ->save();

    // Create more messages than may be deleted in one request.
    for ($i = 0; $i < 10; $i++) {
      Message::Create(array('type' => 'type1'))
        ->setAuthorId($this->account->id())
        ->save();
      Message::Create(array('type' => 'type2'))
        ->setAuthorId($this->account->id())
        ->save();
    }

    // Trigger message's hook_cron().
    message_cron();

    // There are 16 messages to be deleted and 10 deletions allowed, so 8
    // messages of type1 and 2 messages of type2 should be deleted, thus 2
    // messages of type1 and 8 messages of type2 remain.
    $this->assertEqual(count(Message::queryByType('type1')), 2, t('Two messages of type 1 left.'));

    $this->assertEqual(count(Message::queryByType('type2')), 8, t('Eight messages of type 2 left.'));
  }
}
