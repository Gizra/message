<?php

namespace Drupal\message;

use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Interface for MessagePurge plugins.
 */
interface MessagePurgeInterface extends PluginFormInterface {


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

}
