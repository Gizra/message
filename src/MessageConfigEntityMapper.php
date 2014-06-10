<?php

/**
 * @file
 * Contains \Drupal\message\MessageConfigEntityMapper.
 */

namespace Drupal\message;

use Drupal\config_translation\ConfigEntityMapper;
use Symfony\Component\Routing\Route;

/**
 * Configuration mapper for message config entity.
 *
 * Why do we need to override the original mapper? As i mentioned in the save
 * method of the entity type message the message type in Drupal 7 was a
 * fieldable entity. Since the message type is a config entity we can't add the
 * field and translate it easily. We solved it by defining a sequence field and
 * managing the partial by our self. The field is managed by the next format:
 *
 * language => array(
 *  'en' => array(
 *    0 => 'First prtial',
 *    1 => 'Second partial',
 *  ),
 *  'fr' => array(
 *    0 => 'première partie',
 *    1 => 'deuxième partie',
 *  ),
 * );
 *
 * When facing the translation we will need to extend the edit/delete/insert
 * forms and add the multiple text field element. The manging of the field will
 * occur in the submission of the forms(edit/delete/insert) and the user won't
 * notice any different.
 *
 * This will also affect the MessageType::getText() method will need to check
 * the current site language all pull the text in the current language.
 *
 * @see MessageType::save()
 * @see MessageType::getText()
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


  /**
   * {@inheritdoc}
   */
  public function getDeleteRoute() {
    return new Route(
      $this->getBaseRoute()->getPath() . '/translate/{langcode}/delete',
      array(
        '_form' => '\Drupal\message\Form\MessageTypeConfigTranslationDeleteForm',
        'plugin_id' => $this->getPluginId(),
      ),
      array('_config_translation_form_access' => 'TRUE')
    );
  }

}
