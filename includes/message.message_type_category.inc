<?php

/**
 * @file
 * A class used for message types category.
 */

/**
 * @deprecated
 */
class MessageTypeCategory extends Entity {

  /**
   * The machine name of the message type category.
   *
   * @var string
   */
  public $category;

  /**
   * The human-redable name of the message type category.
   *
   * @var string
   */
  public $description = '';

  public function __construct($values = array()) {
    parent::__construct($values, 'message_category');
  }
}
