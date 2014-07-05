<?php

/**
 * @file
 * Contains \Drupal\message\Entity\MessageType.
 */

namespace Drupal\message\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Config\Entity\ConfigEntityInterface;

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
 *     "label" = "label"
 *   },
 *   controllers = {
 *     "form" = {
 *       "add" = "Drupal\message\Form\MessageTypeForm",
 *       "edit" = "Drupal\message\Form\MessageTypeForm",
 *       "delete" = "Drupal\message\Form\MessageTypeDeleteConfirm"
 *     },
 *     "list_builder" = "Drupal\message\MessageTypeListBuilder",
 *   },
 *   links = {
 *     "add-form" = "message.type_add",
 *     "edit-form" = "message_type.edit",
 *     "delete-form" = "message_type.delete"
 *   }
 * )
 */
class MessageType extends ConfigEntityBase implements ConfigEntityInterface {

  /**
   * The ID of this message type.
   *
   * @var string
   */
  public $type;

  /**
   * The UUID of the message type.
   *
   * @var string
   */
  public $uuid;

  /**
   * The human-readable name of the message type.
   *
   * @var string
   */
  public $label;

  /**
   * A brief description of this message type.
   *
   * @var string
   */
  public $description;

  /**
   * The serialised text of the message type.
   *
   * @var String
   */
  public $text;

  /**
   * Holds additional data on the entity.
   *
   * @var array
   */
  public $data;

  /**
   * Return the ID of the entity.
   *
   * @return int|null|string
   */
  public function id() {
    return $this->type;
  }

  /**
   * Overrides Entity::__construct().
   */
  public function ___construct(array $values, $entity_type) {
    parent::__construct($values, $entity_type);
  }

  /**
   * Array with the arguments and their replacement value, or callacbks.
   *
   * The argument keys will be replaced when rendering the message, and it
   * sohuld be prefixed by @, %, ! - similar to way it's done in Drupal
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
   *
   * todo: check if this needed.
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
   *    of the message type. IF not set, the default purge settings defined in the
   *    "Message settings" will apply.
   * - 'days': Optional; Maximal message age in days. IF not set, the default purge settings defined in the
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
   * Retrieves the configured message text in a certain language.
   *
   * @param string $langcode
   *   The language code of the Message text field, the text should be
   *   extracted from.
   * @param array $options
   *   Array of options to pass to the metadata-wrapper:
   *   - 'delta': Optional; If set, returns the output only from a single delta
   *     of the message-text field.
   *
   * @return string
   *   A string with the text from the field.
   */
  public function getText($langcode = NULL, $options = array()) {
    if (!$langcode && \Drupal::moduleHandler()->moduleExists('config_translation')) {
      // The config translation module turned on. Get the proper language.
      $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
    }

    if (!$langcode || !isset($this->text[$langcode])) {
      // We are facing two problems: a language was not supplied or there is no
      // translation for that language. Get the default language.
      $langcode = \Drupal::languageManager()->getDefaultLanguage()->getId();
    }

    // Combine all the field text and return the text.
    return implode(" ", $this->text[$langcode]);
  }

  /**
   * Check if the message is new.
   *
   * todo: Check if message type is exported.
   */
  public function isLocked() {
    return !$this->isNew();
  }

  /**
   * Override the save method.
   */
  function save() {
    if (!empty($this->text) && !is_array($this->text)) {
      // On Drupal 7 the text was field which instantiate on the message type.
      // This was good since the message was an instance of the message type and
      // the text from the message type's field was displayed to the user and
      // the placeholders were replaced on the fly.
      // On Drupal 8 configurable entity is not fieldable and now the text will
      // be held in an array property and the form field will be generated as
      // multiple. The next line ensure the text property will be array.
      $this->text = array($this->text);
    }

    if (!empty($this->data) && !is_array($this->data)) {
      $this->data = array($this->data);
    }

    parent::save();
  }

  /**
   * Setting the text of the message properly.
   *
   * @param Array $text
   *  The message text.
   * @param string $langcode
   *  Optional. The language of the text. When the config translation is on the
   *  language will the current language if not the default will be set to the
   *  default site language.
   */
  public function setText(array $text, $langcode = '') {
    if (!$langcode) {
      if (\Drupal::moduleHandler()->moduleExists('config_translation')) {
        // The config translation module turned on. Get the proper language.
        $langcode = \Drupal::languageManager()->getCurrentLanguage()->id;
      }
      else {
        $langcode = \Drupal::languageManager()->getDefaultLanguage()->id;
      }
    }

    $this->text[$langcode] = $text;
  }

  /**
   * Removing text easily.
   *
   * @param string $langcode
   *  Optional. The language of the text. When the config translation is on the
   *  language will the current language if not the default will be set to the
   *  default site language.
   */
  public function removeText($langcode = '' ) {
    if (!$langcode) {
      if (\Drupal::moduleHandler()->moduleExists('config_translation')) {
        // The config translation module turned on. Get the proper language.
        $langcode = \Drupal::languageManager()->getCurrentLanguage()->id;
      }
      else {
        $langcode = \Drupal::languageManager()->getDefaultLanguage()->id;
      }
    }

    unset($this->text[$langcode]);
  }
}
