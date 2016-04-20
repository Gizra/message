<?php

namespace Drupal\message\Plugin\MessagePurge;

use Drupal\Core\Form\FormStateInterface;
use Drupal\message\MessagePurgeBase;

/**
 * Maximal (approximate) amount of messages.
 *
 * @MessagePurge(
 *  id = "quota",
 *  label = @Translation("Quota", context = "MessagePurge"),
 *  description = @Translation("Maximal (approximate) amount of messages."),
 * )
 */
class Quota extends MessagePurgeBase {


  /**
   * {@inheritdoc}
   */
  public function configurationForm($form, FormStateInterface $form_state) {
    return [
      '#type' => 'textfield',
      '#title' => t('Messages quota'),
      '#description' => t('Maximal (approximate) amount of messages.'),
      '#default_value' => $this->configFactory->get('message.settings')->get('purge_quota'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function fetch() {
  }

}
