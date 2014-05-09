<?php

/**
 * @file
 * Contains \Drupal\message\Entity\MessageType.
 */

namespace Drupal\message\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldDefinition;
use Drupal\Core\Language\Language;
use Drupal\message\MessageException;
use Drupal\field\Field;
use Drupal\Core\Config\Entity\ConfigEntityBase;

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
 *       "add" = "Drupal\message\MessageTypeForm",
 *       "edit" = "Drupal\message\MessageTypeForm",
 *       "delete" = "Drupal\message\Form\MessageTypeDeleteConfirm"
 *     },
 *     "list_builder" = "Drupal\message\NodeTypeListBuilder",
 *   },
 * )
 */
class MessageType extends ConfigEntityBase {

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
  public $text = array();


  public function id() {
    return $this->type;
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
   *   - 'field name': The name of the Message text field, text should be
   *     extracted from.
   *   - 'delta': Optional; If set, returns the output only from a single delta
   *     of the message-text field.
   *
   * @throws \Drupal\message\MessageException
   * @return string A string with the text from the field.
   */
  public function getText($langcode = Language::LANGCODE_NOT_SPECIFIED, $options = array()) {
    return implode(" ", $this->text);
    // Set default values.
    $options += array(
      // The field name from which the text should be extracted.
      'field name' => MESSAGE_FIELD_MESSAGE_TEXT,
    );

    $field_name = $options['field name'];
    $params = array('%field' => $field_name);
    if (!$field = Field::fieldInfo()->getField('message_type', $field_name)) {
      throw new MessageException(format_string('Field %field does not exist.', $params));
    }

    if (empty($field['settings']['message_text'])) {
      throw new MessageException(format_string('Field %field is not a message-text.', $params));
    }

    if (empty($langcode) && \Drupal::moduleHandler()->moduleExists('locale')) {
      // Get the langcode from the current language.
      $language = Drupal::languageManager()->getLanguage();
      $langcode = $language->language;
    }

    // Let the metadata wrapper deal with the language.
    $property = $this->getTranslation($langcode)->$options['field name'];

    if (isset($options['delta'])) {
      $delta = $options['delta'];

      if ($delta >= $property->count()) {
        // Delta is bigger than the existing field, so return early, to
        // prevent an error.
        return;
      }
      return $property->get($delta)->value;
    }
    else {
      $output = '';
      foreach ($property as $item) {
        $output .= $item->value;
      }
      return $output;
    }
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

    parent::save();
  }
}
