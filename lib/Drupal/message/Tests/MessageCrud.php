<?php

/**
 * Test the Message CRUD handling.
 */
class MessageCrud extends DrupalWebTestCase {

  public static function getInfo() {
    return array(
      'name' => 'Message CRUD',
      'description' => 'Test the create, update and remove of Message entities.',
      'group' => 'Message',
    );
  }

  function setUp() {
    parent::setUp('message');
  }

  /**
   * Test CRUD of message entity.
   */
  function testMessageCrud() {
    $web_user = $this->drupalCreateUser();

    $message_type = message_type_create('foo', array('message_text' => array(LANGUAGE_NONE => array(array('value' => 'Example text.')))));
    $message_type->save();

    $message = message_create('foo', array(), $web_user);
    $message->save();
    $mid = $message->mid;

    // Reload the message to see it was saved.
    $message = message_load($mid);
    $this->assertTrue(!empty($message->mid), t('Message was saved to the database.'));

    $this->assertEqual($message->uid, $web_user->uid, 'Message has been saved for the right user.');
    $this->assertEqual($message->getType()->message_text[LANGUAGE_NONE][0]['value'], 'Example text.', 'Message type text has been saved.');

    // Make sure an exception is thrown if message type already exists.
    try {
      $message_type = message_type_create('foo');
      $this->fail("Creating the same message type hasn't created an exception.");
    }
    catch (Exception $e) {
      $this->pass("Exception was thrown: ". $e->getMessage());
    }
  }
}
