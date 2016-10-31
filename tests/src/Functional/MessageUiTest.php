<?php

namespace Drupal\Tests\message\Functional;

use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\message\Entity\MessageTemplate;

/**
 * Testing the CRUD functionality for the Message template entity.
 *
 * @group Message
 */
class MessageUiTest extends MessageTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'language',
    'config_translation',
    'message',
    'filter_test',
  ];

  /**
   * The user object.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->account = $this->drupalCreateUser([
      'administer message templates',
      'translate configuration',
      'use text format filtered_html',
    ]);
  }

  /**
   * Test the translation interface for message.
   */
  public function testMessageTranslate() {
    $this->drupalLogin($this->account);

    // Verifying creation of a message template.
    $edit = [
      'label' => 'Dummy message',
      'template' => 'dummy_message',
      'description' => 'This is a dummy text',
      // Use some HTML to ensure text formatting is working in ::getText().
      'text[0][value]' => '<p>This is a dummy message with some dummy text</p>',
      'text[0][format]' => 'filtered_html',
    ];
    $this->drupalPostForm('admin/structure/message/template/add', $edit, t('Save message template'));
    $this->assertText('The message template Dummy message created successfully.', 'The message created successfully');
    $this->drupalGet('admin/structure/message/manage/dummy_message');

    $elements = [
      '//input[@value="Dummy message"]' => 'The label input text exists on the page with the right text.',
      '//input[@value="This is a dummy text"]' => 'The description of the message exists on the page.',
      '//textarea[.="<p>This is a dummy message with some dummy text</p>"]' => 'The body of the message exists in the page.',
    ];
    $this->verifyFormElements($elements);

    // Verifying editing message.
    $edit = [
      'label' => 'Edited dummy message',
      'description' => 'This is a dummy text after editing',
      'text[0][value]' => '<p>This is a dummy message with some edited dummy text</p>',
    ];
    $this->drupalPostForm('admin/structure/message/manage/dummy_message', $edit, t('Save message template'));

    $this->drupalGet('admin/structure/message/manage/dummy_message');

    $elements = [
      '//input[@value="Edited dummy message"]' => 'The label input text exists on the page with the right text.',
      '//input[@value="This is a dummy text after editing"]' => 'The description of the message exists on the page.',
      '//textarea[.="<p>This is a dummy message with some edited dummy text</p>"]' => 'The body of the message exists in the page.',
    ];
    $this->verifyFormElements($elements);

    // Add language.
    ConfigurableLanguage::create(['id' => 'he', 'name' => 'Hebrew'])->save();

    // Change to post form and add text different then the original.
    $edit = [
      'translation[config_names][message.template.dummy_message][label]' => 'Translated dummy message to Hebrew',
      'translation[config_names][message.template.dummy_message][description]' => 'This is a dummy text after translation to Hebrew',
      'translation[config_names][message.template.dummy_message][text][0][value]' => '<p>This is a dummy message with translated text to Hebrew</p>',
    ];
    $this->drupalPostForm('admin/structure/message/manage/dummy_message/translate/he/add', $edit, t('Save translation'));

    // Go to the edit form and verify text.
    $this->drupalGet('admin/structure/message/manage/dummy_message/translate/he/edit');

    $elements = [
      '//input[@value="Translated dummy message to Hebrew"]' => 'The text in the form translation is the expected string in Hebrew.',
      '//textarea[.="This is a dummy text after translation to Hebrew"]' => 'The description element have the expected value in Hebrew.',
      '//textarea[.="<p>This is a dummy message with translated text to Hebrew</p>"]' => 'The text element have the expected value in Hebrew.',
    ];
    $this->verifyFormElements($elements);

    // Load the message via code in hebrew and english and verify the text.
    $template = 'dummy_message';
    /* @var $message MessageTemplate */
    $message = MessageTemplate::load($template);
    if (empty($message)) {
      $this->fail('MessageTemplate "' . $template . '" not found.');
    }
    else {
      $this->assertEquals(['<p>This is a dummy message with translated text to Hebrew</p>'], $message->getText('he'), 'The text in hebrew pulled correctly.');
      $this->assertEquals(['<p>This is a dummy message with some edited dummy text</p>'], $message->getText(), 'The text in english pulled correctly.');
    }

    // Delete message via the UI.
    $this->drupalPostForm('admin/structure/message/delete/' . $template, [], 'Delete');
    $this->assertText(t('There is no Message template yet.'));
    $this->assertFalse(MessageTemplate::load($template), 'The message deleted via the UI successfully.');
  }

  /**
   * Verifying the form elements values in easy way.
   *
   * When all the elements are passing a pass message with the text "The
   * expected values is in the form." When one of the Xpath expression return
   * false the message will be display on screen.
   *
   * @param array $elements
   *   Array mapped by in the next format.
   *
   * @code
   *   [XPATH_EXPRESSION => MESSAGE]
   * @endcode
   */
  private function verifyFormElements(array $elements) {
    $errors = [];
    foreach ($elements as $xpath => $message) {
      $element = $this->xpath($xpath);
      if (!$element) {
        $errors[] = $message;
      }
    }

    if (empty($errors)) {
      $this->pass('All elements were found.');
    }
    else {
      $this->fail('The next errors were found: ' . implode("", $errors));
    }
  }

}
