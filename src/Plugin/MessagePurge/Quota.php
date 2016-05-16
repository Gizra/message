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
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['quota'] = [
      '#type' => 'number',
      '#min' => 1,
      '#title' => t('Messages quota'),
      '#description' => t('Maximal (approximate) amount of messages.'),
      '#default_value' => $this->configuration['quota'],
    ];

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['quota'] = $form_state->getValue('quota');
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'quota' => 1000,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function fetch() {
  }

}
