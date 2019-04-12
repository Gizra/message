<?php

namespace Drupal\message\Plugin\MessagePurge;

use Drupal\Core\Form\FormStateInterface;
use Drupal\message\MessagePurgeBase;
use Drupal\message\MessageTemplateInterface;

/**
 * Maximal (approximate) amount of messages.
 *
 * @MessagePurge(
 *   id = "quota",
 *   label = @Translation("Quota", context = "MessagePurge"),
 *   description = @Translation("Maximal (approximate) amount of messages to keep."),
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
      '#title' => $this->t('Messages quota'),
      '#description' => $this->t('Maximal (approximate) amount of messages.'),
      '#default_value' => $this->configuration['quota'],
      '#tree' => FALSE,
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
    return [
      'quota' => 1000,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function fetch(MessageTemplateInterface $template) {
    $query = $this->baseQuery($template);
    $result = $query
      // We need some kind of limit in order to get any results, but we really
      // want all of them, so use an arbitrarily large number.
      ->range($this->configuration['quota'], 1000000)
      ->execute();
    return $result;
  }

}
