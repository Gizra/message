<?php

namespace Drupal\Tests\message\Functional;

use Drupal\message\Entity\Message;
use Drupal\message\Entity\MessageTemplate;

/**
 * Test message purging upon cron.
 *
 * @group Message
 */
class MessageCron extends MessageTestBase {

  /**
   * The user object.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $account;

  /**
   * The purge plugin manager.
   *
   * @var \Drupal\message\MessagePurgePluginManager
   */
  protected $purgeManager;

  /**
   * The cron service.
   *
   * @var \Drupal\Core\CronInterface
   */
  protected $cron;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->purgeManager = $this->container->get('plugin.manager.message.purge');
    $this->account = $this->drupalCreateUser();
    $this->cron = $this->container->get('cron');
  }

  /**
   * Testing the deletion of messages in cron according to settings.
   */
  public function testPurge() {
    // Create a purgeable message template with max quota 2 and max days 0.
    $quota = $this->purgeManager->createInstance('quota', ['data' => ['quota' => 2]]);
    $days = $this->purgeManager->createInstance('days', ['data' => ['days' => 0]]);
    $settings = [
      'purge_override' => TRUE,
      'purge_methods' => [
        'quota' => $quota->getConfiguration(),
        'days' => $days->getConfiguration(),
      ],
    ];

    /** @var \Drupal\message\Entity\MessageTemplate $message_template */
    $message_template = MessageTemplate::create(['template' => 'template1']);
    $message_template
      ->setSettings($settings)
      ->save();

    // Make sure the purging data is actually saved.
    $message_template = MessageTemplate::load($message_template->id());
    $this->assertEqual($message_template->getSetting('purge_methods'), $settings['purge_methods'], t('Purge settings are stored in message template.'));

    // Create a purgeable message template with max quota 1 and max days 2.
    $quota = $this->purgeManager->createInstance('quota', ['data' => ['quota' => 1]]);
    $days = $this->purgeManager->createInstance('days', ['data' => ['days' => 2]]);
    $settings = [
      'purge_override' => TRUE,
      'purge_methods' => [
        'quota' => $quota->getConfiguration(),
        'days' => $days->getConfiguration(),
      ],
    ];
    $message_template = MessageTemplate::create(['template' => 'template2']);
    $message_template
      ->setSettings($settings)
      ->save();

    // Create a non purgeable message (no purge methods enabled).
    $settings['purge_enabled'] = FALSE;
    $settings = [
      'purge_override' => TRUE,
      'purge_methods' => [],
    ];

    $message_template = MessageTemplate::create(['template' => 'template3']);
    $message_template
      ->setSettings($settings)
      ->save();

    // Create messages.
    for ($i = 0; $i < 4; $i++) {
      Message::Create(['template' => 'template1'])
        ->setCreatedTime(REQUEST_TIME - 3 * 86400)
        ->setOwnerId($this->account->id())
        ->save();
    }

    for ($i = 0; $i < 3; $i++) {
      Message::Create(['template' => 'template2'])
        ->setCreatedTime(REQUEST_TIME - 3 * 86400)
        ->setOwnerId($this->account->id())
        ->save();
    }

    for ($i = 0; $i < 3; $i++) {
      Message::Create(['template' => 'template3'])
        ->setCreatedTime(REQUEST_TIME - 3 * 86400)
        ->setOwnerId($this->account->id())
        ->save();
    }

    // Trigger message's hook_cron() as well as the queue processing.
    $this->cron->run();

    // Four template1 messages were created. The first two should have been
    // deleted.
    $this->assertFalse(array_diff(Message::queryByTemplate('template1'), [3, 4]), 'Two messages deleted due to quota definition.');

    // All template2 messages should have been deleted.
    $this->assertEqual(Message::queryByTemplate('template2'), [], 'Three messages deleted due to age definition.');

    // template3 messages should not have been deleted.
    $remaining = [8, 9, 10];
    $this->assertFalse(array_diff(Message::queryByTemplate('template3'), $remaining), 'Messages with disabled purging settings were not deleted.');
  }

  /**
   * Test global purge settings and overriding them.
   */
  public function testPurgeGlobalSettings() {
    // Set global purge settings.
    $quota = $this->purgeManager->createInstance('quota', ['data' => ['quota' => 1]]);
    $days = $this->purgeManager->createInstance('days', ['data' => ['days' => 2]]);
    $methods = [
      'quota' => $quota->getConfiguration(),
      'days' => $days->getConfiguration(),
    ];
    \Drupal::configFactory()->getEditable('message.settings')
      ->set('purge_enable', TRUE)
      ->set('purge_methods', $methods)
      ->save();

    MessageTemplate::create(['template' => 'template1'])->save();

    // Create an overriding template with no purge methods.
    $data = [
      'purge_override' => TRUE,
      'purge_methods' => [],
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

    // Trigger message's hook_cron() as well as the queue processing.
    $this->cron->run();

    $this->assertEqual(count(Message::queryByTemplate('template1')), 0, t('All template1 messages deleted.'));
    $this->assertEqual(count(Message::queryByTemplate('template2')), 2, t('Template2 messages were not deleted due to settings override.'));
  }

}
