<?php

/**
 * Test the Message cron functionallity.
 */
class MessageCron extends DrupalWebTestCase {

  public static function getInfo() {
    return array(
      'name' => 'Message purge',
      'description' => 'Test message purging upon cron',
      'group' => 'Message',
    );
  }

  function setUp() {
    parent::setUp('message');
  }

  /**
   * Test purging of messages upon message_cron according to message type purge
   * settings.
   */
  function testPurge() {
    $web_user = $this->drupalCreateUser();

    // Create a purgeable message type with max quota 2 and max days 0.
    $values = array(
      'data' => array(
        'purge' => array(
          'override' => TRUE,
          'enabled' => TRUE,
          'quota' => 2,
          'days' => 0,
        ),
      ),
    );
    $message_type = message_type_create('type1', $values);
    $message_type->save();

    // Make sure the purging data is actually saved.
    $this->assertEqual($message_type->data['purge'], $values['data']['purge'], t('Purge settings are stored in message type.'));

    // Create a purgeable message type with max quota 1 and max days 2.
    $values['data']['purge']['quota'] = 1;
    $values['data']['purge']['days'] = 2;
    $message_type = message_type_create('type2', $values);
    $message_type->save();

    // Create a non purgeable message type with max quota 1 and max days 10.
    $values['data']['purge']['enabled'] = FALSE;
    $values['data']['purge']['quota'] = 1;
    $values['data']['purge']['days'] = 1;
    $message_type = message_type_create('type3', $values);
    $message_type->save();

    $values = array(
      // Set messages creation time to three days ago.
      'timestamp' => time() - 3 * 86400,
    );
    // Create messages.
    for ($i = 0; $i < 4; $i++) {
      $message = message_create('type1', $values, $web_user);
      $message->save();
    }

    for ($i = 0; $i < 3; $i++) {
      $message = message_create('type2', $values, $web_user);
      $message->save();
    }

    for ($i = 0; $i < 3; $i++) {
      $message = message_create('type3', $values, $web_user);
      $message->save();
    }

    // Trigger message's hook_cron().
    message_cron();

    // Four type1 messages were created. The first two should have been
    // deleted.
    $messages = message_load_multiple(FALSE, array('type' => 'type1'));
    $this->assertEqual(array_keys($messages), array(3, 4), t('Two messages deleted due to quota definition.'));

    // All type2 messages should have been deleted.
    $messages = message_load_multiple(FALSE, array('type' => 'type2'));
    $this->assertEqual(array_keys($messages), array(), t('Three messages deleted due to age definition.'));

    // type3 messages should not have been deleted
    $messages = message_load_multiple(FALSE, array('type' => 'type3'));
    $this->assertEqual(array_keys($messages), array(8, 9, 10), t('Messages with disabled purging settings were not deleted.'));
  }

  /**
   * Test compliance with MESSAGE_DELETE_PER_PURGE.
   */
  function testPurgeRequestLimit() {
    // Set maximal amount of messages to delete.
    variable_set('message_delete_cron_limit', 10);

    $web_user = $this->drupalCreateUser();

    // Create a purgeable message type with max quota 2 and max days 0.
    $values = array(
      'data' => array(
        'purge' => array(
          'override' => TRUE,
          'enabled' => TRUE,
          'quota' => 2,
          'days' => 0,
        ),
      ),
    );
    $message_type = message_type_create('type1', $values);
    $message_type->save();
    $message_type = message_type_create('type2', $values);
    $message_type->save();

    // Create more messages than may be deleted in one request.
    for ($i = 0; $i < 10; $i++) {
      $message = message_create('type1', array(), $web_user);
      $message->save();
      $message = message_create('type2', array(), $web_user);
      $message->save();
    }

    // Trigger message's hook_cron().
    message_cron();

    // There are 16 messages to be deleted and 10 deletions allowed, so 8
    // messages of type1 and 2 messages of type2 should be deleted, thus 2
    // messages of type1 and 8 messages of type2 remain.
    $messages = message_load_multiple(FALSE, array('type' => 'type1'));
    $this->assertEqual(count($messages), 2, t('Two messages of type 1 left.'));

    $messages = message_load_multiple(FALSE, array('type' => 'type2'));
    $this->assertEqual(count($messages), 8, t('Eight messages of type 2 left.'));
  }

  /**
   * Test global purge settings and overriding them.
   */
  function testPurgeGlobalSettings() {
    // Set global purge settings.
    variable_set('message_purge_enable', TRUE);
    variable_set('message_purge_quota', 1);
    variable_set('message_purge_days', 2);

    $web_user = $this->drupalCreateUser();

    $message_type = message_type_create('type1');
    $message_type->save();

    // Create an overriding type.
    $values = array(
      'data' => array(
        'purge' => array(
          'override' => TRUE,
          'enabled' => FALSE,
          'quota' => 1,
          'days' => 1,
        ),
      ),
    );
    $message_type = message_type_create('type2', $values);
    $message_type->save();

    $values = array(
      // Set messages creation time to three days ago.
      'timestamp' => time() - 3 * 86400,
    );
    for ($i = 0; $i < 2; $i++) {
      $message = message_create('type1', $values, $web_user);
      $message->save();
      $message = message_create('type2', $values, $web_user);
      $message->save();
    }

    // Trigger message's hook_cron().
    message_cron();

    $messages = message_load_multiple(FALSE, array('type' => 'type1'));
    $this->assertEqual(count($messages), 0, t('All type1 messages deleted.'));

    $messages = message_load_multiple(FALSE, array('type' => 'type2'));
    $this->assertEqual(count($messages), 2, t('Type2 messages were not deleted due to settings override.'));
  }
}