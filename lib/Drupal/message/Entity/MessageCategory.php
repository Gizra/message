<?php

/**
 * @file
 * Contains \Drupal\message\Entity\MessageCategory.
 */

namespace Drupal\message\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityStorageControllerInterface;
use Drupal\Core\Entity\Annotation\EntityType;
use Drupal\Core\Annotation\Translation;

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
 *     "id" = "type",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   }
 * )
 */
class MessageCategory extends ConfigEntityBase implements ConfigEntityInterface {

  /**
   * The machine name of this node type.
   *
   * @var string
   *
   * @todo Rename to $id.
   */
  public $type;

  /**
   * The UUID of the node type.
   *
   * @var string
   */
  public $uuid;

  /**
   * The human-readable name of the node type.
   *
   * @var string
   */
  public $label;

  /**
   * A brief description of this node type.
   *
   * @var string
   */
  public $description;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->type;
  }

}
