<?php

/**
 * @file
 * Contains \Drupal\message\Entity\MessageType.
 */

namespace Drupal\message\Entity;

use Drupal\Core\Entity\EntityNG;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityStorageControllerInterface;
use Drupal\Core\Entity\Annotation\EntityType;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Language\Language;
use Drupal\message\MessageException;
use Drupal\message\Entity\MessageTypeInterface;
use Drupal\field\Field;

/**
 * Defines the Message type entity class.
 *
 * @EntityType(
 *   id = "message_type",
 *   label = @Translation("Message type"),
 *   module = "message",
 *   controllers = {
 *     "storage" = "Drupal\Core\Entity\DatabaseStorageControllerNG"
 *   },
 *   config_prefix = "message.type",
 *   bundle_of = "message",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "category",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   bundle_keys = {
 *     "bundle" = "type"
 *   },
 *   base_table = "message_type"
 * )
 */
class MessageType extends EntityNG implements MessageTypeInterface {

  /**
   * The ID of this message type.
   *
   * @var string
   */
  private $id;

  /**
   * The machine name of this message type.
   *
   * @var string
   */
  private $name;

  /**
   * The UUID of the message type.
   *
   * @var string
   */
  private $uuid;

  /**
   * The human-readable name of the message type.
   *
   * @var string
   */
  private $label;

  /**
   * A brief description of this message type.
   *
   * @var string
   */
  private $description;

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
  private $arguments = array();

  /**
   * Set the default message category of the message type.
   *
   * @var string
   */
  private $category = NULL;

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
   * @see message_get_property_values()
   *
   * @var array
   */
  private $data = array();

  /**
   * @todo: Remove this from schema.
   */
  private $argument_keys = array();

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->get('id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function uri() {
    return array(
      'path' => 'admin/structure/messages/manage/' . $this->id(),
      'options' => array(
        'entity_type' => $this->entityType,
        'entity' => $this,
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions($entity_type) {
    $properties['id'] = array(
      'label' => t('Message type ID'),
      'description' => t('The message type ID.'),
      'type' => 'integer_field',
      'read-only' => TRUE,
    );
    $properties['uuid'] = array(
      'label' => t('UUID'),
      'description' => t('The term UUID.'),
      'type' => 'uuid_field',
      'read-only' => TRUE,
    );
    $properties['langcode'] = array(
      'label' => t('Language code'),
      'description' => t('The term language code.'),
      'type' => 'language_field',
    );
    $properties['name'] = array(
      'label' => t('Name'),
      'description' => t('The message type name.'),
      'type' => 'string_field',
    );
    $properties['description'] = array(
      'label' => t('Description'),
      'description' => t('A description of the message type.'),
      'type' => 'string_field',
    );
    $properties['category'] = array(
      'label' => t('Catgeory'),
      'description' => t('The message category.'),
      'type' => 'string_field',
    );

    $properties['arguments'] = array(
      'label' => t('Arguments'),
      'description' => t('A serialized array of arguments.'),
      'type' => 'string_field',
    );

    // @todo: Remove.
    $properties['argument_keys'] = array(
      'label' => t('Arguments'),
      'description' => t('A serialized array of arguments.'),
      'type' => 'string_field',
    );

    $properties['data'] = array(
      'label' => t('Data'),
      'description' => t('A serialized array of settings override.'),
      'type' => 'string_field',
    );

    return $properties;
  }

  /**
   * Retrieves the configured message text in a certain language.
   *
   * @param $langcode
   *   The language code of the Message text field, the text should be
   *   extracted from.
   * @param $options
   *   Array of options to pass to the metadata-wrapper:
   *   - 'field name': The name of the Message text field, text should be
   *     extracted from.
   *   - 'delta': Optional; If set, returns the output only from a single delta
   *     of the message-text field.
   *
   * @return
   *   A string with the text from the field.
   */
  public function getText($langcode = Language::LANGCODE_NOT_SPECIFIED, $options = array()) {
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
}
