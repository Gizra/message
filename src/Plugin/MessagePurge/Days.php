<?php

namespace Drupal\message\Plugin\MessagePurge;

use Drupal\Core\Form\FormStateInterface;
use Drupal\message\MessagePurgeBase;

/**
 * Delete messages older than certain days.
 *
 * @OgDeleteOrphans(
 *  id = "days",
 *  label = @Translation("Days", context = "MessagePurge"),
 *  description = @Translation("Delete messages older than certain days."),
 * )
 */
class Days extends MessagePurgeBase {


  /**
   * {@inheritdoc}
   */
  public function configurationForm($form, FormStateInterface $form_state) {
    return [
      '#type' => 'textfield',
      '#title' => t('Purge messages older than'),
      '#description' => t('Maximal message age in days.'),
      '#default_value' => $this->config->get('purge_quota'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function fetch() {
  }

}
