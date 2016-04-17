<?php

namespace Drupal\message;

use Drupal\Core\Form\FormStateInterface;

/**
 * Interface for MessagePurge plugins.
 */
interface MessagePurgeInterface {


  /**
   * Fetch the messages that need to be purged.
   */
  public function fetch();


  /**
   * Process the purgeable messages.
   *
   * Normally this is a bulk delete operation.
   *
   * @param array $ids
   *   The message IDs to be processed.
   *
   * @return bool
   *   The result of the process.
   */
  public function process(array $ids);

  /**
   * Returns the configuration form elements specific to this plugin.
   *
   * @param array $form
   *   The form definition array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array $form
   *   The renderable form array representing the entire configuration form.
   */
  public function configurationForm($form, FormStateInterface $form_state);

}
