<?php

namespace Drupal\message\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Check and then delete messages.
 *
 * @QueueWorker(
 *   id = "message_check_delete",
 *   title = @Translation("Delete messages if an entity is referenced"),
 *   cron = {"time" = 10}
 * )
 */
class MessageCheckAndDeleteWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The message storage handler.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageInterface
   */
  protected $messageStorage;

  /**
   * Constructs a new MessageCheckAndDeleteWorker object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->messageStorage = $entity_type_manager->getStorage('message');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    // $data is expected to be an array where the keys are message IDs and the
    // values are arrays of multi-valued entity reference field names to check.
    if (!empty($data)) {

      // Check messages with multiple cardinality references; Only delete such
      // messages if the entity being deleted is the last one referenced by the
      // message.
      $messages = $this->messageStorage->loadMultiple(array_keys($data));
      foreach ($data as $id => $fields) {
        foreach ($fields as $field_name) {
          if(isset($messages[$id])) {
            $message = $messages[$id];
            if (count($message->get($field_name)->referencedEntities()) === 0) {
              $this->messageStorage->delete([$message]);
              // As soon as one field qualifies, we can delete the entity. No need
              // to check the other fields.
              break;
            }
          }
        }
      }
    }
  }

}
