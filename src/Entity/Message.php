<?php

/**
 * @file
 * Contains Drupal\message\Entity\Message.
 */

namespace Drupal\message\Entity;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Language\Language;
use Drupal\Core\Render\Markup;
use Drupal\message\MessageInterface;
use Drupal\message\MessageTypeInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Message entity class.
 *
 * @ContentEntityType(
 *   id = "message",
 *   label = @Translation("Message"),
 *   bundle_label = @Translation("Message type"),
 *   module = "message",
 *   base_table = "message",
 *   data_table = "message_field_data",
 *   translatable = TRUE,
 *   bundle_entity_type = "message_type",
 *   entity_keys = {
 *     "id" = "mid",
 *     "bundle" = "type",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode"
 *   },
 *   bundle_keys = {
 *     "bundle" = "type"
 *   },
 *   handlers = {
 *     "view_builder" = "Drupal\message\MessageViewBuilder",
 *     "list_builder" = "Drupal\message\MessageListBuilder",
 *     "views_data" = "Drupal\message\MessageViewsData",
 *   },
 *   field_ui_base_route = "entity.message_type.edit_form"
 * )
 */
class Message extends ContentEntityBase implements MessageInterface, EntityOwnerInterface {

  /**
   * @var int
   *
   * The message ID.
   */
  protected $mid;

  /**
   * @var string
   *
   * The UUID string.
   */
  protected $uuid;

  /**
   * @var \Drupal\message\MessageTypeInterface
   *
   * The message type object.
   */
  protected $type;

  /**
   * @var \Drupal\user\UserInterface
   *
   * The user object.
   */
  protected $uid;

  /**
   * @var int
   *
   * The time stamp the message was created.
   */
  protected $created;

  /**
   * @var array
   *
   * Holds the arguments of the message instance.
   */
  protected $arguments;

  /**
   * {@inheritdoc}
   */
  public function setType(MessageTypeInterface $type) {
    $this->set('type', $type);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return MessageType::load($this->bundle());
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
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->getEntityKey('uid');
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUUID() {
    return $this->get('uuid')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getArguments() {
    return $this->get('arguments')->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function setArguments(array $values) {
    $this->set('arguments', $values);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['mid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Message ID'))
      ->setDescription(t('The message ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The message UUID'))
      ->setReadOnly(TRUE);

    $fields['type'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Type'))
      ->setDescription(t('The message type.'))
      ->setSetting('target_type', 'message_type')
      ->setReadOnly(TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The message language code.'));

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Author'))
      ->setDescription(t('The user that is the message author.'))
      ->setSettings(array(
        'target_type' => 'user',
        'default_value' => 0,
      ))
      ->setTranslatable(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the node was created.'))
      ->setTranslatable(TRUE);

    $fields['arguments'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Arguments'))
      ->setDescription(t('Holds the arguments of the message in serialise format.'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getText($langcode = Language::LANGCODE_NOT_SPECIFIED, array $options = array()) {

    if (!$message_type = $this->getType()) {
      // Message type does not exist any more.
      return '';
    }

    $output = $message_type->getText($langcode, $options);
    $arguments = $this->getArguments();
    // @todo Why is only the first argument used?
    $arguments = reset($arguments);

    if (is_array($arguments)) {
      foreach ($arguments as $key => $value) {
        if (is_array($value) && !empty($value['callback']) && is_callable($value['callback'])) {

          // A replacement via callback function.
          $value += array('pass message' => FALSE);

          if ($value['pass message']) {
            // Pass the message object as-well.
            $value['callback arguments']['message'] = $this;
          }

          $arguments[$key] = call_user_func_array($value['callback'], $value['arguments']);
        }
      }

      foreach ($output as $key => $value) {
        $output[$key] = new FormattableMarkup($value, $arguments);
      }
    }

    // @todo Re-work/simplify. We shouldn't have to loop through output twice.
    foreach ($output as $key => $value) {
      $output[$key] = \Drupal::token()
        ->replace($value, array('message' => $this), $options);
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function save() {
    $token_options = !empty($this->data['token options']) ? $this->data['token options'] : array();

    $tokens = array();

    // Handle hard coded arguments.
    foreach ($this->getType()->getText() as $text) {
      preg_match_all('/[@|%|\!]\{([a-z0-9:_\-]+?)\}/i', $text, $matches);

      foreach ($matches[1] as $delta => $token) {
        $output = \Drupal::token()->replace('[' . $token . ']', array('message' => $this), $token_options);
        if ($output != '[' . $token . ']') {
          // Token was replaced and token sanitizes.
          $argument = $matches[0][$delta];
          $tokens[$argument] = Markup::create($output);
        }
      }
    }

    $arguments = $this->getArguments();
    $this->setArguments(array_merge($tokens, $arguments));

    parent::save();
  }

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\message\MessageInterface
   *  A message type object ready to be save.
   */
  public static function create(array $values = array()) {
    return parent::create($values);
  }

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\message\MessageInterface
   */
  public static function load($id) {
    return parent::load($id);
  }

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\message\MessageInterface[]
   */
  public static function loadMultiple(array $ids = NULL) {
    return parent::loadMultiple($ids);
  }

  /**
   * {@inheritdoc}
   */
  public static function deleteMultiple($ids) {
    \Drupal::entityTypeManager()->getStorage('message')->delete($ids);
  }

  /**
   * {@inheritdoc}
   */
  public static function queryByType($type) {
    return \Drupal::entityQuery('message')
      ->condition('type', $type)
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return trim(implode("\n", $this->getText()));
  }

}
