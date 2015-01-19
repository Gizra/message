<?php

/**
 * @file
 * Contains EntityValidatorExampleArticleValidator.
 */

class ExampleArguments extends MessageArgumentsBase {

  /**
   * @return mixed
   */
  public function prepare() {
    return array(
      '@name' => array($this, 'processName'),
      '%time' => array($this, 'processTime'),
      '!link' => array($this, 'processLink'),
    );
  }

  /**
   * @return \stdClass.
   */
  private function getAccount() {
    return $this->getMessage()->user();
  }

  /**
   * Process the user current user name.
   */
  public function processName() {
    return $this->getAccount()->name;
  }

  /**
   * Process the current time.
   */
  public function processTime() {
    return format_date($this->getMessage()->timestamp);
  }

  /**
   * Process the link the user profile.
   */
  public function processLink() {
    $uri = entity_uri('user', $this->getAccount());
    return l(t('link'), $uri['path'], array('absolute' => TRUE));
  }
}
