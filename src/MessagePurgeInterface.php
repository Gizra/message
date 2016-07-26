<?php

namespace Drupal\message;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Interface for MessagePurge plugins.
 */
interface MessagePurgeInterface extends ConfigurablePluginInterface, PluginFormInterface {

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
   * Returns the purge method label.
   *
   * @return string
   *   The purge method label.
   */
  public function label();

  /**
   * Returns the purge method description.
   *
   * @return string
   *   The purge plugin description.
   */
  public function description();

  /**
   * Returns the weight of the purge plugin.
   *
   * @return int
   *   The integer weight of the purge plugin.
   */
  public function getWeight();

  /**
   * Sets the weight for this purge plugin.
   *
   * @param int $weight
   *   The weight for this purge plugin.
   *
   * @return $this
   */
  public function setWeight($weight);

}
