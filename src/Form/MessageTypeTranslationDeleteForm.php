<?php

/**
 * @file
 * Contains \Drupal\config_translation\Form\ConfigTranslationDeleteForm.
 */

namespace Drupal\message\Form;

use Drupal\config_translation\ConfigMapperManagerInterface;
use Drupal\config_translation\Form\ConfigTranslationDeleteForm;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\language\ConfigurableLanguageManagerInterface;
use Drupal\message\Controller\MessageController;
use Drupal\message\Entity\MessageType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Builds a form to delete configuration translation.
 */
class MessageTypeTranslationDeleteForm extends ConfigTranslationDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'message_type_translation_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, Request $request = NULL, $plugin_id = NULL, $langcode = NULL) {
//    $configs = $form_state['config_translation_mapper']->getConfigData();
//    $config = reset($configs);
//
//    $entity = MessageController::MessageTypeLoad($config['type']);
//    $form_state['#entity'] = $entity;
    dpm($form);
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    /** @var MessageType $entity */
    $entity = $form_state['#entity'];
    unset($entity->text[$form_state['config_translation_language']->id]);
    $entity->save();
    parent::submitForm($form, $form_state);
  }
}
