<?php

/**
 * @file
 * Contains \Drupal\message\Entity\MessageType.
 */

namespace Drupal\message\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityNG;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityStorageControllerInterface;
use Drupal\Core\Entity\Annotation\EntityType;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\message\MessageException;
use Drupal\message\MessageTypeInterface;
use Drupal\field\Field;

use Drupal\Core\Entity\Entity;


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
abstract class MessageType extends Entity {

  /**
   * The ID of this message type.
   *
   * @var string
   */
  protected $id;

  /**
   * The machine name of this message type.
   *
   * @var string
   */
  protected $name;

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
  protected $arguments = array();

  /**
   * Set the default message category of the message type.
   *
   * @var string
   */
  protected $category = NULL;

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
  protected $settings = array();

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
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
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

    $properties['settings'] = array(
      'label' => t('Settings'),
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

  /**
   * Checks data value access.
   *
   * @param string $operation
   *   The operation to be performed.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   (optional) The user for which to check access, or NULL to check access
   *   for the current user. Defaults to NULL.
   *
   * @return bool|null
   *   self::ALLOW, self::DENY, or self::KILL.
   */
  public function access($operation, AccountInterface $account = NULL) {
    // TODO: Implement access() method.
  }

  /**
   * Gets a property object.
   *
   * @param $property_name
   *   The name of the property to get; e.g., 'title' or 'name'.
   *
   * @throws \InvalidArgumentException
   *   If an invalid property name is given.
   *
   * @return \Drupal\Core\TypedData\TypedDataInterface
   *   The property object.
   */
  public function get($property_name) {
    // TODO: Implement get() method.
  }

  /**
   * Sets a property value.
   *
   * @param $property_name
   *   The name of the property to set; e.g., 'title' or 'name'.
   * @param $value
   *   The value to set, or NULL to unset the property.
   * @param bool $notify
   *   (optional) Whether to notify the parent object of the change. Defaults to
   *   TRUE. If the update stems from a parent object, set it to FALSE to avoid
   *   being notified again.
   *
   * @throws \InvalidArgumentException
   *   If the specified property does not exist.
   *
   * @return \Drupal\Core\TypedData\TypedDataInterface
   *   The property object.
   */
  public function set($property_name, $value, $notify = TRUE) {
    // TODO: Implement set() method.
  }

  /**
   * Gets an array of property objects.
   *
   * @param bool $include_computed
   *   If set to TRUE, computed properties are included. Defaults to FALSE.
   *
   * @return \Drupal\Core\TypedData\TypedDataInterface[]
   *   An array of property objects implementing the TypedDataInterface, keyed
   *   by property name.
   */
  public function getProperties($include_computed = FALSE) {
    // TODO: Implement getProperties() method.
  }

  /**
   * Determines whether the data structure is empty.
   *
   * @return boolean
   *   TRUE if the data structure is empty, FALSE otherwise.
   */
  public function isEmpty() {
    // TODO: Implement isEmpty() method.
  }

  /**
   * React to changes to a child property.
   *
   * Note that this is invoked after any changes have been applied.
   *
   * @param $property_name
   *   The name of the property which is changed.
   */
  public function onChange($property_name) {
    // TODO: Implement onChange() method.
  }

  /**
   * Marks the translation identified by the given language code as existing.
   *
   * @param string $langcode
   *   The language code identifying the translation to be initialized.
   *
   * @todo Remove this as soon as translation metadata have been converted to
   *    regular fields.
   */
  public function initTranslation($langcode) {
    // TODO: Implement initTranslation() method.
  }

  /**
   * Provides or alters field definitions for a specific bundle.
   *
   * The field definitions returned here for the bundle take precedence on the
   * base field definitions specified by baseFieldDefinitions() for the entity
   * type.
   *
   * @todo Provide a better DX for field overrides.
   *   See https://drupal.org/node/2145115.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition. Useful when a single class is used for multiple,
   *   possibly dynamic entity types.
   * @param string $bundle
   *   The bundle.
   * @param \Drupal\Core\Field\FieldDefinitionInterface[] $base_field_definitions
   *   The list of base field definitions.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface[]
   *   An array of bundle field definitions, keyed by field name.
   *
   * @see \Drupal\Core\Entity\EntityManagerInterface::getFieldDefinitions()
   * @see \Drupal\Core\Entity\ContentEntityInterface::baseFieldDefinitions()
   */
  public static function bundleFieldDefinitions(EntityTypeInterface $entity_type, $bundle, array $base_field_definitions) {
    // TODO: Implement bundleFieldDefinitions() method.
  }

  /**
   * Returns whether the entity has a field with the given name.
   *
   * @param string $field_name
   *   The field name.
   *
   * @return bool
   *   TRUE if the entity has a field with the given name. FALSE otherwise.
   */
  public function hasField($field_name) {
    // TODO: Implement hasField() method.
  }

  /**
   * Gets the definition of a contained field.
   *
   * @param string $name
   *   The name of the field.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface|null
   *   The definition of the field or null if the field does not exist.
   */
  public function getFieldDefinition($name) {
    // TODO: Implement getFieldDefinition() method.
  }

  /**
   * Gets an array of field definitions of all contained fields.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface[]
   *   An array of field definitions, keyed by field name.
   *
   * @see \Drupal\Core\Entity\EntityManagerInterface::getFieldDefinitions()
   */
  public function getFieldDefinitions() {
    // TODO: Implement getFieldDefinitions() method.
  }

  /**
   * Returns an array of all field values.
   *
   * Gets an array of plain field values, including only non-computed values.
   * Note that the structure varies by entity type and bundle.
   *
   * @return array
   *   An array of field values, keyed by field name.
   */
  public function toArray() {
    // TODO: Implement toArray() method.
  }

  /**
   * Returns the entity UUID (Universally Unique Identifier).
   *
   * The UUID is guaranteed to be unique and can be used to identify an entity
   * across multiple systems.
   *
   * @return string|null
   *   The UUID of the entity, or NULL if the entity does not have one.
   */
  public function uuid() {
    // TODO: Implement uuid() method.
  }

  /**
   * Returns the language of the entity.
   *
   * @return \Drupal\Core\Language\Language
   *   The language object.
   */
  public function language() {
    // TODO: Implement language() method.
  }

  /**
   * Returns whether the entity is new.
   *
   * Usually an entity is new if no ID exists for it yet. However, entities may
   * be enforced to be new with existing IDs too.
   *
   * @return bool
   *   TRUE if the entity is new, or FALSE if the entity has already been saved.
   *
   * @see \Drupal\Core\Entity\EntityInterface::enforceIsNew()
   */
  public function isNew() {
    // TODO: Implement isNew() method.
  }

  /**
   * Enforces an entity to be new.
   *
   * Allows migrations to create entities with pre-defined IDs by forcing the
   * entity to be new before saving.
   *
   * @param bool $value
   *   (optional) Whether the entity should be forced to be new. Defaults to
   *   TRUE.
   *
   * @return self
   *
   * @see \Drupal\Core\Entity\EntityInterface::isNew()
   */
  public function enforceIsNew($value = TRUE) {
    // TODO: Implement enforceIsNew() method.
  }

  /**
   * Returns the ID of the type of the entity.
   *
   * @return string
   *   The entity type ID.
   */
  public function getEntityTypeId() {
    // TODO: Implement getEntityTypeId() method.
  }

  /**
   * Returns the bundle of the entity.
   *
   * @return string
   *   The bundle of the entity. Defaults to the entity type ID if the entity
   *   type does not make use of different bundles.
   */
  public function bundle() {
    // TODO: Implement bundle() method.
  }

  /**
   * Returns the label of the entity.
   *
   * @return string|null
   *   The label of the entity, or NULL if there is no label defined.
   */
  public function label() {
    // TODO: Implement label() method.
  }

  /**
   * Returns the URI elements of the entity.
   *
   * URI templates might be set in the links array in an annotation, for
   * example:
   *
   * @code
   * links = {
   *   "canonical" = "node.view",
   *   "edit-form" = "node.page_edit",
   *   "version-history" = "node.revision_overview"
   * }
   * @endcode
   * or specified in a callback function set like:
   * @code
   * uri_callback = "comment_uri",
   * @endcode
   * If the path is not set in the links array, the uri_callback function is
   * used for setting the path. If this does not exist and the link relationship
   * type is canonical, the path is set using the default template:
   * entity/entityType/id.
   *
   * @param string $rel
   *   The link relationship type, for example: canonical or edit-form.
   *
   * @return \Drupal\Core\Url
   */
  public function urlInfo($rel = 'canonical') {
    // TODO: Implement urlInfo() method.
  }

  /**
   * Returns the public URL for this entity.
   *
   * @param string $rel
   *   The link relationship type, for example: canonical or edit-form.
   * @param array $options
   *   See \Drupal\Core\Routing\UrlGeneratorInterface::generateFromRoute() for
   *   the available options.
   *
   * @return string
   *   The URL for this entity.
   */
  public function url($rel = 'canonical', $options = array()) {
    // TODO: Implement url() method.
  }

  /**
   * Returns the internal path for this entity.
   *
   * self::url() will return the full path including any prefixes, fragments, or
   * query strings. This path does not include those.
   *
   * @param string $rel
   *   The link relationship type, for example: canonical or edit-form.
   *
   * @return string
   *   The internal path for this entity.
   */
  public function getSystemPath($rel = 'canonical') {
    // TODO: Implement getSystemPath() method.
  }

  /**
   * Indicates if a link template exists for a given key.
   *
   * @param string $key
   *   The link type.
   *
   * @return bool
   *   TRUE if the link template exists, FALSE otherwise.
   */
  public function hasLinkTemplate($key) {
    // TODO: Implement hasLinkTemplate() method.
  }

  /**
   * Returns a list of URI relationships supported by this entity.
   *
   * @return string[]
   *   An array of link relationships supported by this entity.
   */
  public function uriRelationships() {
    // TODO: Implement uriRelationships() method.
  }

  /**
   * Saves an entity permanently.
   *
   * When saving existing entities, the entity is assumed to be complete,
   * partial updates of entities are not supported.
   *
   * @return int
   *   Either SAVED_NEW or SAVED_UPDATED, depending on the operation performed.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *   In case of failures an exception is thrown.
   */
  public function save() {
    // TODO: Implement save() method.
  }

  /**
   * Deletes an entity permanently.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *   In case of failures an exception is thrown.
   */
  public function delete() {
    // TODO: Implement delete() method.
  }

  /**
   * Acts on an entity before the presave hook is invoked.
   *
   * Used before the entity is saved and before invoking the presave hook.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage object.
   */
  public function preSave(EntityStorageInterface $storage) {
    // TODO: Implement preSave() method.
  }

  /**
   * Acts on a saved entity before the insert or update hook is invoked.
   *
   * Used after the entity is saved, but before invoking the insert or update
   * hook.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage object.
   * @param bool $update
   *   TRUE if the entity has been updated, or FALSE if it has been inserted.
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    // TODO: Implement postSave() method.
  }

  /**
   * Changes the values of an entity before it is created.
   *
   * Load defaults for example.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage object.
   * @param mixed[] $values
   *   An array of values to set, keyed by property name. If the entity type has
   *   bundles the bundle key has to be specified.
   */
  public static function preCreate(EntityStorageInterface $storage, array &$values) {
    // TODO: Implement preCreate() method.
  }

  /**
   * Acts on an entity after it is created but before hooks are invoked.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage object.
   */
  public function postCreate(EntityStorageInterface $storage) {
    // TODO: Implement postCreate() method.
  }

  /**
   * Acts on entities before they are deleted and before hooks are invoked.
   *
   * Used before the entities are deleted and before invoking the delete hook.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage object.
   * @param \Drupal\Core\Entity\EntityInterface[] $entities
   *   An array of entities.
   */
  public static function preDelete(EntityStorageInterface $storage, array $entities) {
    // TODO: Implement preDelete() method.
  }

  /**
   * Acts on deleted entities before the delete hook is invoked.
   *
   * Used after the entities are deleted but before invoking the delete hook.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage object.
   * @param \Drupal\Core\Entity\EntityInterface[] $entities
   *   An array of entities.
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    // TODO: Implement postDelete() method.
  }

  /**
   * Acts on loaded entities.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage object.
   * @param \Drupal\Core\Entity\EntityInterface[] $entities
   *   An array of entities.
   */
  public static function postLoad(EntityStorageInterface $storage, array &$entities) {
    // TODO: Implement postLoad() method.
  }

  /**
   * Creates a duplicate of the entity.
   *
   * @return static
   *   A clone of $this with all identifiers unset, so saving it inserts a new
   *   entity into the storage system.
   */
  public function createDuplicate() {
    // TODO: Implement createDuplicate() method.
  }

  /**
   * Returns the entity type definition.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   *   The entity type definition.
   */
  public function getEntityType() {
    // TODO: Implement getEntityType() method.
  }

  /**
   * Returns a list of entities referenced by this entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of entities.
   */
  public function referencedEntities() {
    // TODO: Implement referencedEntities() method.
  }

  /**
   * Returns the original ID.
   *
   * @return int|string|null
   *   The original ID, or NULL if no ID was set or for entity types that do not
   *   support renames.
   */
  public function getOriginalId() {
    // TODO: Implement getOriginalId() method.
  }

  /**
   * Sets the original ID.
   *
   * @param int|string|null $id
   *   The new ID to set as original ID. If the entity supports renames, setting
   *   NULL will prevent an update from being considered a rename.
   *
   * @return $this
   */
  public function setOriginalId($id) {
    // TODO: Implement setOriginalId() method.
  }

  /**
   * The unique cache tag associated with this entity.
   *
   * @return array
   *   An array of cache tags.
   */
  public function getCacheTag() {
    // TODO: Implement getCacheTag() method.
  }

  /**
   * The list cache tags associated with this entity.
   *
   * Enables code listing entities of this type to ensure that newly created
   * entities show up immediately.
   *
   * @return array
   *   An array of cache tags.
   */
  public function getListCacheTags() {
    // TODO: Implement getListCacheTags() method.
  }

  /**
   * Returns whether a new revision should be created on save.
   *
   * @return bool
   *   TRUE if a new revision should be created.
   *
   * @see \Drupal\Core\Entity\EntityInterface::setNewRevision()
   */
  public function isNewRevision() {
    // TODO: Implement isNewRevision() method.
  }

  /**
   * Enforces an entity to be saved as a new revision.
   *
   * @param bool $value
   *   (optional) Whether a new revision should be saved.
   *
   * @throws \LogicException
   *   Thrown if the entity does not support revisions.
   *
   * @see \Drupal\Core\Entity\EntityInterface::isNewRevision()
   */
  public function setNewRevision($value = TRUE) {
    // TODO: Implement setNewRevision() method.
  }

  /**
   * Returns the revision identifier of the entity.
   *
   * @return
   *   The revision identifier of the entity, or NULL if the entity does not
   *   have a revision identifier.
   */
  public function getRevisionId() {
    // TODO: Implement getRevisionId() method.
  }

  /**
   * Checks if this entity is the default revision.
   *
   * @param bool $new_value
   *   (optional) A Boolean to (re)set the isDefaultRevision flag.
   *
   * @return bool
   *   TRUE if the entity is the default revision, FALSE otherwise. If
   *   $new_value was passed, the previous value is returned.
   */
  public function isDefaultRevision($new_value = NULL) {
    // TODO: Implement isDefaultRevision() method.
  }

  /**
   * Acts on a revision before it gets saved.
   *
   * @param EntityStorageInterface $storage
   *   The entity storage object.
   * @param \stdClass $record
   *   The revision object.
   */
  public function preSaveRevision(EntityStorageInterface $storage, \stdClass $record) {
    // TODO: Implement preSaveRevision() method.
  }

  /**
   * Returns the languages the data is translated to.
   *
   * @param bool $include_default
   *   (optional) Whether the default language should be included. Defaults to
   *   TRUE.
   *
   * @return
   *   An array of language objects, keyed by language codes.
   */
  public function getTranslationLanguages($include_default = TRUE) {
    // TODO: Implement getTranslationLanguages() method.
  }

  /**
   * Gets a translation of the data.
   *
   * The returned translation has to be of the same type than this typed data
   * object. If the specified translation does not exist, a new one will be
   * instantiated.
   *
   * @param $langcode
   *   The language code of the translation to get or Language::LANGCODE_DEFAULT
   *   to get the data in default language.
   *
   * @return \Drupal\Core\TypedData\TypedDataInterface
   *   A typed data object for the translated data.
   */
  public function getTranslation($langcode) {
    // TODO: Implement getTranslation() method.
  }

  /**
   * Returns the translatable object referring to the original language.
   *
   * @return \Drupal\Core\TypedData\TranslatableInterface
   *   The translation object referring to the original language.
   */
  public function getUntranslated() {
    // TODO: Implement getUntranslated() method.
  }

  /**
   * Returns TRUE there is a translation for the given language code.
   *
   * @param string $langcode
   *   The language code identifiying the translation.
   *
   * @return bool
   *   TRUE if the translation exists, FALSE otherwise.
   */
  public function hasTranslation($langcode) {
    // TODO: Implement hasTranslation() method.
  }

  /**
   * Adds a new translation to the translatable object.
   *
   * @param string $langcode
   *   The language code identifying the translation.
   * @param array $values
   *   (optional) An array of initial values to be assigned to the translatable
   *   fields. Defaults to none.
   *
   * @return \Drupal\Core\TypedData\TranslatableInterface
   */
  public function addTranslation($langcode, array $values = array()) {
    // TODO: Implement addTranslation() method.
  }

  /**
   * Removes the translation identified by the given language code.
   *
   * @param string $langcode
   *   The language code identifying the translation to be removed.
   */
  public function removeTranslation($langcode) {
    // TODO: Implement removeTranslation() method.
  }

  /**
   * Returns the translation support status.
   *
   * @return bool
   *   TRUE if the object has translation support enabled.
   */
  public function isTranslatable() {
    // TODO: Implement isTranslatable() method.
  }

  /**
   * Gets the data definition.
   *
   * @return \Drupal\Core\TypedData\DataDefinitionInterface
   *   The data definition object.
   */
  public function getDataDefinition() {
    // TODO: Implement getDataDefinition() method.
  }

  /**
   * Gets the data value.
   *
   * @return mixed
   */
  public function getValue() {
    // TODO: Implement getValue() method.
  }

  /**
   * Sets the data value.
   *
   * @param mixed|null $value
   *   The value to set in the format as documented for the data type or NULL to
   *   unset the data value.
   * @param bool $notify
   *   (optional) Whether to notify the parent object of the change. Defaults to
   *   TRUE. If a property is updated from a parent object, set it to FALSE to
   *   avoid being notified again.
   *
   * @throws \Drupal\Core\TypedData\ReadOnlyException
   *   If the data is read-only.
   */
  public function setValue($value, $notify = TRUE) {
    // TODO: Implement setValue() method.
  }

  /**
   * Returns a string representation of the data.
   *
   * @return string
   */
  public function getString() {
    // TODO: Implement getString() method.
  }

  /**
   * Gets a list of validation constraints.
   *
   * @return array
   *   Array of constraints, each being an instance of
   *   \Symfony\Component\Validator\Constraint.
   */
  public function getConstraints() {
    // TODO: Implement getConstraints() method.
  }

  /**
   * Validates the currently set data value.
   *
   * @return \Symfony\Component\Validator\ConstraintViolationListInterface
   *   A list of constraint violations. If the list is empty, validation
   *   succeeded.
   */
  public function validate() {
    // TODO: Implement validate() method.
  }

  /**
   * Applies the default value.
   *
   * @param bool $notify
   *   (optional) Whether to notify the parent object of the change. Defaults to
   *   TRUE. If a property is updated from a parent object, set it to FALSE to
   *   avoid being notified again.
   *
   * @return \Drupal\Core\TypedData\TypedDataInterface
   *   Returns itself to allow for chaining.
   */
  public function applyDefaultValue($notify = TRUE) {
    // TODO: Implement applyDefaultValue() method.
  }

  /**
   * Returns the name of a property or item.
   *
   * @return string
   *   If the data is a property of some complex data, the name of the property.
   *   If the data is an item of a list, the name is the numeric position of the
   *   item in the list, starting with 0. Otherwise, NULL is returned.
   */
  public function getName() {
    // TODO: Implement getName() method.
  }

  /**
   * Returns the parent data structure; i.e. either complex data or a list.
   *
   * @return \Drupal\Core\TypedData\ComplexDataInterface|\Drupal\Core\TypedData\ListInterface
   *   The parent data structure, either complex data or a list; or NULL if this
   *   is the root of the typed data tree.
   */
  public function getParent() {
    // TODO: Implement getParent() method.
  }

  /**
   * Returns the root of the typed data tree.
   *
   * Returns the root data for a tree of typed data objects; e.g. for an entity
   * field item the root of the tree is its parent entity object.
   *
   * @return \Drupal\Core\TypedData\ComplexDataInterface|\Drupal\Core\TypedData\ListInterface
   *   The root data structure, either complex data or a list.
   */
  public function getRoot() {
    // TODO: Implement getRoot() method.
  }

  /**
   * Returns the property path of the data.
   *
   * The trail of property names relative to the root of the typed data tree,
   * separated by dots; e.g. 'field_text.0.format'.
   *
   * @return string
   *   The property path relative to the root of the typed tree, or an empty
   *   string if this is the root.
   */
  public function getPropertyPath() {
    // TODO: Implement getPropertyPath() method.
  }

  /**
   * Sets the context of a property or item via a context aware parent.
   *
   * This method is supposed to be called by the factory only.
   *
   * @param string $name
   *   (optional) The name of the property or the delta of the list item,
   *   or NULL if it is the root of a typed data tree. Defaults to NULL.
   * @param \Drupal\Core\TypedData\TypedDataInterface $parent
   *   (optional) The parent object of the data property, or NULL if it is the
   *   root of a typed data tree. Defaults to NULL.
   */
  public function setContext($name = NULL, TypedDataInterface $parent = NULL) {
    // TODO: Implement setContext() method.
  }}
