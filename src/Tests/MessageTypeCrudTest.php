<?php

/**
 * @file
 * Definition of Drupal\message\Tests\MessageTypeCrudTest.
 */

namespace Drupal\message\Tests;

/**
 * Testing the CRUD functionallity for the Message type entity.
 *
 * @group Message
 */
class MessageTypeCrudTest extends MessageTestBase {

  /**
   * {@inheritdoc}
   */
  function setUp() {
    parent::setUp();
  }

  /**
   * Creating/reading/updating/deleting the message type entity and test it.
   */
  function testCrudEntityType() {
    // Create the message type.
    $created_message_type = $this->createMessageType('dummy_message', 'Dummy test', 'This is a dummy message with a dummy message', array('Dummy message'));

    // Reset any static cache.
    drupal_static_reset();

    // Load the message and verify the message type structure.
    $type = $this->loadMessageType('dummy_message');

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

    $type = $this->loadMessageType('dummy_message');
    $this->assertEqual($type->label, 'New label', 'The message was updated successfully');

    // Delete the message any try to load it from the DB.
    $type->delete();

    // Reset any static cache.
    drupal_static_reset();

    $this->assertFalse($this->loadMessageType('dummy_message'), 'The message was not found in the DB');
  }

}
