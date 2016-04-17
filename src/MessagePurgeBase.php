<?php

namespace Drupal\message;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\message\Entity\Message;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base implementation for OgDeleteOrphans plugins.
 */
abstract class MessagePurgeBase extends PluginBase implements MessagePurgeInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity query object for Message items.
   *
   * @var \Drupal\Core\Entity\Query\QueryInterface
   */
  protected $messageQuery;


  /**
   * Constructs a MessagePurgeBase object.
   *
   * @var array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\Query\QueryInterface $message_query
   *   The entity query object for message items.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, QueryInterface $message_query) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->$messageQuery = $message_query;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity.query')->get('message')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function process(array $ids) {
    $messages = Message::loadMultiple($ids);
    $this->entityTypeManager->getStorage('message')->delete($messages);
  }

  /**
   * Get a base query.
   *
   * @param array $bundles
   *   Array with the message type that need to be queried.
   *
   * @return QueryInterface
   *   The query object.
   */
  protected function baseQuery(array $bundles) {
    return $this->messageQuery
      ->condition('type', $bundles, 'IN')
      ->sort('created', 'DESC')
      ->sort('mid', 'DESC');
  }

  /**
   * {@inheritdoc}
   */
  public function configurationForm($form, FormStateInterface $form_state) {
    return [];
  }

}
