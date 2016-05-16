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
   * Returns the image effect label.
   *
   * @return string
   *   The image effect label.
   */
  public function label();

  /**
   * Returns the unique ID representing the image effect.
   *
   * @return string
   *   The image effect ID.
   */
  public function getUuid();

  /**
   * Returns the weight of the image effect.
   *
   * @return int|string
   *   Either the integer weight of the image effect, or an empty string.
   */
  public function getWeight();

  /**
   * Sets the weight for this image effect.
   *
   * @param int $weight
   *   The weight for this image effect.
   *
   * @return $this
   */
  public function setWeight($weight);

  /**
   * Returns if a purge plugin is enabled.
   *
   * @return bool
   */
  public function getEnabled();

  /**
   * Sets the weight for this purge plugin.
   *
   * @param bool $enabled
   *   The status of the purge plugin.
   *
   * @return $this
   */
  public function setEnabled($enabled);
}
