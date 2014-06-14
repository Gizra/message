<?php

/**
 * @file
 * Definition of Drupal\message\Tests\MessageTestBase.
 */

namespace Drupal\message\Tests;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Language\Language;
use Drupal\message\Controller\MessageController;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\taxonomy\Entity\Vocabulary;

/**
 * Test the Message delete on entity delete functionality.
 */
class MessageEntityDelete extends MessageTestBase {

  /**
   * @var Vocabulary
   */
  protected $vocabulary;


  /**
   * @var NodeType
   */
  protected $contentType;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('message', 'entity_reference', 'node', 'taxonomy', 'user');

  public static function getInfo() {
    return array(
      'name' => 'Message references',
      'description' => 'Test the Message delete on entity delete functionality',
      'group' => 'Message',
    );
  }

  function setUp() {
    parent::setUp();

    // Set config.
    $this->configSet('delete_on_entity_delete', array('node', 'taxonomy_term', 'user'));

    // Set config.
    $this->createMessageType('dummy_text', 'Dummy text', 'This is a dummy message text', array('Dummy text message type.'));

    // Create a vocabulary.
    $this->vocabulary = entity_create('taxonomy_vocabulary', array(
      'name' => $this->randomName(),
      'description' => $this->randomName(),
      'vid' => drupal_strtolower($this->randomName()),
      'langcode' => Language::LANGCODE_NOT_SPECIFIED,
      'weight' => mt_rand(0, 10),
    ));
    $this->vocabulary->save();

    $this->createTermReferenceField(TRUE, 'field_term_references');
    $this->createEntityReferenceField(TRUE, 'field_nodes_ref');

    $this->createTermReferenceField(FALSE, 'field_term_reference');
    $this->createEntityReferenceField(FALSE, 'field_node_ref');

    $this->contentType = $this->drupalCreateContentType();

    for ($i = 0; $i <= 5; $i++) {
      entity_create('node', array(
        'type' => $this->contentType->id(),
        'title' => 'Node ' . $i,
      ))->save();
      entity_create('taxonomy_term', array(
        'vid' => $this->vocabulary->id(),
        'name' => 'term ' . $i,
      ))->save();
    }
  }

  /**
   * Create a term reference field.
   *
   * @param boolean $multiple
   *  Determine of the field should be multiple.
   * @param string $name
   *  The name of the field.
   */
  private function createTermReferenceField($multiple, $name) {
    // Create a term reference field.
    entity_create('field_config', array(
      'name' => $name,
      'entity_type' => 'message',
      'type' => 'taxonomy_term_reference',
      'cardinality' => $multiple ? FieldDefinitionInterface::CARDINALITY_UNLIMITED : 1,
      'settings' => array(
        'allowed_values' => array(
          array(
            'vocabulary' => $this->vocabulary->id(),
            'parent' => 0,
          ),
        ),
      ),
    ))->save();

    entity_create('field_instance_config', array(
      'field_name' => $name,
      'bundle' => 'dummy_text',
      'entity_type' => 'message',
    ))->save();
  }

  /**
   * Create a multiple entity reference field.
   *
   * @param boolean $multiple
   *  Determine of the field should be multiple.
   * @param string $name
   *  The name of the field.
   */
  private function createEntityReferenceField($multiple, $name) {
    entity_create('field_config', array(
      'name' => $name,
      'entity_type' => 'message',
      'translatable' => FALSE,
      'entity_types' => array(),
      'settings' => array(
        'target_type' => 'node',
      ),
      'type' => 'entity_reference',
      'cardinality' => $multiple ? FieldDefinitionInterface::CARDINALITY_UNLIMITED : 1,
    ))->save();

    entity_create('field_instance_config', array(
      'label' => 'Entity reference field',
      'field_name' => $name,
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
    $message = MessageController::MessageCreate(array('type' => 'dummy_text'));
    $message->set('field_nodes_ref', array(1, 2));
    $message->save();

    Node::load(2)->delete();

    $this->assertTrue(MessageController::MessageLoad($message->id()), 'Message exists after deleting one of two referenced nodes.');

    Node::load(1)->delete();

//    $this->assertFalse(MessageController::MessageLoad($message->id()), 'Message deleted after deleting all referenced nodes.');
    return;
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
