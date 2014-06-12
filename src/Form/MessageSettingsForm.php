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
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $config = $this->config('message.message');

//      '#default_value' => $config->get('path.temporary'),

    $form['purge'] = array(
      '#type' => 'fieldset',
      '#title' => t('Purge settings'),
    );

    $form['purge']['message_purge_enable'] = array(
      '#type' => 'checkbox',
      '#title' => t('Purge messages'),
      '#description' => t('When enabled, old messages will be deleted.'),
//      '#default_value' => variable_get('message_purge_enable', FALSE),
    );

    $states = array(
      'visible' => array(
        ':input[name="message_purge_enable"]' => array('checked' => TRUE),
      ),
    );

    $form['purge']['message_purge_quota'] = array(
      '#type' => 'textfield',
      '#title' => t('Messages quota'),
      '#description' => t('Maximal (approximate) amount of messages.'),
//      '#default_value' => variable_get('message_purge_quota', NULL),
      '#element_validate' => array('element_validate_integer_positive'),
      '#states' => $states,
    );

    $form['purge']['message_purge_days'] = array(
      '#type' => 'textfield',
      '#title' => t('Purge messages older than'),
      '#description' => t('Maximal message age in days.'),
//      '#default_value' => variable_get('message_purge_days', NULL),
      '#element_validate' => array('element_validate_integer_positive'),
      '#states' => $states,
    );

    $options = array();
//    foreach (entity_get_info() as $entity_id => $entity) {
//      $options[$entity_id] = $entity['label'];
//    }

    $form['message_delete_on_entity_delete'] = array(
      '#title' => t('Auto delete messages referencing the following entities'),
      '#type' => 'select',
      '#multiple' => TRUE,
      '#options' => $options,
//      '#default_value' => variable_get('message_delete_on_entity_delete', array('node', 'user', 'taxonomy_term', 'comment')),
      '#description' => t('Messages that reference entities of these types will be deleted when the referenced entity gets deleted.'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $this->config('message.message')
      ->set('file_temporary_path', $form_state['values']['file_private_path'])
      ->save();

    parent::submitForm($form, $form_state);
  }

}
