<?php

/**
 * @file
 * Definition of Drupal\message\Tests\MessageTypeCrudTest.
 */

namespace Drupal\message\Tests;

/**
 * Testing the listing functionality for the Message type entity.
 *
 * @group Message
 */
class MessageTypeListTest extends MessageTestBase {

  function setUp() {
    parent::setUp();
    $this->user = $this->drupalCreateUser(['administer message types']);

  }

  /**
   * Listing of messages.
   */
  function testEntityTypeList() {
    $this->drupalLogin($this->user);

    $test = $this->drupalGet('admin/structure/message');
    $this->assertResponse(200);
  }

}
