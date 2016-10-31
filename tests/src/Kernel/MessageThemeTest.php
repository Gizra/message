<?php

namespace Drupal\Tests\message\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\message\Entity\Message;
use Drupal\simpletest\UserCreationTrait;

/**
 * Test theming of messages.
 *
 * @group message
 */
class MessageThemeTest extends KernelTestBase {

  use MessageTemplateCreateTrait;
  use UserCreationTrait;

  /**
   * User account.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['message', 'user', 'system', 'filter'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->installEntitySchema('message');
    $this->installEntitySchema('user');
    $this->installSchema('system', ['sequences']);
    $this->installConfig(['filter']);

    $this->account = $this->createUser();
  }

  /**
   * Test that message render returns message text wrapped in a div.
   */
  public function testMessageTextWrapper() {
    $template = 'dummy_message';
    // Create message to be rendered.
    $message_template = $this->createMessageTemplate($template, 'Dummy message', '', ['Text to be wrapped by div.']);
    $message = Message::create(['template' => $message_template->id()])
      ->setOwner($this->account);

    $message->save();

    // Simulate theming of the message.
    $build = $this->container->get('entity_type.manager')->getViewBuilder('message')->view($message);
    $output = $this->container->get('renderer')->renderRoot($build);
    $this->setRawContent($output);
    $xpath = $this->xpath('//div');

    // @todo Something is off here, as *only* the div is there, no content.
    // @see https://github.com/Gizra/message/issues/128
    $this->assertTrue($xpath, 'A div has been found wrapping the message text.');
  }

}
