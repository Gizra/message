<?php

/**
 * @file
 * Definition of Drupal\node\Plugin\views\argument\Nid.
 */

namespace Drupal\message\Plugin\views\argument;

use Drupal\views\Plugin\views\argument\Numeric;

/**
 * Argument handler to accept a node id.
 *
 * @ViewsArgument("message_mid")
 */
class Mid extends Numeric {

  /**
   * Override the behavior of title(). Get the title of the node.
   */
  public function titleQuery() {
    $titles = array();

    $nodes = node_load_multiple($this->value);
    foreach ($nodes as $node) {
      $titles[] = check_plain($node->label());
    }
    return $titles;
  }

}
