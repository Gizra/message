<?php

/**
 * @file
 * Contains \Drupal\message\MessageConfigEntityMapper.
 */

namespace Drupal\message;

use Drupal\config_translation\ConfigEntityMapper;
use Symfony\Component\HttpFoundation\Request;

/**
 * Configuration mapper for message config entity. We need to take over the
 * config entity mapper for the message type entity due to the multiple text
 * element.
 */
class MessageConfigEntityMapper extends ConfigEntityMapper {

  /**
   * {@inheritdoc}
   */
  public function populateFromRequest(Request $request) {
    parent::populateFromRequest($request);
    $entity = $request->attributes->get($this->entityType);
    $this->setEntity($entity);
  }
}
