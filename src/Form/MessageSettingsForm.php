<?php

/**
 * @file
 *
 * Contains \Drupal\system\Form\FileSystemForm.
 */

namespace Drupal\message\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure file system settings for this site.
 */
class MessageSettingsForm extends ConfigFormBase {

  /**
   * The entity manager object.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'message_system_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['message.settings'];
  }

  /**
   * Holds the name of the keys we holds in the variable.
   */
  public function defaultKeys() {
    return [
      'purge_enable',
      'purge_settings',
      'delete_on_entity_delete',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity.manager')
    );
  }

  /**
   * Constructs a \Drupal\system\ConfigFormBase object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager object.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityManagerInterface $entity_manager) {
    parent::__construct($config_factory);
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('message.settings');

    $form['purge'] = [
      '#type' => 'fieldset',
      '#title' => t('Purge settings'),
    ];

    $form['purge']['purge_enable'] = [
      '#type' => 'checkbox',
      '#title' => t('Purge messages'),
      '#description' => t('When enabled, old messages will be deleted.'),
      '#default_value' => $config->get('purge_enable'),
    ];

    $states = [
      'visible' => [
        ':input[name="purge_enable"]' => ['checked' => TRUE],
      ],
    ];

    /** @var \Drupal\message\MessagePurgeInterface $plugin */
    foreach (array_keys(\Drupal::service('plugin.manager.message.purge')->getDefinitions()) as $plugin_id) {
      $plugin = \Drupal::service('plugin.manager.message.purge')->createInstance($plugin_id);
      $element = [];
      $element = $plugin->buildConfigurationForm($element, $form_state);
      $element['#states'] = $states;

      // @todo: Set the default value.
      // $element['#default_value'] = $config->get($plugin_id);

      $form['purge'][$plugin_id] = $element;
    }


    $form['delete_on_entity_delete'] = [
      '#title' => t('Auto delete messages referencing the following entities'),
      '#type' => 'select',
      '#multiple' => TRUE,
      '#options' => $this->getContentEntityTypes(),
      '#default_value' => $config->get('delete_on_entity_delete'),
      '#description' => t('Messages that reference entities of these types will be deleted when the referenced entity gets deleted.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $config = $this->config('message.settings');

    foreach ($this->defaultKeys() as $key) {
      $config->set($key, $form_state->getValue($key));
    }

    $config->save();
  }

  /**
   * Get content entity types keyed by id.
   *
   * @return array
   */
  protected function getContentEntityTypes() {
    $options = [];
    foreach ($this->entityManager->getDefinitions() as $entity_id => $entity_type) {
      if ($entity_type instanceof ContentEntityTypeInterface) {
        $options[$entity_type->id()] = $entity_type->getLabel();
      }
    }
    return $options;
  }

}
