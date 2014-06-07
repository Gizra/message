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
    // todo: do!
    return new Route(
      $this->getBaseRoute()->getPath() . '/translate/{langcode}/edit',
      array(
        // Left for reference. Remove when done.
//        '_form' => '\Drupal\config_translation\Form\ConfigTranslationEditForm',
        '_form' => '\Drupal\message\Form\MessageConfigTranslationEditForm',
        'plugin_id' => $this->getPluginId(),
      ),
      array('_config_translation_form_access' => 'TRUE')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getAddRoute() {
    // todo: do!
    return new Route(
      $this->getBaseRoute()->getPath() . '/translate/{langcode}/add',
      array(
        // Left for reference. Remove when done.
//        '_form' => '\Drupal\config_translation\Form\ConfigTranslationAddForm',
        '_form' => '\Drupal\message\Form\MessageConfigTranslationAddForm',
        'plugin_id' => $this->getPluginId(),
      ),
      array('_config_translation_form_access' => 'TRUE')
    );
  }

}
