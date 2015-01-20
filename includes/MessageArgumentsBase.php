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
    $arguments = array();
    $callbacks = $this->prepare();

    foreach ($callbacks as $argument => $callback) {
      $arguments[$argument] = call_user_func($callback);
    }

    return $arguments;
  }

  /**
   * @return mixed
   */
  abstract function prepare();
}
