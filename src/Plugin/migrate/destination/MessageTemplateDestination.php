<?php

namespace Drupal\message\Plugin\migrate\destination;

use Drupal\migrate\Plugin\migrate\destination\EntityConfigBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\migrate\Row;

/**
 * @MigrateDestination(
 *   id = "entity:message_template"
 * )
 */
class MessageTemplateDestination extends EntityConfigBase {
 
  /**
   * {@inheritdoc}
   */
  protected function updateEntity(EntityInterface $entity, Row $row) {
    parent::updateEntity($entity, $row);
    if ($row->getDestinationProperty('text')) {
      $entity->set('text', $row->getDestinationProperty('text'));
    }
  }

}
