<?php

/**
 * @file
 * Contains Drupal\message\Entity\Message.
 */

namespace Drupal\message\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldDefinition;
use Drupal\Core\Language\Language;
use Drupal\message\Controller\MessageController;
use Drupal\user\Entity\User;

/**
 * Defines the Message entity class.
 *
 * @ContentEntityType(
 *   id = "message",
 *   label = @Translation("Message"),
 *   bundle_label = @Translation("Message type"),
 *   module = "message",
 *   base_table = "message",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "mid",
 *     "bundle" = "type",
 *     "uuid" = "uuid"
 *   },
 *   bundle_keys = {
 *     "bundle" = "type"
 *   },
 *   controllers = {
 *     "view_builder" = "Drupal\message\MessageViewBuilder",
 *     "list_builder" = "Drupal\message\MessageListBuilder",
 *   },
 *   links = {
 *     "admin-form" = "message.type_add"
 *   }
 * )
 */
class Message extends ContentEntityBase {

  /**
   * Implements Drupal\Core\Entity\EntityInterface::id().
   */
  public function id() {
    return $this->get('mid')->value;
  }

  /**
   * Get the type of the message type.
   *
   * @return MessageType
   */
  public function getType() {
    return MessageController::MessageTypeLoad($this->bundle());
  }

  /**
   * Retrieve the time stamp of the message.
   *
   * @return Int
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * Setting the timestamp.
   *
   * @param Int $timestamp
   *  The timestamp
   *
   * @return $this
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * Retrieve the message owner object.
   *
   * @return User
   *  The user object.
   */
  public function getAuthor() {
    return $this->get('uid')->entity;
  }

  /**
   * Retrieve the author ID.
   *
   * @return Int
   *  The author ID.
   */
  public function getAuthorId() {
    return $this->get('uid')->target_id;
  }

  /**
   * Set the author ID.s
   *
   * @param Int $uid
   *  The user ID.
   *
   * @return $this
   *  The user object.
   */
  public function setAuthorId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['mid'] = FieldDefinition::create('integer')
      ->setLabel(t('Message ID'))
      ->setDescription(t('The message ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uuid'] = FieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The message UUID'))
      ->setReadOnly(TRUE);

    $fields['type'] = FieldDefinition::create('entity_reference')
      ->setLabel(t('Type'))
      ->setDescription(t('The message type.'))
      ->setSetting('target_type', 'message_type')
      ->setSetting('default_value', 0)
      ->setReadOnly(TRUE);

    $fields['langcode'] = FieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The message language code.'))
      ->setRevisionable(TRUE);

    $fields['uid'] = FieldDefinition::create('entity_reference')
      ->setLabel(t('Author'))
      ->setDescription(t('The user that is the message author.'))
      ->setRevisionable(TRUE)
      ->setSettings(array(
        'target_type' => 'user',
        'default_value' => 0,
      ))
      ->setTranslatable(TRUE);

    $fields['created'] = FieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the node was created.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

  /**
   * Replace arguments with their placeholders.
   *
   * @param $langcode
   *   Optional; The language to get the text in. If not set the current language
   *   will be used.
   * @param $options
   *   Optional; Array to be passed to MessageType::getText().
   *
   * @return string
   *  The message text.
   */
  public function getText($langcode = Language::LANGCODE_NOT_SPECIFIED, $options = array()) {

    if (!$message_type = $this->getType()) {
      // Message type does not exist any more.
      return '';
    }

    return $message_type->getText();

    return;
    $arguments = message_get_property_values($this, 'arguments');
    $output = $message_type->getText($langcode, $options);

    if (!empty($arguments)) {
      $args = array();
      foreach ($arguments as $key => $value) {
        if (is_array($value) && !empty($value['callback']) && function_exists($value['callback'])) {
          // A replacement via callback function.
          $value += array('pass message' => FALSE);
          if ($value['pass message']) {
            // Pass the message object as-well.
            $value['callback arguments'][] = $this;
          }

          $value = call_user_func_array($value['callback'], $value['callback arguments']);
        }

        switch ($key[0]) {
          case '@':
            // Escaped only.
            $args[$key] = check_plain($value);
            break;

          case '%':
          default:
            // Escaped and placeholder.
            $args[$key] = drupal_placeholder($value);
            break;

          case '!':
            // Pass-through.
            $args[$key] = $value;
        }
      }
      $output = strtr($output, $args);
    }
    $token_replace = message_get_property_values($this, 'data', 'token replace', TRUE);
    if ($output && $token_replace) {
      // Message isn't explicetly denying token replace, so process the text.
      $context = array('message' => $this);

      $token_options = message_get_property_values($this, 'data', 'token options');
      $output = token_replace($output, $context, $token_options);
    }
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function save() {
    // todo: When the user object is not supplied set to the current user.
    parent::save();
  }
}
