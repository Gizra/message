<?php

namespace Drupal\message\Plugin\MessagePurge;

use Drupal\Core\Form\FormStateInterface;
use Drupal\message\MessagePurgeBase;

/**
 * Delete messages older than certain days.
 *
 * @MessagePurge(
 *  id = "days",
 *  label = @Translation("Days", context = "MessagePurge"),
 *  description = @Translation("Delete messages older than certain days."),
 * )
 */
class Days extends MessagePurgeBase {


  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['days'] = [
      '#type' => 'textfield',
      '#title' => t('Messages older than'),
      '#description' => t('Maximal message age in days.'),
      '#default_value' => $this->configFactory->get('message.settings')->get('purge_quota'),
    ];

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['days'] = $form_state->getValue('days');
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'days' => 0,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function fetch() {
  }

}
