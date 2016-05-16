<?php

namespace Drupal\message;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base implementation for MessagePurge plugins.
 */
abstract class MessagePurgeBase extends PluginBase implements MessagePurgeInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The purge plugin ID.
   *
   * @var string
   */
  protected $uuid;

  /**
   * The weight of the purge plugin.
   *
   * @var int|string
   */
  protected $weight = '';

  /**
   * Determines if a purge plugin is enabled.
   *
   * @var bool
   */
  protected $enabled = TRUE;

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
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;



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
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, QueryInterface $message_query, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->messageQuery = $message_query;
    $this->configFactory = $config_factory;
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
      $container->get('entity.query')->get('message'),
      $container->get('config.factory')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function process(array $ids) {
    $storage = $this->entityTypeManager->getStorage('message');
    $messages = $storage->loadMultiple($ids);
    $storage->delete($messages);
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
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable'),
      '#description' => t('Determine if @name purge plugin should be enabled.', ['@name' => $this->label()]),
      '#weight' => -10,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->pluginDefinition['label'];
  }

  public function getEnabled() {
    return $this->enabled;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnabled($enabled) {
    $this->enabled = $enabled;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUuid() {
    return $this->uuid;
  }

  /**
   * {@inheritdoc}
   */
  public function setWeight($weight) {
    $this->weight = $weight;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->weight;
  }


  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return array(
      'enabled' => $this->getEnabled(),
      'uuid' => $this->getUuid(),
      'id' => $this->getPluginId(),
      'weight' => $this->getWeight(),
      'data' => $this->configuration,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $configuration += array(
      'enabled' => TRUE,
      'data' => array(),
      'uuid' => '',
      'weight' => '',
    );
    $this->configuration = $configuration['data'] + $this->defaultConfiguration();
    $this->uuid = $configuration['uuid'];
    $this->weight = $configuration['weight'];
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return array();
  }
}
