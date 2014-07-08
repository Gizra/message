<?php

/**
 * @file
 * Contains Drupal\message\Entity\Message.
 */

namespace Drupal\message\Entity;

use Drupal\Component\Utility\String;
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
 *   translatable = TRUE,
 *   fieldable = TRUE,
 *   bundle_entity_type = "message_type",
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
 *     "admin-form" = "message_type.edit"
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
   * Retrieve the message arguments.
   *
   * @return Array
   *  The arguments of the message.
   */
  public function getArguments() {
    $value = reset($this->get('arguments')->getValue())['value'];
    return !$value ? array() : unserialize($value);
  }

  /**
   * Set the arguments of the message.
   *
   * @param Array $values
   *  Array of arguments.
   *  @code
   *  $values = array(
   *    '@name_without_callback' => 'John doe',
   *    '@name_with_callback' => array(
   *      'callback' => 'User::load',
   *      'arguments' => array(1),
   *    ),
   *  );
   *  @endcode
   *
   * @return $this
   */
  public function setArguments($values) {
    $this->arguments = serialize($values);
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


    $fields['arguments'] = FieldDefinition::create('string')
      ->setLabel(t('Arguments'))
      ->setDescription(t('Holds the arguments of the message in serialise format.'));

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

    $output = $message_type->getText($langcode, $options);
    $arguments = $this->getArguments();

    if (is_array($arguments)) {
      $args = array();
      foreach ($arguments as $key => $value) {
        if (is_array($value) && !empty($value['callback']) && is_callable($value['callback'])) {

          // A replacement via callback function.
          $value += array('pass message' => FALSE);

          if ($value['pass message']) {
            // Pass the message object as-well.
            $value['callback arguments']['message'] = $this;
          }

          $value = call_user_func_array($value['callback'], $value['arguments']);
        }

        switch ($key[0]) {
          case '@':
            // Escaped only.
            $args[$key] = String::checkPlain($value);
            break;

          case '%':
          default:
            // Escaped and placeholder.
            $args[$key] = String::placeholder($value);
            break;

          case '!':
            // Pass-through.
            $args[$key] = $value;
        }
      }

      $output = strtr($output, $args);
    }

    $output = \Drupal::token()->replace($output, array('message' => $this), $options);

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function save() {
    // todo: Handle hard coded arguments with html.
    $token_options = !empty($this->data['token options']) ? $this->data['token options'] : array();

    $tokens = array();

    // Handle hard coded arguments.
    foreach ($this->getType()->text as $texts) {

      foreach ($texts as $text) {

        preg_match_all('/[@|%|\!]\{([a-z0-9:_\-]+?)\}/i', $text, $matches);

        foreach ($matches[1] as $delta => $token) {
          $output = \Drupal::token()->replace('[' . $token .  ']', array('message' => $this), $token_options);
          if ($output != '[' . $token . ']') {
            // Token was replaced.
            $argument = $matches[0][$delta];
            $tokens[$argument] = $output;
          }
        }
      }
    }

    $arguments = $this->getArguments();
    $this->setArguments(array_merge($tokens, $arguments));

    // todo: When the user object is not supplied set to the current user.
    parent::save();
  }
}
