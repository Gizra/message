<?php

namespace Drupal\message;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Interface for OgDeleteOrphans plugins.
 *
 * Depending on the needs of a project there are different ways to deal with
 * orphaned memberships and content after a group entity is deleted. This
 * plugin type allows to register a group entity for having its orphans deleted,
 * to customize the query that will gather the orphans, and to start the
 * deletion process.
 *
 * It is up to the implementing plugin to deal with the specifics. A long
 * running batch process will need to store the list of orphans somewhere, and
 * will be responsible for running the deletion to the end.
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
