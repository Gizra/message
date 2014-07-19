<?php

/**
 * @file
 * Contains \Drupal\message\MessageTypeForm.
 */

namespace Drupal\message\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\message\Entity\Message;
use Drupal\message\Entity\MessageType;
use Drupal\message\FormElement\MessageTypeMultipleTextField;

/**
 * Form controller for node type forms.
 */
class MessageTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $form = parent::form($form, $form_state);

    /** @var MessageType $type */
    $type = $this->entity;

    $form['label'] = array(
      '#title' => t('Label'),
      '#type' => 'textfield',
      '#default_value' => $type->label(),
      '#description' => t('The human-readable name of this message type. This text will be displayed as part of the list on the <em>Add message</em> page. It is recommended that this name begin with a capital letter and contain only letters, numbers, and spaces. This name must be unique.'),
      '#required' => TRUE,
      '#size' => 30,
    );

    $form['type'] = array(
      '#type' => 'machine_name',
      '#default_value' => $type->id(),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#disabled' => $type->isLocked(),
      '#machine_name' => array(
        'exists' => '\Drupal\message\Entity\MessageType::load',
        'source' => array('label'),
      ),
      '#description' => t('A unique machine-readable name for this message type. It must only contain lowercase letters, numbers, and underscores. This name will be used for constructing the URL of the %message-add page, in which underscores will be converted into hyphens.', array(
        '%message-add' => t('Add message'),
      )),
    );

    $form['description'] = array(
      '#title' => t('Description'),
      '#type' => 'textfield',
      '#default_value' => $this->entity->description,
      '#description' => t('The human-readable description of this message type.'),
    );

    $multiple = new MessageTypeMultipleTextField($this->entity, array(get_class($this), 'addMoreAjax'));
    $multiple->textField($form, $form_state);

    $form['data'] = array(
      // Placeholder for other module to add their settings, that should be added
      // to the data column.
      '#tree' => TRUE,
    );

    $form['data']['token options']['clear'] = array(
      '#title' => t('Clear empty tokens'),
      '#type' => 'checkbox',
      '#description' => t('When this option is selected, empty tokens will be removed from display.'),
      '#default_value' => isset($this->entity->data['token options']['clear']) ? $this->entity->data['token options']['clear'] : FALSE,
    );

    $form['data']['purge'] = array(
      '#type' => 'fieldset',
      '#title' => t('Purge settings'),
    );

    $form['data']['purge']['override'] = array(
      '#title' => t('Override global settings'),
      '#type' => 'checkbox',
      '#description' => t('Override global purge settings for messages of this type.'),
      '#default_value' => !empty($this->entity->data['purge']['override']),
    );

    $states = array(
      'visible' => array(
        ':input[name="data[purge][override]"]' => array('checked' => TRUE),
      ),
    );

    $form['data']['purge']['enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Purge messages'),
      '#description' => t('When enabled, old messages will be deleted.'),
      '#states' => $states,
      '#default_value' => !empty($this->entity->data['purge']['enabled']) ? TRUE : FALSE,
    );

    $states = array(
      'visible' => array(
        ':input[name="data[purge][enabled]"]' => array('checked' => TRUE),
      ),
    );

    $form['data']['purge']['quota'] = array(
      '#type' => 'textfield',
      '#title' => t('Messages quota'),
      '#description' => t('Maximal (approximate) amount of messages of this type.'),
      '#default_value' => !empty($this->entity->data['purge']['quota']) ? $this->entity->data['purge']['quota'] : '',
      '#states' => $states,
    );

    $form['data']['purge']['days'] = array(
      '#type' => 'textfield',
      '#title' => t('Purge messages older than'),
      '#description' => t('Maximal message age in days, for messages of this type.'),
      '#default_value' => !empty($this->entity->data['purge']['days']) ? $this->entity->data['purge']['days'] : '',
      '#states' => $states,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, array &$form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = t('Save message type');
    $actions['delete']['#value'] = t('Delete message type');
    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, array &$form_state) {
    parent::validate($form, $form_state);
  }

  /**
   * Ajax callback for the "Add another item" button.
   *
   * This returns the new page content to replace the page content made obsolete
   * by the form submission.
   */
  public static function addMoreAjax(array $form, array $form_state) {
    return $form['text'];
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    // Until the parent method will do something we handle the saving by our
    // self.
    parent::save($form, $form_state);

    // Saving the message text values.
    $message_text = array();

    // todo: Handle weight order.
    foreach ($form_state['values']['text'] as $text) {
      if (empty($text['value'])) {
        continue;
      }
      $message_text[] = $text['value'];
    }

    // Updating the message text.
    $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();

    // todo: check if we need this or just move this to save method.
//    foreach ($this->entity->getText() as $key => $value) {
//      if (is_int($key)) {
//        unset($this->entity->text[$key]);
//      }
//    }

    $this->entity->setText($message_text, $langcode);

    // todo: When the parent method will do something remove as much code as we
    // can.
    $this->entity->save();

    $params = array(
      '@type' => $form_state['values']['label'],
    );

    drupal_set_message(t('The message type @type created successfully.', $params));

    $form_state['redirect'] = 'admin/structure/message';
  }
}
