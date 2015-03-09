<?php

/**
 * @file
 * Contains \Drupal\message\Plugin\Action\DeleteMessage.
 */

namespace Drupal\message\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\TempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Redirects to a message deletion form.
 *
 * @Action(
 *   id = "message_delete_action",
 *   label = @Translation("Delete selected content"),
 *   type = "message",
 *   confirm_form_path = "admin/content/message/delete"
 * )
 */
class DeleteMessage extends ActionBase implements ContainerFactoryPluginInterface {

  /**
   * The tempstore object.
   *
   * @var \Drupal\user\TempStore
   */
  protected $tempStore;

  /**
   * Constructs a new DeleteMessage object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\user\TempStoreFactory $temp_store_factory
   *   The tempstore factory.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, TempStoreFactory $temp_store_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->tempStore = $temp_store_factory->get('message_multiple_delete_confirm');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('user.private_tempstore'));
  }

  /**
   * {@inheritdoc}
   */
  public function executeMultiple(array $entities) {
    $this->tempStore->set(\Drupal::currentUser()->id(), $entities);
  }

  /**
   * {@inheritdoc}
   */
  public function execute($object = NULL) {
    $this->executeMultiple(array($object));
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    /** @var \Drupal\message\MessageInterface $object */
    return $object->access('delete', $account, $return_as_object);
  }

}
