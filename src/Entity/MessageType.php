<?php

/**
 * @file
 * Contains \Drupal\message\Entity\MessageType.
 */

namespace Drupal\message\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\language\ConfigurableLanguageManagerInterface;

/**
 * Defines the Message type entity class.
 *
 * @ConfigEntityType(
 *   id = "message_type",
 *   label = @Translation("Message type"),
 *   config_prefix = "type",
 *   bundle_of = "message",
 *   entity_keys = {
 *     "id" = "type",
 *     "label" = "label",
 *     "langcode" = "langcode",
 *   },
 *   admin_permission = "administer message types",
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\message\Form\MessageTypeForm",
 *       "edit" = "Drupal\message\Form\MessageTypeForm",
 *       "delete" = "Drupal\message\Form\MessageTypeDeleteConfirm"
 *     },
 *     "list_builder" = "Drupal\message\MessageTypeListBuilder",
 *     "view_builder" = "Drupal\message\MessageViewBuilder",
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/message/type/add",
 *     "edit-form" = "/admin/structure/message/manage/{message_type}",
 *     "delete-form" = "/admin/structure/message/delete/{message_type}"
 *   }
 * )
 */
class MessageType extends ConfigEntityBase implements ConfigEntityInterface {

  /**
   * The ID of this message type.
   *
   * @var string
   */
  protected $type;

  /**
   * The UUID of the message type.
   *
   * @var string
   */
  protected $uuid;

  /**
   * The human-readable name of the message type.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of this message type.
   *
   * @var string
   */
  protected $description;

  /**
   * The serialised text of the message type.
   *
   * @var array
   */
  protected $text = array();

  /**
   * Holds additional data on the entity.
   *
   * @var array
   */
  protected $data = array();

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->type;
  }

  /**
   * {@inheritdoc}
   */
  public function setArguments(array $arguments) {
    $this->arguments = $arguments;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getArguments() {
    return $this->arguments;
  }

  /**
   * {@inheritdoc}
   */
  public function setCategory($category) {
    $this->category = $category;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCategory() {
    return $this->category;
  }

  /**
   * {@inheritdoc}
   */
  public function setData(array $data) {
    $this->data = $data;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getData($key = '') {
    if ($key && isset($this->data)) {
      return $this->data[$key];
    }
    // @todo do not return entire data set if key is passed. This could cause
    // logic errors.
    return $this->data;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setLabel($label) {
    $this->label = $label;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * {@inheritdoc}
   */
  public function setType($type) {
    $this->type = $type;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->type;
  }

  /**
   * {@inheritdoc}
   */
  public function setUuid($uuid) {
    $this->uuid = $uuid;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUuid() {
    return $this->uuid;
  }

  /**
   * Array with the arguments and their replacement value, or callacbks.
   *
   * The argument keys will be replaced when rendering the message, and it
   * should be prefixed by @, %, ! - similar to way it's done in Drupal
   * core's t() function.
   *
   * @code
   *
   * // Assuming out message-text is:
   * // %user-name created <a href="@message-url">@message-title</a>
   *
   * $message_type->arguments = array(
   *   // Hard code the argument.
   *   '%user-name' => 'foo',
   *
   *   // Use a callback, and provide callbacks arguments.
   *   // The following example will call Drupal core's url() function to
   *   // get the most up-to-date path of message ID 1.
   *   '@message-url' => array(
   *      'callback' => 'url',
   *      'callback arguments' => array('message/1'),
   *    ),
   *
   *   // Use callback, but instead of passing callback argument, we will
   *   // pass the Message entity itself.
   *   '@message-title' => array(
   *      'callback' => 'example_bar',
   *      'pass message' => TRUE,
   *    ),
   * );
   * @endcode
   *
   * Arguments assigned to message-type can be overriden by the ones
   * assigned to the message.
   *
   * @see message_get_property_values()
   *
   * @var array
   */
  public $arguments = array();

  /**
   * Set the default message category of the message type.
   *
   * @var string
   */
  public $category = NULL;

  /**
   * Serialized array with misc options.
   *
   * Purge settings (under $message_type->data['purge]). Note that the
   * purge settings can be added only to the message-type.
   * - 'enabled': TRUE or FALSE to explicetly enable or disable message
   *    purging. IF not set, the default purge settings defined in the
   *    "Message settings" will apply.
   * - 'quota': Optional; Maximal (approximate) amount of allowed messages
   *    of the message type. IF not set, the default purge settings defined in
   *    the "Message settings" will apply.
   * - 'days': Optional; Maximal message age in days. IF not set, the default
   *    purge settings defined in the
   *    "Message settings" will apply.
   *
   * Token settings:
   * - 'token replace': Indicate if message's text should be passed
   *    through token_replace(). defaults to TRUE.
   * - 'token options': Array with options to be passed to
   *    token_replace().
   *
   * Tokens settings assigned to message-type can be overriden by the ones
   * assigned to the message.
   *
   * @var array
   */
  public $settings = array();

  /**
   * {@inheritdoc}
   */
  public function getText($langcode = NULL, $options = array()) {
    $text = $this->text;

    if ($langcode) {
      $language_manager = \Drupal::languageManager();
      if ($language_manager instanceof ConfigurableLanguageManagerInterface) {
        $config_translation = $language_manager->getLanguageConfigOverride($langcode, 'message.type.' . $this->id());
        if ($translated_text = $config_translation->get('text')) {
          $text = $translated_text;
        }
      }
    }

    if (isset($options['text']) && $options['text']) {

      if (isset($options['delta'])) {
        return $text[$options['delta']];
      }

      return $text;
    }

    if (!isset($text)) {
      return array();
    }

    // Combine all the field text and return it as a trimmed text.
    return trim(implode("\n", $text));
  }

  /**
   * {@inheritdoc}
   */
  public function isLocked() {
    return !$this->isNew();
  }

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\message\MessageTypeInterface
   *   A message type object ready to be save.
   */
  public static function create(array $values = array()) {
    return parent::create($values);
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    $this->text = array_filter($this->text);
    parent::preSave($storage);
  }

}
