<?php

/**
 * Contains \MessageArgumentsBase.
 */

abstract class MessageArgumentsBase {

  /**
   * @var Message
   *
   * The message object.
   */
  protected $message;

  /**
   * @return Message
   */
  public function getMessage() {
    return $this->message;
  }

  /**
   * @param Message $message
   *
   * @return MessageArgumentsBase
   */
  public function setMessage(Message $message) {
    $this->message = $message;
    return $this;
  }

  /**
   * Retrieve the arguments info.
   *
   * @return array
   *   The arguments as and their values.
   */
  public function getArguments() {
    $args = array();
    $callbacks = $this->prepare();

    foreach ($callbacks as $arg => $callback) {
      if (!is_callable($callback)) {
        continue;
      }

      $args[$arg] = call_user_func($callback);
    }

    return $args;
  }

  /**
   * @return mixed
   */
  abstract function prepare();
}
