<?php

/**
 * @file
 * Definition of Drupal\message\Tests\MessageCron.
 */

namespace Drupal\message\Tests;

use Drupal\message\Entity\Message;
use Drupal\message\Entity\MessageTemplate;
use Drupal\user\Entity\User;

/**
 * Test message purging upon cron.
 *
 * @group Message
 */
class MessageCron extends MessageTestBase {

  /**
   * The user object.
   *
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
    // Create a purgeable message template with max quota 2 and max days 0.
    $settings = [
      'purge' => [
        'override' => TRUE,
        'enabled' => TRUE,
        'quota' => 2,
        'days' => 0,
      ],
    ];

    /** @var MessageTemplate $message_template */
    $message_template = MessageTemplate::create(['template' => 'template1']);
    $message_template
      ->setSettings($settings)
      ->save();

    // Make sure the purging data is actually saved.
    $this->assertEqual($message_template->getSetting('purge'), $settings['purge'], t('Purge settings are stored in message template.'));

    // Create a purgeable message template with max quota 1 and max days 2.
    $settings['purge']['quota'] = 1;
    $settings['purge']['days'] = 2;
    $message_template = MessageTemplate::create(['template' => 'template2']);
    $message_template
      ->setSettings($settings)
      ->save();

    // Create a non purgeable message template with max quota 1 and max days 10.
    $settings['purge']['enabled'] = FALSE;
    $settings['purge']['quota'] = 1;
    $settings['purge']['days'] = 1;
    $message_template = MessageTemplate::create(['template' => 'template3']);
    $message_template
      ->setSettings($settings)
      ->save();

    // Create messages.
    for ($i = 0; $i < 4; $i++) {
      Message::Create(['template' => 'template1'])
        ->setCreatedTime(time() - 3 * 86400)
        ->setOwnerId($this->account->id())
        ->save();
    }

    for ($i = 0; $i < 3; $i++) {
      Message::Create(['template' => 'template2'])
        ->setCreatedTime(time() - 3 * 86400)
        ->setOwnerId($this->account->id())
        ->save();
    }

    for ($i = 0; $i < 3; $i++) {
      Message::Create(['template' => 'template3'])
        ->setCreatedTime(time() - 3 * 86400)
        ->setOwnerId($this->account->id())
          ->save();
    }

    // Trigger message's hook_cron().
    message_cron();

    // Four template1 messages were created. The first two should have been
    // deleted.
    $this->assertFalse(array_diff(Message::queryByTemplate('template1'), [3, 4]), 'Two messages deleted due to quota definition.');

    // All template2 messages should have been deleted.
    $this->assertEqual(Message::queryByTemplate('template2'), [], 'Three messages deleted due to age definition.');

    // template3 messages should not have been deleted.
    $this->assertFalse(array_diff(Message::queryByTemplate('template3'), [8, 9, 10]), 'Messages with disabled purging settings were not deleted.');
  }

  /**
   * Testing the purge request limit.
   */
  public function testPurgeRequestLimit() {
    // Set maximal amount of messages to delete.
    \Drupal::configFactory()->getEditable('message.settings')
      ->set('delete_cron_limit', 10)
      ->save();

    // Create a purgeable message template with max quota 2 and max days 0.
    $data = [
      'purge' => [
        'override' => TRUE,
        'enabled' => TRUE,
        'quota' => 2,
        'days' => 0,
      ],
    ];

    MessageTemplate::create(['template' => 'template1'])
      ->setSettings($data)
      ->save();

    MessageTemplate::create(['template' => 'template2'])
      ->setSettings($data)
      ->save();

    // Create more messages than may be deleted in one request.
    for ($i = 0; $i < 10; $i++) {
      Message::Create(['template' => 'template1'])
        ->setOwnerId($this->account->id())
        ->save();
      Message::Create(['template' => 'template2'])
        ->setOwnerId($this->account->id())
        ->save();
    }

    // Trigger message's hook_cron().
    message_cron();

    // There are 16 messages to be deleted and 10 deletions allowed, so 8
    // messages of template1 and 2 messages of template2 should be deleted, thus 2
    // messages of template1 and 8 messages of template2 remain.
    $this->assertEqual(count(Message::queryByTemplate('template1')), 2, t('Two messages of template 1 left.'));

    $this->assertEqual(count(Message::queryByTemplate('template2')), 8, t('Eight messages of template 2 left.'));
  }

  /**
   * Test global purge settings and overriding them.
   */
  public function testPurgeGlobalSettings() {
    // Set global purge settings.
    \Drupal::configFactory()->getEditable('message.settings')
      ->set('purge_enable', TRUE)
      ->set('purge_quota', 1)
      ->set('purge_days', 2)
      ->save();

    MessageTemplate::create(['template' => 'template1'])->save();

    // Create an overriding template.
    $data = [
      'purge' => [
        'override' => TRUE,
        'enabled' => FALSE,
        'quota' => 1,
        'days' => 1,
      ],
    ];

    MessageTemplate::create(['template' => 'template2'])
      ->setSettings($data)
      ->save();

    for ($i = 0; $i < 2; $i++) {
      Message::create(['template' => 'template1'])
        ->setCreatedTime(time() - 3 * 86400)
        ->setOwnerId($this->account->id())
        ->save();

      Message::create(['template' => 'template2'])
        ->setCreatedTime(time() - 3 * 86400)
        ->setOwnerId($this->account->id())
        ->save();
    }

    // Trigger message's hook_cron().
    message_cron();

    $this->assertEqual(count(Message::queryByTemplate('template1')), 0, t('All template1 messages deleted.'));
    $this->assertEqual(count(Message::queryByTemplate('template2')), 2, t('Template2 messages were not deleted due to settings override.'));
  }
}
