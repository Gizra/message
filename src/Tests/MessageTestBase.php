<?php

/**
 * @file
 * Definition of Drupal\message\Tests\MessageTestBase.
 */

namespace Drupal\message\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Holds set of tools for the message testing.
 */
abstract class MessageTestBase extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('message');

  /**
   * The node access controller.
   *
   * @var \Drupal\Core\Entity\EntityAccessControllerInterface
   */
  protected $accessController;

  function setUp() {
    parent::setUp();
  }

}
