<?php

namespace Drupal\Tests\message\Unit\Entity;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\message\Entity\Message;
use Drupal\message\Entity\MessageTemplate;
use Drupal\message\MessageTemplateInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;

/**
 * Unit tests for the Message entity.
 *
 * @coversDefaultClass \Drupal\message\Entity\Message
 */
class MessageTest extends UnitTestCase {

  /**
   * Message entity to test.
   *
   * @var \Drupal\message\MessageInterface
   */
  protected $message;

  /**
   * The entity manager mock.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    // Mock entity type definition.
    $entity_type = $this->prophesize(EntityTypeInterface::class);
    $entity_type->getKeys()->willReturn([
      'id' => 'id',
      'uuid' => 'uuid',
      'langcode' => 'langcode',
      'bundle' => 'template',
    ]);
    $entity_type->getKey('default_langcode')->willReturn('yy');
    $entity_type->getKey('langcode')->willReturn('xx');
    $entity_type->getKey('id')->willReturn('id');

    // Storage.
    $storage = $this->prophesize(EntityStorageInterface::class)->reveal();

    // Field type manager.
    $field_type_manager = $this->prophesize(FieldTypePluginManagerInterface::class);
    $field_type_manager->getDefaultStorageSettings(Argument::any())->willReturn([]);
    $field_type_manager->getDefaultFieldSettings(Argument::any())->willReturn([]);
    $field_type_manager->createFieldItemList(Argument::any(), Argument::any(), Argument::any())->willReturn($this->prophesize(FieldItemListInterface::class)->reveal());

    // Stub out a minimal container.
    $container = new ContainerBuilder();
    $container->set('plugin.manager.field.field_type', $field_type_manager->reveal());
    \Drupal::setContainer($container);

    // Mocked field definitions.
    $field_definitions = [
      'template' => BaseFieldDefinition::create('string'),
    ];

    // Setup an entity manager.
    $entity_manager = $this->prophesize(EntityManagerInterface::class);
    $entity_manager->getDefinition('message')->willReturn($entity_type->reveal());
    $entity_manager->getFieldDefinitions('message', 'message')->willReturn($field_definitions);
    $entity_manager->getEntityTypeFromClass(MessageTemplate::class)->willReturn('message');
    $entity_manager->getStorage('message')->willReturn($storage);
    $this->entityManager = $entity_manager->reveal();

    \Drupal::getContainer()->set('entity.manager', $this->entityManager);
    // $container->set('uuid', $this->uuid);
    //   $container->set('typed_data_manager', $this->typedDataManager);
    //  $container->set('language_manager', $this->languageManager);
    // $container->set('plugin.manager.field.field_type', $this->fieldTypePluginManager);.
    $this->message = new Message(['id' => 42], 'message');
  }

  /**
   * Tests getText.
   *
   * @covers ::getText
   * @covers ::setTemplate
   */
  public function testGetText() {
    // If no template exists, empty array should be returned.
    $this->assertEmpty($this->message->getText());

    // Mock a template.
    $template = $this->prophesize(MessageTemplateInterface::class);
    $template->getText()->willReturn(['foo', 'bar', 'baz']);
    $this->message->setTemplate($template->reveal());
    $this->assertEquals([], $this->message->getText());
  }

}
