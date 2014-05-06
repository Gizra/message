<?php

/**
 * @file
 * Contains Drupal\message\Entity\Message.
 */

namespace Drupal\message\Entity;

use Drupal\Core\Entity\Entity;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\message\MessageInterface;

/**
 * Defines the Message entity class.
 *
 * @EntityType(
 *   id = "message",
 *   label = @Translation("Message"),
 *   bundle_label = @Translation("Message type"),
 *   module = "message",
 *   base_table = "message",
 *   fieldable = TRUE,
 *   controllers = {
 *     "storage" = "Drupal\Core\Entity\DatabaseStorageControllerNG"
 *   },
 *   entity_keys = {
 *     "id" = "mid",
 *     "bundle" = "type",
 *     "uuid" = "uuid"
 *   },
 *   bundle_keys = {
 *     "bundle" = "type"
 *   }
 * )
 */
//class Message extends Entity implements MessageInterface {
class Message extends Entity {

  /**
   * Implements Drupal\Core\Entity\EntityInterface::id().
   */
  public function id() {
    return $this->get('mid')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->bundle();
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }


  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthor() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthorId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setAuthorId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function get($property_name) {
    // TODO: Implement get() method.
  }

  /**
   * {@inheritdoc}
   */
  public function set($property_name, $value, $notify = TRUE) {
    // TODO: Implement set() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getProperties($include_computed = FALSE) {
    // TODO: Implement getProperties() method.
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    // TODO: Implement isEmpty() method.
  }

  /**
   * {@inheritdoc}
   */
  public function onChange($property_name) {
    // TODO: Implement onChange() method.
  }

  /**
   * {@inheritdoc}
   */
  public function initTranslation($langcode) {
    // TODO: Implement initTranslation() method.
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $properties['mid'] = array(
      'label' => t('Message ID'),
      'description' => t('The message ID.'),
      'type' => 'integer_field',
      'read-only' => TRUE,
    );

    $properties['uuid'] = array(
      'label' => t('UUID'),
      'description' => t('The message UUID.'),
      'type' => 'uuid_field',
      'read-only' => TRUE,
    );
    $properties['type'] = array(
      'label' => t('Type'),
      'description' => t('The message type.'),
      'type' => 'entity_reference_field',
      'settings' => array(
        'target_type' => 'message_type',
        'default_value' => 0,
      ),
      'read-only' => TRUE,
    );
    $properties['langcode'] = array(
      'label' => t('Language code'),
      'description' => t('The node language code.'),
      'type' => 'language_field',
    );
    $properties['uid'] = array(
      'label' => t('User ID'),
      'description' => t('The user ID of the node author.'),
      'type' => 'entity_reference_field',
      'settings' => array(
        'target_type' => 'user',
        'default_value' => 0,
      ),
    );
    $properties['created'] = array(
      'label' => t('Created'),
      'description' => t('The time that the message was created.'),
      'type' => 'integer_field',
    );
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function bundleFieldDefinitions(EntityTypeInterface $entity_type, $bundle, array $base_field_definitions) {
    // TODO: Implement bundleFieldDefinitions() method.
  }

  /**
   * {@inheritdoc}
   */
  public function hasField($field_name) {
    // TODO: Implement hasField() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldDefinition($name) {
    // TODO: Implement getFieldDefinition() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldDefinitions() {
    // TODO: Implement getFieldDefinitions() method.
  }

  /**
   * {@inheritdoc}
   */
  public function isNewRevision() {
    // TODO: Implement isNewRevision() method.
  }

  /**
   * {@inheritdoc}
   */
  public function setNewRevision($value = TRUE) {
    // TODO: Implement setNewRevision() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionId() {
    // TODO: Implement getRevisionId() method.
  }

  /**
   * {@inheritdoc}
   */
  public function isDefaultRevision($new_value = NULL) {
    // TODO: Implement isDefaultRevision() method.
  }

  /**
   * {@inheritdoc}
   */
  public function preSaveRevision(EntityStorageInterface $storage, \stdClass $record) {
    // TODO: Implement preSaveRevision() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getTranslationLanguages($include_default = TRUE) {
    // TODO: Implement getTranslationLanguages() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getTranslation($langcode) {
    // TODO: Implement getTranslation() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getUntranslated() {
    // TODO: Implement getUntranslated() method.
  }

  /**
   * {@inheritdoc}
   */
  public function hasTranslation($langcode) {
    // TODO: Implement hasTranslation() method.
  }

  /**
   * {@inheritdoc}
   */
  public function addTranslation($langcode, array $values = array()) {
    // TODO: Implement addTranslation() method.
  }

  /**
   *{@inheritdoc}
   */
  public function removeTranslation($langcode) {
    // TODO: Implement removeTranslation() method.
  }

  /**
   * {@inheritdoc}
   */
  public function isTranslatable() {
    // TODO: Implement isTranslatable() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getDataDefinition() {
    // TODO: Implement getDataDefinition() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getValue() {
    // TODO: Implement getValue() method.
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($value, $notify = TRUE) {
    // TODO: Implement setValue() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getString() {
    // TODO: Implement getString() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    // TODO: Implement getConstraints() method.
  }

  /**
   * {@inheritdoc}
   */
  public function validate() {
    // TODO: Implement validate() method.
  }

  /**
   * {@inheritdoc}
   */
  public function applyDefaultValue($notify = TRUE) {
    // TODO: Implement applyDefaultValue() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    // TODO: Implement getName() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getParent() {
    // TODO: Implement getParent() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getRoot() {
    // TODO: Implement getRoot() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyPath() {
    // TODO: Implement getPropertyPath() method.
  }

  /**
   * {@inheritdoc}
   */
  public function setContext($name = NULL, TypedDataInterface $parent = NULL) {
    // TODO: Implement setContext() method.
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator() {
    // TODO: Implement getIterator() method.
  }
}
