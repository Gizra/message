<?php

/**
 * @file
 * Contains \Drupal\message\MessageConfigEntityMapper.
 */

namespace Drupal\message;

use Drupal\config_translation\ConfigEntityMapper;
use Symfony\Component\Routing\Route;

/**
 * Configuration mapper for message config entity. We need to take over the
 * config entity mapper for the message type entity due to the multiple text
 * element.
 */
class MessageConfigEntityMapper extends ConfigEntityMapper {

  /**
   * {@inheritdoc}
   */
  public function getEditRoute() {
    return new Route(
      $this->getBaseRoute()->getPath() . '/translate/{langcode}/edit',
      array(
        '_form' => '\Drupal\message\Form\MessageTypeConfigTranslationEditForm',
        'plugin_id' => $this->getPluginId(),
      ),
      array('_config_translation_form_access' => 'TRUE')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getAddRoute() {
    return new Route(
      $this->getBaseRoute()->getPath() . '/translate/{langcode}/add',
      array(
        '_form' => '\Drupal\message\Form\MessageTypeConfigTranslationAddForm',
        'plugin_id' => $this->getPluginId(),
      ),
      array('_config_translation_form_access' => 'TRUE')
    );
  }

}
