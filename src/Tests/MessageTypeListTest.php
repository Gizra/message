<?php

/**
 * @file
 * Definition of Drupal\message\Tests\MessageTypeCrudTest.
 */

namespace Drupal\message\Tests;

use Drupal\user\Entity\User;

/**
 * Testing the listing functionality for the Message type entity.
 *
 * @group Message
 */
class MessageTypeListTest extends MessageTestBase {

  /**
   * @var User
   *
   * The user object.
   */
  protected $user;

  /**
   * Listing of messages.
   */
  public function testEntityTypeList() {
    $this->drupalLogin($this->user);

    $this->drupalGet('admin/structure/message');
    $this->assertResponse(200);
  }

}
