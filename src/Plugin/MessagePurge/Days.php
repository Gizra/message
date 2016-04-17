<?php

namespace Drupal\message\Plugin\MessagePurge;

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
  public function fetch() {
  }

}
