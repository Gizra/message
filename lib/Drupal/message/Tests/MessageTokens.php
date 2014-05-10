<?php

/**
 * Test the Message and tokens integration.
 */
class MessageTokens extends DrupalWebTestCase {

  public static function getInfo() {
    return array(
      'name' => 'Message tokens',
      'description' => 'Test the Message and tokens integration.',
      'group' => 'Message',
    );
  }

  function setUp() {
    parent::setUp('message', 'entity_token');
  }

  /**
   * Test token replacement in a message type.
   */
  function testTokens() {
    $user1 = $this->drupalCreateUser();
    $random_text = $this->randomName();
    $token_message = '[message:user:name] ' . $random_text;
    $replaced_message = $user1->name . ' ' . $random_text;

    $message_type = message_type_create('foo');
    $message_type->{MESSAGE_FIELD_MESSAGE_TEXT} = array(
      LANGUAGE_NONE => array(
        0 => array('value' => $token_message, 'format' => 'plain_text'),
      ),
    );
    $message_type->save();

    $message = message_create('foo', array(), $user1);

    $this->assertEqual($replaced_message . "\n", strip_tags($message->getText()), t('Got correct text after token replacement.'));

    // Test not replacing tokens setting enabled.
    $message->data['token replace'] = FALSE;
    $this->assertEqual($token_message . "\n", strip_tags($message->getText()), t('Got correct text without token replacement.'));
  }

  /**
   * Test token hardcoding in a message type.
   */
  function testTokensHardcode() {
    $user1 = $this->drupalCreateUser();
    $name = $user1->name;
    $random_text = $this->randomName();

    $token_messages = array(
      'some text @{message:user} ' . $random_text,
      'some text !{message:user} ' . $random_text,
      'some text %{message:user} ' . $random_text,
      'some text !{wrong:token} ' . $random_text
    );

    $replaced_messages = array(
      'some text ' . $name . ' ' . $random_text,
      'some text ' . $name . ' ' . $random_text,
      'some text <em class="placeholder">' . $name . '</em> ' . $random_text,
      'some text !{wrong:token} ' . $random_text
    );

    $message_type = message_type_create('foo');
    foreach ($token_messages as $token_message) {
      $message_type->{MESSAGE_FIELD_MESSAGE_TEXT}[LANGUAGE_NONE][] = array('value' => $token_message, 'format' => 'plain_text');
    }
    $message_type->save();

    $message = message_create('foo', array(), $user1);

    // Assert the arguments.
    $this->assertTrue(empty($message->arguments), t('No message arguments exist prior to saving the message.'));
    $message->save();
    $this->assertEqual(count($message->arguments), 3, t('Correct number of arguments added after saving the message.'));

    // Assert message is rendered as expected.
    foreach (array_keys($message_type->{MESSAGE_FIELD_MESSAGE_TEXT}[LANGUAGE_NONE]) as $delta) {
      $options = array(
        'partials' => TRUE,
        'partial delta' => $delta,
      );
      // Get text from each partial. We strip the <p> tags, but make sure
      // to keep the <em> tag, so we can assert the token prefixed with
      // '%' sign.
      $this->assertEqual($replaced_messages[$delta] . "\n", strip_tags($message->getText(LANGUAGE_NONE, $options), '<em>'), t('Got correct text for partial @delta after token replacement.', array('@delta' => $delta)));
    }

    // Test no hardcoding.
    $message = message_create('foo', array(), $user1);
    $message->data['skip token hardcode'] = TRUE;
    $message->save();
    $this->assertTrue(empty($message->arguments), t('No message arguments created after saving the message, when "skip token hardcode" is enabled.'));

  }
}