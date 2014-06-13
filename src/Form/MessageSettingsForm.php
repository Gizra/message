<?php

/**
 * @file
 * Contains \Drupal\system\Form\FileSystemForm.
 */

namespace Drupal\message\Form;

use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\Core\Form\ConfigFormBase;

/**
 * Configure file system settings for this site.
 */
class MessageSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'message_system_settings';
  }

  /**
   * Holds the name of the keys we holds in the variable.
   */
  public function defaultKeys() {
    return array('purge_enable', 'purge_quota', 'purge_days', 'delete_on_entity_delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $config = $this->config('message.message');

    $form['purge'] = array(
      '#type' => 'fieldset',
      '#title' => t('Purge settings'),
    );

    $form['purge']['purge_enable'] = array(
      '#type' => 'checkbox',
      '#title' => t('Purge messages'),
      '#description' => t('When enabled, old messages will be deleted.'),
      '#default_value' => $config->get('purge_enable'),
    );

    $states = array(
      'visible' => array(
        ':input[name="purge_enable"]' => array('checked' => TRUE),
      ),
    );

    $form['purge']['purge_quota'] = array(
      '#type' => 'textfield',
      '#title' => t('Messages quota'),
      '#description' => t('Maximal (approximate) amount of messages.'),
      '#default_value' => $config->get('purge_quota'),
//      '#element_validate' => array('element_validate_integer_positive'),
      '#states' => $states,
    );

    $form['purge']['purge_days'] = array(
      '#type' => 'textfield',
      '#title' => t('Purge messages older than'),
      '#description' => t('Maximal message age in days.'),
      '#default_value' => $config->get('purge_quota'),
//      '#element_validate' => array('element_validate_integer_positive'),
      '#states' => $states,
    );


    $options = array();
    foreach (\Drupal::entityManager()->getDefinitions() as $entity_id => $entity) {
      if (!$entity->isFieldable()) {
        continue;
      }

      $options[$entity_id] = $entity->getLabel();
    }

    $entities = $config->get('delete_on_entity_delete') ? $config->get('delete_on_entity_delete') : array('node', 'user', 'taxonomy_term', 'comment');

    $form['delete_on_entity_delete'] = array(
      '#title' => t('Auto delete messages referencing the following entities'),
      '#type' => 'select',
      '#multiple' => TRUE,
      '#options' => $options,
      '#default_value' => $entities,
      '#description' => t('Messages that reference entities of these types will be deleted when the referenced entity gets deleted.'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $config = $this->config('message.message');

    foreach ($this->defaultKeys() as $key) {
      $config->set($key, $form_state['values'][$key]);
    }

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
