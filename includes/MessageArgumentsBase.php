<?php

abstract class MessageArgumentsBase implements MessageArgumentsInterface {

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
   */
  public function getArgumentsInfo() {
    $arguments = $this->argumentsInfo();
  }
}
