<?php

/**
 * @file
 * Definition of Drupal\node\Plugin\views\field\Type.
 */

namespace Drupal\message\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Provide the message type label.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("message_type")
 */
class Type extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    return $values->_entity->getType()->label();
  }
}
