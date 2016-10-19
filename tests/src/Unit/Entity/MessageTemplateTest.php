<?php

namespace Drupal\Tests\message\Unit\Entity;

use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\language\ConfigurableLanguageManagerInterface;
use Drupal\message\Entity\MessageTemplate;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Unit tests for the message template entity.
 *
 * @coversDefaultClass \Drupal\message\Entity\MessageTemplate
 *
 * @group Message
 */
class MessageTemplateTest extends UnitTestCase {

  /**
   * A message template entity.
   *
   * @var \Drupal\message\MessageTemplateInterface
   */
  protected $messageTemplate;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->messageTemplate = new MessageTemplate(['template' => 'foo_template'], 'message_template');
  }

  /**
   * Test the ID method.
   *
   * @covers ::id
   */
  public function testId() {
    $this->assertSame('foo_template', $this->messageTemplate->id());
  }

  /**
   * Tests getting and setting the Settings array.
   *
   * @covers ::setSettings
   * @covers ::getSettings
   * @covers ::getSettings
   */
  public function testSetSettings() {
    $settings = [
      'one' => 'foo',
      'two' => 'bar',
    ];

    $this->messageTemplate->setSettings($settings);
    $this->assertArrayEquals($settings, $this->messageTemplate->getSettings());
    $this->assertEquals($this->messageTemplate->getSetting('one'), $this->messageTemplate->getSetting('one'));
    $this->assertEquals('bar', $this->messageTemplate->getSetting('two'));
  }

  /**
   * Tests getting and setting description.
   *
   * @covers ::setDescription
   * @covers ::getDescription
   */
  public function testSetDescription() {
    $description = 'A description';

    $this->messageTemplate->setDescription($description);
    $this->assertEquals($description, $this->messageTemplate->getDescription());
  }

  /**
   * Tests getting and setting label.
   *
   * @covers ::setLabel
   * @covers ::getLabel
   */
  public function testSetLabel() {
    $label = 'A label';
    $this->messageTemplate->setLabel($label);
    $this->assertEquals($label, $this->messageTemplate->getLabel());
  }

  /**
   * Tests getting and setting template.
   *
   * @covers ::setTemplate
   * @covers ::getTemplate
   */
  public function testSetTemplate() {
    $template = 'a_template';
    $this->messageTemplate->setTemplate($template);
    $this->assertEquals($template, $this->messageTemplate->getTemplate());
  }

  /**
   * Tests getting and setting uuid.
   *
   * @covers ::setUuid
   * @covers ::getUuid
   */
  public function testSetUuid() {
    $uuid = 'a-uuid-123';
    $this->messageTemplate->setUuid($uuid);
    $this->assertEquals($uuid, $this->messageTemplate->getUuid());
  }

  /**
   * Tests if the template is locked.
   *
   * @covers ::isLocked
   */
  public function testIsLocked() {
    $this->assertTrue($this->messageTemplate->isLocked());
    $this->messageTemplate->enforceIsNew(TRUE);
    $this->assertFalse($this->messageTemplate->isLocked());
  }

  /**
   * Tests the getText method.
   *
   * @covers ::getText
   */
  public function testGetText() {
    // Mock a language manager.
    $container = new ContainerBuilder();
    $language_manager = $this->prophesize(LanguageManagerInterface::class)->reveal();
    $container->set('language_manager', $language_manager);
    \Drupal::setContainer($container);

    // Should be empty by default.
    $this->assertEmpty($this->messageTemplate->getText());

    // Setup a renderer.
    $renderer = $this->prophesize(RendererInterface::class);

    // Set some text.
    $text = [
      ['value' => 'foo text', 'format' => 'foo_format'],
      ['value' => 'bar text', 'format' => 'bar_format'],
    ];
    $expected_build = [
      '#type' => 'processed_text',
      '#text' => $text[0]['value'],
      '#format' => $text[0]['format'],
      '#langcode' => Language::LANGCODE_NOT_SPECIFIED,
    ];
    $renderer->renderPlain($expected_build)->willReturn('<div>foo text</div>');
    $expected_build = [
      '#type' => 'processed_text',
      '#text' => $text[1]['value'],
      '#format' => $text[1]['format'],
      '#langcode' => Language::LANGCODE_NOT_SPECIFIED,
    ];
    $renderer->renderPlain($expected_build)->willReturn('bar text');
    \Drupal::getContainer()->set('renderer', $renderer->reveal());

    $this->messageTemplate->set('text', $text);
    $expected = [
      '<div>foo text</div>',
      'bar text',
    ];
    $this->assertEquals($expected, $this->messageTemplate->getText());

    // Test specific delta.
    $this->assertEquals($expected[1], $this->messageTemplate->getText(Language::LANGCODE_NOT_SPECIFIED, 1));

    // Non-existent delta.
    $this->assertEmpty($this->messageTemplate->getText(Language::LANGCODE_NOT_SPECIFIED, 42));

    // Default language with configurable languages available.
    $default_language = $this->prophesize(Language::class);
    $default_language->getId()->willReturn('hu');
    $language_manager = $this->prophesize(ConfigurableLanguageManagerInterface::class);
    $language_manager->getDefaultLanguage()->willReturn($default_language);
    $language_manager->getLanguageConfigOverride('hu', 'message.template.foo_template')->willReturn($this->messageTemplate);
    \Drupal::getContainer()->set('language_manager', $language_manager->reveal());

    $renderer = $this->prophesize(RendererInterface::class);
    $expected_build = [
      '#type' => 'processed_text',
      '#text' => $text[0]['value'],
      '#format' => $text[0]['format'],
      '#langcode' => 'hu',
    ];
    $renderer->renderPlain($expected_build)->willReturn('<div>foo text</div>');
    $expected_build = [
      '#type' => 'processed_text',
      '#text' => $text[1]['value'],
      '#format' => $text[1]['format'],
      '#langcode' => 'hu',
    ];
    $renderer->renderPlain($expected_build)->willReturn('bar text');
    \Drupal::getContainer()->set('renderer', $renderer->reveal());

    $expected = [
      '<div>foo text</div>',
      'bar text',
    ];
    $this->assertEquals($expected, $this->messageTemplate->getText());

    // Language without translation should return empty array.
    // @todo Is this correct? Why not return untranslated text?
    $default_language = $this->prophesize(Language::class);
    $mock_template = $this->prophesize(MessageTemplate::class);
    $language_manager->getLanguageConfigOverride('xx', 'message.template.foo_template')->willReturn($mock_template->reveal());
    \Drupal::getContainer()->set('language_manager', $language_manager->reveal());
    $this->assertEmpty($this->messageTemplate->getText('xx'));
  }

}
