<?php

/**
 * @file
 * Definition of Drupal\node\Plugin\views\wizard\Node.
 */

namespace Drupal\message\Plugin\views\wizard;

use Drupal\views\Plugin\views\wizard\WizardPluginBase;

/**
 * @todo: replace numbers with constants.
 */

/**
 * Tests creating node views with the wizard.
 *
 * @ViewsWizard(
 *   id = "message",
 *   base_table = "message",
 *   title = @Translation("Message")
 * )
 */
class Message extends WizardPluginBase {

  /**
   * Set default values for the path field options.
   */
  protected $pathField = array(
    'id' => 'mid',
    'table' => 'message',
    'field' => 'mid',
    'exclude' => TRUE,
    'link_to_user' => FALSE,
    'alter' => array(
      'alter_text' => TRUE,
      'text' => 'user/[uid]'
    )
  );

  /**
   * Set default values for the filters.
   */
  protected $filters = array(
    'status' => array(
      'value' => TRUE,
      'table' => 'message',
      'field' => 'status',
      'provider' => 'message',
    )
  );

  /**
   * Overrides Drupal\views\Plugin\views\wizard\WizardPluginBase::defaultDisplayOptions().
   */
  protected function defaultDisplayOptions() {
    $display_options = parent::defaultDisplayOptions();

    // Add permission-based access control.
    $display_options['access']['type'] = 'perm';
    $display_options['access']['provider'] = 'user';
    $display_options['access']['perm'] = 'access user profiles';

    // Remove the default fields, since we are customizing them here.
    unset($display_options['fields']);

    return $display_options;
  }
}
