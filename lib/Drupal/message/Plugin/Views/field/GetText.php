<?php

/**
 * @file
 * Definition of Drupal\taxonomy\Plugin\views\field\Language.
 */

namespace Drupal\message\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to present a term edit link.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("get_text")
 */
class GetText extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    return $values->_entity->getText();
  }

}
