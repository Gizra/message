<?php
/**
 * @file
 * Contains \Drupal\Tests\message\Unit\Entity\MessageTypeTest.
 */

namespace Drupal\Tests\message\Unit\Entity;

use Drupal\message\Entity\MessageType;
use Drupal\Tests\UnitTestCase;

/**
 * Unit tests for the message type entity.
 *
 * @coversDefaultClass \Drupal\message\Entity\MessageType
 *
 * @group Message
 */
class MessageTypeTest extends UnitTestCase {

  /**
   * A message type entity.
   *
   * @var \Drupal\message\MessageTypeInterface
   */
  protected $messageType;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->messageType = new MessageType([], 'message_type');
  }

  /**
   * Tests getting and setting data.
   *
   * @covers ::setData
   * @covers ::getData
   */
  public function testSetData() {
    $data = [
      'one' => 'foo',
      'two' => 'bar',
    ];
    $this->messageType->setData($data);
    $this->assertArrayEquals($data, $this->messageType->getData());
    $this->assertEquals('foo', $this->messageType->getData('one'));
    $this->assertEquals('bar', $this->messageType->getData('two'));
  }

  /**
   * Tests getting and setting description.
   *
   * @covers ::setDescription
   * @covers ::getDescription
   */
  public function testSetDescription() {
    $description = 'A description';
    $this->messageType->setDescription($description);
    $this->assertEquals($description, $this->messageType->getDescription());
  }

  /**
   * Tests getting and setting label.
   *
   * @covers ::setLabel
   * @covers ::getLabel
   */
  public function testSetLabel() {
    $label = 'A label';
    $this->messageType->setLabel($label);
    $this->assertEquals($label, $this->messageType->getLabel());
  }

  /**
   * Tests getting and setting type.
   *
   * @covers ::setType
   * @covers ::getType
   */
  public function testSetType() {
    $type = 'a_type';
    $this->messageType->setType($type);
    $this->assertEquals($type, $this->messageType->getType());
  }

  /**
   * Tests getting and setting uuid.
   *
   * @covers ::setUuid
   * @covers ::getUuid
   */
  public function testSetUuid() {
    $uuid = 'a-uuid-123';
    $this->messageType->setUuid($uuid);
    $this->assertEquals($uuid, $this->messageType->getUuid());
  }

  /**
   * Tests if the type is locked.
   *
   * @covers ::isLocked
   */
  public function testIsLocked() {
    $this->assertTrue($this->messageType->isLocked());
    $this->messageType->enforceIsNew(TRUE);
    $this->assertFalse($this->messageType->isLocked());
  }

}
