<?php

/**
 * @file
 * Definition of Drupal\node\Tests\MessageUiTest.
 */

namespace Drupal\message\Tests;
use Drupal\user\Entity\User;

/**
 * Testing the CRUD functionallity for the Message type entity.
 */
class MessageUiTest extends MessageTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = array('language', 'config_translation', 'message');

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Message UI test',
      'description' => 'Testing the UI for translating messages including the config translation.',
      'group' => 'Message',
    );
  }

  /**
   * @var User
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->account = $this->drupalCreateUser(array('administer message types', 'translate configuration'));
  }

  public function testMessageTranslate() {
    $this->drupalLogin($this->account);
return;
    $edit = array('action' => 'node_make_sticky_action');
    $this->drupalPostForm('admin/structure/message/type/add', $edit, t('Save message type'));
  }
}
