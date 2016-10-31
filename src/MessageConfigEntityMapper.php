<?php

namespace Drupal\message;

use Drupal\config_translation\ConfigEntityMapper;
use Symfony\Component\Routing\Route;

/**
 * Configuration mapper for message config entity.
 *
 * Why do we need to override the original mapper? As i mentioned in the save
 * method of the entity type message the message template in Drupal 7 was a
 * fieldable entity. Since the message template is a config entity we can't add
 * the field and translate it easily. We solved it by defining a sequence field
 * and managing the partial by our self. The field is managed by the next
 * format:
 *
 * @code
 * language => [
 *  'en' => [
 *    0 => 'First prtial',
 *    1 => 'Second partial',
 *  ],
 *  'fr' => [
 *    0 => 'première partie',
 *    1 => 'deuxième partie',
 *  ],
 * ];
 * @endcode
 *
 * When facing the translation we will need to extend the edit/delete/insert
 * forms and add the multiple text field element. The manging of the field will
 * occur in the submission of the forms(edit/delete/insert) and the user won't
 * notice any different.
 *
 * This will also affect the MessageTemplate::getText() method will need to
 * check the current site language all pull the text in the current language.
 *
 * @see MessageTemplate::save()
 * @see MessageTemplate::getText()
 */
class MessageConfigEntityMapper extends ConfigEntityMapper {

  /**
   * {@inheritdoc}
   */
  public function getEditRoute() {
    $route = new Route(
      $this->getBaseRoute()->getPath() . '/translate/{langcode}/edit',
      [
        '_form' => '\Drupal\message\Form\MessageTemplateConfigTranslationEditForm',
        'plugin_id' => $this->getPluginId(),
      ],
      ['_config_translation_form_access' => 'TRUE']
    );

    $this->processRoute($route);
    return $route;
  }

  /**
   * {@inheritdoc}
   */
  public function getAddRoute() {
    $route = new Route(
      $this->getBaseRoute()->getPath() . '/translate/{langcode}/add',
      [
        '_form' => '\Drupal\message\Form\MessageTemplateConfigTranslationAddForm',
        'plugin_id' => $this->getPluginId(),
      ],
      ['_config_translation_form_access' => 'TRUE']
    );

    $this->processRoute($route);
    return $route;
  }

  /**
   * {@inheritdoc}
   */
  public function getDeleteRoute() {
    $route = new Route(
      $this->getBaseRoute()->getPath() . '/translate/{langcode}/delete',
      [
        '_form' => '\Drupal\message\Form\MessageTemplateConfigTranslationDeleteForm',
        'plugin_id' => $this->getPluginId(),
      ],
      ['_config_translation_form_access' => 'TRUE']
    );

    $this->processRoute($route);
    return $route;
  }

}
