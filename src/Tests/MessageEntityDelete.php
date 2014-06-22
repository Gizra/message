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
use Drupal\taxonomy\Entity\Term;
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
    $this->createEntityReferenceField(TRUE, 'field_node_references');

    $this->createTermReferenceField(FALSE, 'field_term_reference');
    $this->createEntityReferenceField(FALSE, 'field_node_reference');

    $this->createEntityReferenceField(FALSE, 'field_user_reference', 'user');

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
    $field = entity_create('field_config', array(
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
    ));
    $field->save();

    entity_create('field_instance_config', array(
      'field' => $field,
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
   * @param string $target_type
   *  The target type. Default to node.
   */
  private function createEntityReferenceField($multiple, $name, $target_type = 'node') {
    $field = entity_create('field_config', array(
      'name' => $name,
      'entity_type' => 'message',
      'translatable' => FALSE,
      'entity_types' => array(),
      'settings' => array(
        'target_type' => $target_type,
      ),
      'type' => 'entity_reference',
      'cardinality' => $multiple ? FieldDefinitionInterface::CARDINALITY_UNLIMITED : 1,
    ));

    $field->save();

    entity_create('field_instance_config', array(
      'label' => 'Entity reference field',
      'field' => $field,
      'entity_type' => 'message',
      'bundle' => 'dummy_text',
      'settings' => array(
        'handler' => 'default',
        'handler_settings' => array(
          'target_bundles' => array(
            $target_type,
          ),
          'auto_create' => TRUE,
        ),
      ),
    ))->save();
  }

  /**
   * Test deletion of a message after its referenced entities have been deleted.
   */
  function testReferencedEntitiesDelete() {
    // Testing nodes reference.
    $message = MessageController::MessageCreate(array('type' => 'dummy_text'));
    $message->set('field_node_references', array(1, 2));
    $message->save();

    Node::load(1)->delete();
    $this->assertTrue(MessageController::MessageLoad($message->id()), 'Message exists after deleting one of two referenced nodes.');
    Node::load(2)->delete();
    $this->assertFalse(MessageController::MessageLoad($message->id()), 'Message deleted after deleting all referenced nodes.');

    // Test terms reference.
    $message = MessageController::MessageCreate(array('type' => 'dummy_text'));
    $message->set('field_term_references', array(1, 2));
    $message->save();

    Term::load(1)->delete();
    $this->assertTrue(MessageController::MessageLoad($message->id()), 'Message exists after deleting one of two referenced terms.');
    Term::load(2)->delete();
    $this->assertFalse(MessageController::MessageLoad($message->id()), 'Message deleted after deleting all referenced terms.');

    // Test term references.
    $term = Term::load(3);
    $message = MessageController::MessageCreate(array('type' => 'dummy_text'));
    $message->set('field_term_reference', $term);
    $message->save();

    $term->delete();
    $this->assertFalse(MessageController::MessageLoad($message->id()), 'Message deleted after deleting single referenced term.');

    // Test node reference.
    $message = MessageController::MessageCreate(array('type' => 'dummy_text'));
    $message->set('field_node_reference', 3);
    $message->save();

    Node::load(3)->delete();
    $this->assertFalse(MessageController::MessageLoad($message->id()), 'Message deleted after deleting single referenced node.');

    // Testing when a message referenced to terms and term.
    $message = MessageController::MessageCreate(array('type' => 'dummy_text'));
    $message->set('field_term_references', array(4, 5));
    $message->set('field_term_reference', 4);
    $message->save();
    Term::load(4)->delete();

    $this->assertFalse(MessageController::MessageLoad($message->id()), 'Message deleted after deleting single referenced term while another the message still references other term in another field.');

    // Test user reference.
    $account = $this->drupalCreateUser();
    $message = MessageController::MessageCreate(array('type' => 'dummy_text'));
    $message->set('field_user_reference', $account->id());
    $message->save();

    $account->delete();
    $this->assertFalse(MessageController::MessageLoad($message->id()), 'Message deleted after deleting single referenced user.');
  }
}
