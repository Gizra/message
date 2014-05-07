<?php

/**
 * @file
 * Contains \Drupal\message\Entity\MessageCategory.
 */

namespace Drupal\message\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\message\MessageCategoryInterface;

/**
 * Defines the Message category configuration entity.
 *
 * @EntityType(
 *   id = "message_category",
 *   label = @Translation("Message category"),
 *   module = "message",
 *   controllers = {
 *     "storage" = "Drupal\Core\Config\Entity\ConfigStorageController",
 *   },
 *   config_prefix = "message.category",
 *   bundle_of = "message_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   }
 * )
 */
class MessageCategory extends ConfigEntityBase implements MessageCategoryInterface {

  /**
   * The machine name of this message category.
   *
   * @var string
   */
  protected $id;

  /**
   * The UUID of the message category.
   *
   * @var string
   */
  public $uuid;

  /**
   * The human-readable name of the message category.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of this message category.
   *
   * @var string
   */
  protected $description;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->type;
  }

  /**
   * @param string $label
   */
  public function setLabel($label) {
    $this->label = $label;
  }

  /**
   * @return string
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * @param string $description
   */
  public function setDescription($description) {
    $this->description = $description;
  }

  /**
   * @return string
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * @return string
   */
  public function getUuid() {
    return $this->uuid;
  }
}
