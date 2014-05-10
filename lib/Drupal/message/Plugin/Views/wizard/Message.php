<?php

/**
 * @file
 * Definition of Drupal\node\Plugin\views\wizard\Node.
 */

namespace Drupal\node\Plugin\views\wizard;

use Drupal\views\Plugin\views\wizard\WizardPluginBase;

/**
 * @todo: replace numbers with constants.
 */

/**
 * Tests creating node views with the wizard.
 *
 * @ViewsWizard(
 *   id = "mid",
 *   base_table = "message",
 *   title = @Translation("Message")
 * )
 */
class Message extends WizardPluginBase {

  /**
   * Set the created column.
   */
  protected $createdColumn = 'node_field_data-created';

  /**
   * Set default values for the path field options.
   */
  protected $pathField = array(
    'id' => 'mid',
    'table' => 'message',
    'field' => 'mid',
    'exclude' => TRUE,
    'link_to_node' => FALSE,
    'alter' => array(
      'alter_text' => TRUE,
      'text' => 'node/[nid]'
    )
  );
}
