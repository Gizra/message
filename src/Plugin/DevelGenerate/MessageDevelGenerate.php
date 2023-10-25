<?php

namespace Drupal\message\Plugin\DevelGenerate;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\devel_generate\DevelGenerateBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a VocabularyDevelGenerate plugin.
 *
 * @DevelGenerate(
 *   id = "message",
 *   label = @Translation("messages"),
 *   description = @Translation("Generate a given number of messages. Optionally delete current messages."),
 *   url = "messages",
 *   permission = "administer devel_generate",
 *   settings = {
 *     "num" = 1,
 *     "kill" = FALSE
 *   },
 *   dependencies = {
 *     "message",
 *   },
 * )
 */
class MessageDevelGenerate extends DevelGenerateBase implements ContainerFactoryPluginInterface {

  /**
   * The message storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $messageStorage;

  /**
   * The message type storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $messageTypeStorage;

  /**
   * The url generator service.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * Constructs a new MessageDevelGenerate object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   The message storage.
   * @param \Drupal\Core\Entity\EntityStorageInterface $message_type_storage
   *   The message type storage.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The url generator service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityStorageInterface $entity_storage,
    EntityStorageInterface $message_type_storage,
    UrlGeneratorInterface $url_generator
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->messageStorage = $entity_storage;
    $this->messageTypeStorage = $message_type_storage;
    $this->urlGenerator = $url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $container->get('entity_type.manager')->getStorage('message'),
      $container->get('entity_type.manager')->getStorage('message_template'),
      $container->get('url_generator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $types = $this->messageTypeStorage->loadMultiple();

    if (empty($types)) {
      $create_url = $this->urlGenerator->generateFromRoute('message.type_add');
      $this->setMessage($this->t('You do not have any content types that can be generated. <a href=":create-type">Go create a new content type</a>', [':create-type' => $create_url]), 'error', FALSE);
      return;
    }

    $options = [];

    foreach ($types as $type) {
      $options[$type->id()] = [
        'type' => ['#markup' => $type->label()],
      ];
    }
    $header = [
      'type' => $this->t('Message type'),
    ];

    $form['template'] = [
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $options,
    ];

    $form['num'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of messages?'),
      '#default_value' => $this->getSetting('num'),
      '#required' => TRUE,
      '#min' => 0,
    ];
    $form['kill'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Delete existing messages before generating new ones.'),
      '#default_value' => $this->getSetting('kill'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function generateElements(array $values) {
    if ($values['kill']) {
      $this->deleteMessages();
      $this->setMessage($this->t('Deleted existing messages.'));
    }

    $new_messages = $this->generateMessages($values['num'], $values['template']);
    if (!empty($new_messages)) {
      $this->setMessage($this->t('Created the following new messages: @mess', ['@mess' => implode(', ', $new_messages)]));
    }
  }

  /**
   * Deletes all messages.
   */
  protected function deleteMessages() {
    $messages = $this->messageStorage->loadMultiple();
    $this->messageStorage->delete($messages);
  }

  /**
   * Generates messages.
   *
   * @param int $records
   *   Number of messages to create.
   * @param array $message_template
   *   List of message templates to use.
   *
   * @return array
   *   Array containing the generated messages id.
   */
  protected function generateMessages($records, $message_template) {
    $messages = [];

    $message_template = array_filter($message_template);

    // Insert new data:
    for ($i = 1; $i <= $records; $i++) {
      $template = array_rand($message_template);
      $message = $this->messageStorage->create([
        'template' => $template,
        'langcode' => Language::LANGCODE_NOT_SPECIFIED,
      ]);

      // Populate all fields with sample values.
      $this->populateFields($message);
      $message->save();

      $messages[] = $message->id();
      unset($message);
    }

    return $messages;
  }

  /**
   * {@inheritdoc}
   */
  public function validateDrushParams(array $args, array $options = []) {
    $values = [
      'num' => array_shift($args),
      'kill' => $options['kill'],
    ];

    if ($this->isNumber($values['num']) == FALSE) {
      throw new \Exception(dt('Invalid number of messages: @num.', ['@num' => $values['num']]));
    }

    return $values;
  }

}
