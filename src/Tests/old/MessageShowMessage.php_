<?php

/**
 * Test the Message CRUD handling.
 */
class MessageShowMessage extends DrupalWebTestCase {

  public static function getInfo() {
    return array(
      'name' => 'Message view',
      'description' => 'Test viewing a message.',
      'group' => 'Message',
    );
  }

  function setUp() {
    parent::setUp('message');
  }

  /**
   * Test showing a message.
   */
  function testMessageView() {
    // Add language.
    module_enable(array('locale'));
    require_once DRUPAL_ROOT . '/includes/locale.inc';
    for ($i = 0; $i < 2; ++$i) {
      locale_add_language('l' . $i, $this->randomString(), $this->randomString());
    }

    $property = MESSAGE_FIELD_MESSAGE_TEXT;

    // We use randomName instead of randomString since later-on we use
    // strip_tags, so we don't want to get characters that might be escaped.
    $text1 = $this->randomName() . ' argument -- @foo';
    $text2 = $this->randomName() . ' argument -- @foo';
    $message_type = message_type_create('foo');
    $message_type->{$property} = array(
      'en' => array(
        0 => array('value' => 'english text'),
      ),
      'l0' => array(
        0 => array('value' => $text1),
      ),
      'l1' => array(
        0 => array('value' => $text2),
      ),
    );
    $message_type->save();

    // Reload the message type to see it was saved
    $message_type = message_type_load('foo');
    $this->assertTrue(!empty($message_type->id), t('Message type was saved to the database.'));

    // Assert the message type text field exists and is populated.
    $this->assertEqual($message_type->{$property}['l0'][0]['value'], $text1, t('First language message text was saved to the database.'));
    $this->assertEqual($message_type->{$property}['l1'][0]['value'], $text2, t('Second language message text was saved to the database.'));

    $arguments = array('@foo' => $this->randomName(4));
    $message = message_create('foo', array('arguments' => $arguments));
    $message->save();

    // Assert the arguments in the message are replaced.
    $output = $message->getText('l0');
    $this->assertEqual(trim(strip_tags($output)), strtr($text1, $arguments), t('Arguments in the first language message were replaced.'));

    // Assert the arguments in the message are replaced when showing a message
    // from another language.
    $output = $message->getText('l1');
    $this->assertEqual(trim(strip_tags($output)), strtr($text2, $arguments), t('Arguments in the second language message were replaced.'));

    // Assert value is of current language when Locale is enabled and langocde is NULL.
    global $language;
    $output = $message->getText(NULL);
    $this->assertEqual(trim(strip_tags($output)), 'english text', 'Assert value is of current language when Locale is enabled and langocde is NULL.');
  }

  /**
   * Test message-type and message arguments.
   */
  function testMessageArguments() {
    $text = 'test @token1 and @token2';

    $message_type = message_type_create('foo');
    $message_type->{MESSAGE_FIELD_MESSAGE_TEXT} = array(
      LANGUAGE_NONE => array(
        0 => array('value' => $text, 'format' => 'plain_text'),
      ),
    );
    $message_type->arguments = array(
      '@token1' => 'token1',
      '@token2' => 'token2',
    );
    $message_type->save();

    $message = message_create('foo');
    $output = trim(strip_tags($message->getText()));
    $this->assertEqual('test token1 and token2', $output, t('Message type arguments replaced correctly.'));

    // Test overriding message type arguments, with message arguments.
    $message->arguments = array('@token2' => 'token3');
    $output = trim(strip_tags($message->getText()));
    $this->assertEqual('test token1 and token3', $output, t('Message arguments override message type arguments correctly.'));
  }

  /**
   * Test getting only a single delta from the field.
   */
  function testPartials() {
    $text1 = $this->randomName();
    $text2 = $this->randomName();
    $message_type = message_type_create('foo');
    $message_type->{MESSAGE_FIELD_MESSAGE_TEXT} = array(
      LANGUAGE_NONE => array(
        0 => array('value' => $text1, 'format' => 'plain_text'),
        1 => array('value' => $text2, 'format' => 'filtered_html'),
      ),
    );
    $message_type->save();
    $message = message_create('foo');

    $this->assertEqual($text1 . "\n" . $text2 . "\n", strip_tags($message->getText()), t('Got correct text for all deltas.'));

    $options = array(
      'partials' => TRUE,
      'partial delta' => 0,
    );
    $this->assertEqual($text1 . "\n", strip_tags($message->getText(LANGUAGE_NONE, $options)), t('Got correct text for the 1st delta.'));

    $options['partial delta'] = 1;
    $this->assertEqual($text2 . "\n", strip_tags($message->getText(LANGUAGE_NONE, $options)), t('Got correct text for the 2nd delta.'));
  }

  /**
   * Test rendering partials as extra-fields.
   */
  function testExtraField() {
    $message_type = message_type_create('foo');
    $wrapper = entity_metadata_wrapper('message_type', $message_type);
    $wrapper->{MESSAGE_FIELD_MESSAGE_TEXT}[] = array('value' => 'first partial', 'format' => 'plain_text');
    $wrapper->{MESSAGE_FIELD_MESSAGE_TEXT}[] = array('value' => 'second partial', 'format' => 'plain_text');
    $wrapper->{MESSAGE_FIELD_MESSAGE_TEXT}[] = array('value' => 'third partial', 'format' => 'plain_text');
    $wrapper->save();

    $display = field_extra_fields_get_display('message', 'foo', 'default');
    $this->assertEqual(count($display), 3, 'All partials appear in extra-fields.');
    $this->assertTrue(!empty($display['message__message_text__0']) && !empty($display['message__message_text__1']) && !empty($display['message__message_text__2']), 'All partials appear in extra-fields with the correct name.');

    // Check field info cache is cleared on message type save.
    $wrapper->{MESSAGE_FIELD_MESSAGE_TEXT}[] = array('value' => 'fourth partial', 'format' => 'plain_text');
    $wrapper->save();

    $display = field_extra_fields_get_display('message', 'foo', 'default');
    $this->assertEqual(count($display), 4, 'All partials appear in extra-fields, after re-save.');
    $this->assertTrue(!empty($display['message__message_text__0']) && !empty($display['message__message_text__1']) && !empty($display['message__message_text__2']) && !empty($display['message__message_text__3']), 'All partials appear in extra-fields with the correct name, after re-save.');

    $message = message_create('foo');

    // Ensure partials get rendered, before changing any display settings.
    $build = $message->view();
    $text = drupal_render($build);
    $partial1_pos = strpos($text, 'first partial');
    $partial2_pos = strpos($text, 'second partial');
    $partial3_pos = strpos($text, 'third partial');
    $partial4_pos = strpos($text, 'fourth partial');

    $this->assertTrue($partial1_pos && $partial2_pos && $partial3_pos && $partial4_pos, 'All partials found in rendered message text.');

    // Enable the Full view mode, hide the first partial,
    // and display the last partial first.
    $settings = field_bundle_settings('message', 'foo');
    $settings['view_modes']['full']['custom_settings'] = TRUE;
    $settings['extra_fields']['display']['message__message_text__0']['full'] = array('weight' => 0, 'visible' => FALSE);
    $settings['extra_fields']['display']['message__message_text__1']['full'] = array('weight' => 5, 'visible' => TRUE);
    $settings['extra_fields']['display']['message__message_text__2']['full'] = array('weight' => 10, 'visible' => TRUE);
    $settings['extra_fields']['display']['message__message_text__3']['full'] = array('weight' => -5, 'visible' => TRUE);
    field_bundle_settings('message', 'foo', $settings);

    // Render the message text using Full view mode.
    $build = $message->view('full');
    $text = drupal_render($build);
    $partial2_pos = strpos($text, 'second partial');
    $partial3_pos = strpos($text, 'third partial');
    $partial4_pos = strpos($text, 'fourth partial');

    $this->assertNoText('first partial', 'First partial successfully hidden from rendered text using Full view mode.');
    $this->assertTrue($partial2_pos && $partial3_pos && $partial4_pos, 'All partials configured to be visible were rendered.');
    $this->assertTrue(($partial4_pos < $partial2_pos) && ($partial2_pos < $partial3_pos), 'All partials are rendered in the correct order after re-ordering.');

    // Create a Field API text field to render between partials.
    $field = array(
      'field_name' => 'field_bar',
      'type' => 'text',
    );
    field_create_field($field);
    $instance = array(
      'field_name' => 'field_bar',
      'label' => 'Bar',
      'entity_type' => 'message',
      'bundle' => 'foo',
      'settings' => array(),
      'display' => array(
        // Set weight to render between first two partials.
        'full' => array('visible' => TRUE, 'weight' => -3),
      ),
    );
    field_create_instance($instance);

    $message_wrapper = entity_metadata_wrapper('message', $message);
    $message_wrapper->field_bar->set('sample field text');

    $build = $message->view('full');
    $text = drupal_render($build);
    $partial2_pos = strpos($text, 'second partial');
    $partial3_pos = strpos($text, 'third partial');
    $partial4_pos = strpos($text, 'fourth partial');
    $field_pos = strpos($text, 'sample field text');

    $this->assertTrue(($partial4_pos < $field_pos) && ($field_pos < $partial2_pos), 'Text field rendered between two partials.');
  }

  /**
   * Test rendering message text fields other than MESSAGE_FIELD_MESSAGE_TEXT
   * as extra fields.
   */
  function testOtherExtraField() {
    $field = array(
      'field_name' => 'baz',
      'type' => 'text',
      'entity_types' => array('message_type'),
      'settings' => array(
        // Mark this as a message text field.
        'message_text' => TRUE,
      ),
    );
    field_create_field($field);

    $instance = array(
      'field_name' => 'baz',
      'bundle' => 'message_type',
      'entity_type' => 'message_type',
    );
    field_create_instance($instance);

    $message_type = message_type_create('foo');
    $wrapper = entity_metadata_wrapper('message_type', $message_type);
    $wrapper->{MESSAGE_FIELD_MESSAGE_TEXT}[] = array('value' => 'first field', 'format' => 'plain_text');
    $wrapper->baz->set('other field');
    $wrapper->save();

    $message = message_create('foo');
    $build = $message->buildContent();
    $this->assertTrue($build['message__message_text__0']['#markup'], '<p>first field</p>', 'Default message-text field appears correctly.');
    $this->assertTrue($build['message__baz__0']['#markup'], 'other field', 'Non-default message-text field appears correctly.');
  }
}

