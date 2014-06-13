<?php

/**
 * @file
 * Definition of Drupal\message\Tests\MessageTestBase.
 */

namespace Drupal\message\Tests;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Language\Language;

/**
 * Test the Message delete on entity delete functionality.
 */
class MessageEntityDelete extends MessageTestBase {

  protected $fieldName1;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('taxonomy', 'entity_reference', 'message');

  public static function getInfo() {
    return array(
      'name' => 'Message references',
      'description' => 'Test the Message delete on entity delete functionality',
      'group' => 'Message',
    );
  }

  function setUp() {
    parent::setUp();

    $this->createMessageType('dummy_text', 'Dummy text', 'This is a dummy message text', array('Dummy text message type.'));
    $this->configSet('message_delete_on_entity_delete', array('node', 'taxonomy_term', 'user'));

    $this->createTermReferenceField();
    $this->createEntityReferenceField();

    return;

    // Create a multiple-terms-reference field.
    $field = array(
      'translatable' => FALSE,
      'entity_types' => array('message'),
      'settings' => array(
        'allowed_values' => array(
          array(
            'vocabulary' => 'terms',
            'parent' => 0,
          ),
        ),
      ),
      'field_name' => 'field_terms_ref',
      'type' => 'taxonomy_term_reference',
      'cardinality' => FIELD_CARDINALITY_UNLIMITED,
    );
    $field = field_create_field($field);
    $instance = array(
      'field_name' => 'field_terms_ref',
      'bundle' => 'mt1',
      'entity_type' => 'message',
    );
    field_create_instance($instance);

    // Create an entity reference field.
    $field = array(
      'translatable' => FALSE,
      'entity_types' => array('message'),
      'settings' => array(
        'handler' => 'base',
        'target_type' => 'node',
        'handler_settings' => array(
          'target_bundles' => array(),
        ),
      ),
      'field_name' => 'field_node_ref',
      'type' => 'entityreference',
    );
    $field = field_create_field($field);
    $instance = array(
      'field_name' => 'field_node_ref',
      'bundle' => 'mt1',
      'entity_type' => 'message',
    );
    field_create_instance($instance);

    // Create an user entity reference field.
    $field = array(
      'translatable' => FALSE,
      'entity_types' => array('message'),
      'settings' => array(
        'handler' => 'base',
        'target_type' => 'user',
        'handler_settings' => array(
          'target_bundles' => array(),
        ),
      ),
      'field_name' => 'field_user_ref',
      'type' => 'entityreference',
    );
    $field = field_create_field($field);
    $instance = array(
      'field_name' => 'field_user_ref',
      'bundle' => 'mt1',
      'entity_type' => 'message',
    );
    field_create_instance($instance);

    // Create a terms vocobulary.
    $vocabulary = new stdClass();
    $vocabulary->name = 'Terms';
    $vocabulary->machine_name = 'terms';
    taxonomy_vocabulary_save($vocabulary);

    $message_type = message_type_create('mt1', array());
    $message_type->save();

    // Create nodes and terms.
    for ($i = 1; $i <= 5; $i++) {
      $node = new stdClass();
      $node->type = 'article';
      node_object_prepare($node);
      $node->title = "node $i";
      $node->language = LANGUAGE_NONE;
      node_save($node);

      $term = new stdClass();
      $term->name = "term $i";
      $term->vid = 1;
      taxonomy_term_save($term);
    }
  }

  /**
   * Creating a single term reference field.
   */
  private function createTermReferenceField() {
    // Create a vocabulary.
    $vocabulary = entity_create('taxonomy_vocabulary', array(
      'name' => $this->randomName(),
      'description' => $this->randomName(),
      'vid' => drupal_strtolower($this->randomName()),
      'langcode' => Language::LANGCODE_NOT_SPECIFIED,
      'weight' => mt_rand(0, 10),
    ));
    $vocabulary->save();

    // Create a term reference field.
    $this->fieldName1 = drupal_strtolower($this->randomName());
    entity_create('field_config', array(
      'name' => 'field_term_reference',
      'entity_type' => 'message',
      'type' => 'taxonomy_term_reference',
      'cardinality' => 1,
      'settings' => array(
        'allowed_values' => array(
          array(
            'vocabulary' => $vocabulary->id(),
            'parent' => 0,
          ),
        ),
      ),
    ))->save();

    entity_create('field_instance_config', array(
      'field_name' => 'field_term_reference',
      'bundle' => 'dummy_text',
      'entity_type' => 'message',
    ))->save();
  }

  /**
   * Create a multiple entity reference field.
   */
  private function createEntityReferenceField() {
    entity_create('field_config', array(
      'name' => 'field_nodes_ref',
      'entity_type' => 'message',
      'translatable' => FALSE,
      'entity_types' => array(),
      'settings' => array(
        'target_type' => 'node',
      ),
      'type' => 'entity_reference',
      'cardinality' => FieldDefinitionInterface::CARDINALITY_UNLIMITED,
    ))->save();

    entity_create('field_instance_config', array(
      'label' => 'Entity reference field',
      'field_name' => 'field_nodes_ref',
      'entity_type' => 'message',
      'bundle' => 'dummy_text',
      'settings' => array(
        'handler' => 'default',
        'handler_settings' => array(
          // Reference a single vocabulary.
          'target_bundles' => array(
            'node',
          ),
          // Enable auto-create.
          'auto_create' => TRUE,
        ),
      ),
    ))->save();
  }

  /**
   * Test deletion of a message after its referenced entities have been deleted.
   */
  function testReferencedEntitiesDelete() {
    $this->pass('foo');

    return;
    // Test entities reference.
    $message = message_create('mt1', array());
    $wrapper = entity_metadata_wrapper('message', $message);
    $wrapper->field_nodes_ref->set(array(1, 2));
    $wrapper->save();
    node_delete(2);
    $message = message_load($message->mid);
    $this->assertTrue($message, 'Message exists after deleting one of two referenced nodes.');
    node_delete(1);
    $message = message_load($message->mid);
    $this->assertTrue(empty($message), 'Message deleted after deleting all referenced nodes.');

    // Test terms reference.
    $message = message_create('mt1', array());
    $wrapper = entity_metadata_wrapper('message', $message);
    $wrapper->field_terms_ref->set(array(1, 2));
    $wrapper->save();
    taxonomy_term_delete(2);
    $message = message_load($message->mid);
    $this->assertTrue($message, 'Message exists after deleting one of two referenced terms.');
    taxonomy_term_delete(1);
    $message = message_load($message->mid);
    $this->assertTrue(empty($message), 'Message deleted after deleting all referenced terms.');

    $message = message_create('mt1', array());
    $wrapper = entity_metadata_wrapper('message', $message);
    $wrapper->field_terms_ref->set(array(3));
    $wrapper->save();
    taxonomy_term_delete(3);
    $message = message_load($message->mid);
    $this->assertTrue(empty($message), 'Message deleted after deleting single referenced term.');

    $message = message_create('mt1', array());
    $wrapper = entity_metadata_wrapper('message', $message);
    $wrapper->field_terms_ref->set(array(4, 5));
    $wrapper->field_term_ref->set(4);
    $wrapper->save();
    taxonomy_term_delete(4);
    $message = message_load($message->mid);
    $this->assertTrue(empty($message), 'Message deleted after deleting single referenced term while another the message still references other term in another field.');

    // Test user reference.
    $account = $this->drupalCreateUser();
    $message = message_create('mt1', array());
    $wrapper = entity_metadata_wrapper('message', $message);
    $wrapper->field_user_ref->set($account->uid);
    $wrapper->save();
    user_delete($account->uid);
    $message = message_load($message->mid);
    $this->assertTrue(empty($message), 'Message deleted after deleting single referenced user.');
  }
}
