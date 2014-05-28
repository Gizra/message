<?php

/**
 * Test the Rules integration.
 */
class MessageRulesIntegrationTestCase extends DrupalWebTestCase {

  public static function getInfo() {
    return array(
      'name' => 'Message Rules integration',
      'description' => 'Tests the message module Rules integration.',
      'group' => 'Message',
      'dependencies' => array('rules'),
    );
  }

  function setUp() {
    parent::setUp('rules', 'message');
  }

  /**
   * Tests creating a message via an action.
   */
  function testCRUD() {
    $message_type = message_type_create('foo', array('message_text' => array(LANGUAGE_NONE => array(array('value' => 'Example text.')))));
    $message_type->save();

    $rule = rule();
    $rule->action('entity_create', array(
      'type' => 'message',
      'param_type' => 'foo',
      'param_user' => entity_metadata_wrapper('user', $GLOBALS['user']),
    ));
    $rule->integrityCheck();
    $rule->execute();

    // Checker whether a new message has been saved.
    $messages = message_load_multiple(FALSE, array('type' => 'foo'));
    $message = reset($messages);

    $this->assertTrue(!empty($message), 'Message has been saved using Rules.');
    $this->assertEqual($message->uid, $GLOBALS['user']->uid , 'Message has been saved for the right user.');
    RulesLog::logger()->checkLog();
  }
}
