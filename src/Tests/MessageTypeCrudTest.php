<?php

/**
 * @file
 * Definition of Drupal\node\Tests\MessageTypeCrudTest.
 */

namespace Drupal\message\Tests;

/**
 * Testing the CRUD functionallity for the Message type entity.
 */
class MessageTypeCrudTest extends MessageTestBase {
  public static function getInfo() {
    return array(
      'name' => 'Message type CRUD',
      'description' => 'Testing the message type crud functionallity',
      'group' => 'Message',
    );
  }

  function setUp() {
    parent::setUp();
  }

  /**
   * Creating/reading/updating/deleting the message type entity and test it.
   */
  function testCrudEntityType() {
    // Create the message type.
    $created_message_type = $this->createMessageType('dummy_text', 'Dummy test', 'This is a dummy message with a dummy text', array('Dummy text'));

    // Reset any static cache.
    drupal_static_reset();

    // Load the message and verify the message type structure.
    $type = $this->loadMessageType('dummy_text');

    foreach (array('type' => 'Type', 'label' => 'Label', 'description' => 'Description', 'text' => 'Text') as $key => $label) {
       $param = array(
         '@label' => $label,
       );

       $this->assertEqual($type->{$key}, $created_message_type->{$key}, format_string('The @label between the message we created an loaded are equal', $param));
    }

    // Verifying updating action.
    $type->label = 'New label';
    $type->save();

    // Reset any static cache.
    drupal_static_reset();

    $type = $this->loadMessageType('dummy_text');
    $this->assertEqual($type->label, 'New label', 'The message was updated successfully');


    // Delete the message any try to load it from the DB.
    $type->delete();

    // Reset any static cache.
    drupal_static_reset();

    $this->assertFalse($this->loadMessageType('dummy_text'), 'The message was not found in the DB');
  }

}
