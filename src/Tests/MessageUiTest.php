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

    // Verifying creation of a message.
    $edit = array(
      'label' => 'Dummy message',
      'type' => 'dummy_message',
      'description' => 'This is a dummy text',
      'text[0]' => 'This is a dummy message with some dummy text',
    );
    $this->drupalPostForm('admin/structure/message/type/add', $edit, t('Save message type'));

    $this->assertText('The message type Dummy message created successfully.', 'The message created successfully');

    $this->drupalGet('admin/structure/message/manage/dummy_message');

    // Check that the label exists on the page with the right value.
    $element = $this->xpath('//input[@value="Dummy message"]');
    $this->assertTrue($element, 'The label input text exists on the page with the right text.');

    // Check that the description element appear on the page with the right
    // value.
    $element = $this->xpath('//input[@value="This is a dummy text"]');
    $this->assertTrue($element, 'The name of the message exists on the page.');

    // Verifying editing message.
    // todo.
  }
}
