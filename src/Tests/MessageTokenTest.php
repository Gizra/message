<?php

/**
 * @file
 * Definition of Drupal\message\Tests\MessageTokenTest.
 */

namespace Drupal\message\Tests;

/**
 * Test the Message and tokens integration.
 */
class MessageTokenTest extends MessageTestBase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Message tokens',
      'description' => 'Test the Message and tokens integration.',
      'group' => 'Message',
    );
  }

  /**
   * {@inheritdoc}
   */
  function setUp() {
    parent::setUp();
  }

  /**
   * Test token replacement in a message type.
   */
  function testTokens() {
  }
}
