<?php

/**
 * Test the Message delete on entity delete functionallity.
 */
class MessageEntityDelete extends DrupalWebTestCase {

  public static function getInfo() {
    return array(
      'name' => 'Message references',
      'description' => 'Test the Message delete on entity delete functionallity',
      'group' => 'Message',
      'dependencies' => array('entityreference'),
    );
  }

  function setUp() {
    parent::setUp('message', 'entityreference');

    variable_set('message_delete_on_entity_delete', array('node', 'taxonomy_term', 'user'));

    // Create a term reference field.
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
      'field_name' => 'field_term_ref',
      'type' => 'taxonomy_term_reference',
    );
    $field = field_create_field($field);
    $instance = array(
      'field_name' => 'field_term_ref',
      'bundle' => 'mt1',
      'entity_type' => 'message',
    );
    field_create_instance($instance);

    // Create an multiple-entities-reference field.
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
      'field_name' => 'field_nodes_ref',
      'type' => 'entityreference',
      'cardinality' => FIELD_CARDINALITY_UNLIMITED,
    );
    $field = field_create_field($field);
    $instance = array(
      'field_name' => 'field_nodes_ref',
      'bundle' => 'mt1',
      'entity_type' => 'message',
    );
    field_create_instance($instance);

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
   * Test deletion of a message after its referenced entities have been deleted.
   */
  function testReferencedEntitiesDelete() {
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
