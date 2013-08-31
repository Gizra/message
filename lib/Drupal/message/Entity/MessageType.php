<?php

/**
 * @file
 * Contains \Drupal\message\Entity\MessageType.
 */

namespace Drupal\message\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageControllerInterface;
use Drupal\Core\Entity\Annotation\EntityType;
use Drupal\Core\Annotation\Translation;

/**
 * Defines the Message type configuration entity.
 *
 * @EntityType(
 *   id = "message_type",
 *   label = @Translation("Message type"),
 *   module = "message",
 *   controllers = {
 *     "storage" = "Drupal\Core\Config\Entity\ConfigStorageController"
 *   },
 *   config_prefix = "message.type",
 *   bundle_of = "message",
 *   entity_keys = {
 *     "id" = "type",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   }
 * )
 */
class MessageType extends ConfigEntityBase implements ConfigEntityInterface {

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
  public function isLocked() {
    $locked = \Drupal::state()->get('message.type.locked');
    return isset($locked[$this->id()]) ? $locked[$this->id()] : FALSE;
  }
}
