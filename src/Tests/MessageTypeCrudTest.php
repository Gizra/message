<?php

/**
 * @file
 * Definition of Drupal\node\Tests\NodeAccessTest.
 */

namespace Drupal\message\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Testing the CRUD functionallity for the Message type entity.
 */
class MessageTypeCrudTest extends WebTestBase {
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
   * Creating/editing/deleting/updating the message type entity and test it.
   */
  function testCrudEntityType() {
    $this->pass('First testing ever!!!');
  }

}
