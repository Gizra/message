<?php

/**
 * @file
 * Contains \Drupal\node\NodeTypeForm.
 */

namespace Drupal\message;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldDefinition;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Field\WidgetBase;
use Drupal\entity_reference\Plugin\Field\FieldType\ConfigurableEntityReferenceFieldItemList;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldInstanceConfig;
use Drupal\migrate_drupal\Plugin\migrate\Process\d6\FieldInstanceWidgetSettings;
use Drupal\migrate_drupal\Plugin\migrate\source\d6\FieldInstance;
use Drupal\text\Plugin\Field\FieldWidget\TextareaWidget;

/**
 * Form controller for node type forms.
 */
class MessageTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $form = parent::form($form, $form_state);

    $type = $this->entity;

    $form['label'] = array(
      '#title' => t('Label'),
      '#type' => 'textfield',
      '#default_value' => $type->id(),
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
        'exists' => '\Drupal\message\Controller\MessageController::MessageTypeExists',
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
      '#required' => TRUE,
    );

    // todo: check content translation.
    $form['language'] = array(
      '#title' => t('Field language'),
      '#description' => t('The language code that will be saved with the field values. This is used to allow translation of fields.'),
    );

    // todo: leave for later.
    if (\Drupal::moduleHandler()->moduleExists('content_translation')) {
      $options = array();
      foreach (\Drupal::languageManager()->getLanguages() as $key => $value) {
        if (!empty($value->enabled)) {
          $options[$key] = $value->name;
        }
      }
      $field_language = !empty($form_state['values']['language']) ? $form_state['values']['language'] : \Drupal::languageManager()->getDefaultLanguage()->getId();
      $form['language'] += array(
        '#type' => 'select',
        '#options' => $options,
        '#required' => TRUE,
        '#default_value' => $field_language,
        '#ajax' => array(
          'callback' => 'message_type_fields_ajax_callback',
          'wrapper' => 'message-type-wrapper',
        ),
      );
    }
    else {
      $form['language'] += array(
        '#type' => 'item',
        '#markup' => t('Undefined language'),
      );
    }

    $form['message_type_fields'] = array(
      '#prefix' => '<div id="message-type-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
      '#parents' => array('message_type_fields'),
      'text' => $this->textField($form, $form_state),
    );

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
//      '#default_value' => !empty($this->entity->data['purge']['enabled']),
      '#states' => $states,
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
//      '#element_validate' => array('element_validate_integer_positive'),
      '#states' => $states,
    );

    $form['data']['purge']['days'] = array(
      '#type' => 'textfield',
      '#title' => t('Purge messages older than'),
      '#description' => t('Maximal message age in days, for messages of this type.'),
      '#default_value' => !empty($this->entity->data['purge']['days']) ? $this->entity->data['purge']['days'] : '',
//      '#element_validate' => array('element_validate_integer_positive'),
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
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    // Until the parent method will do something we handle the saving by our
    // self.
    parent::save($form, $form_state);

    // todo: When the parent method will do something remove as much code as we
    // can.
    $this->entity->save();

    $params = array(
      '@type' => $form_state['values']['label'],
    );

    drupal_set_message(t('The message type @type created successfully.', $params));

    $form_state['redirect'] = 'admin/structure/message';
  }

  /**
   * Return the message text element.
   *
   * todo: add token selector, add ckeditor and convert to multiple field.
   */
  private function textField($form, $form_state) {

    // try to create a field in order to use the text area widget. not sure it
    // will work.
//    $field = FieldDefinition::create('text')
//      ->setName('text')
//      ->setCardinality(FieldInstanceConfig::CARDINALITY_UNLIMITED);
//    $form['#parents'] = array();
//    $pluginManager = \Drupal::service('plugin.manager.field.widget');
//    $foo = new TextareaWidget('text_textarea', $pluginManager->getDefinitions(), $field, array());
//
//    $bar = new ConfigurableEntityReferenceFieldItemList($field, 'message_type');
//    $foo->form($bar, $form, $form_state);


    $element = array(
      '#type' => 'container',
    );

    foreach ($this->entity->text as $delta => $text) {
      $element[$delta] = array(
        '#type' => 'textarea',
        '#title' => t('Message text'),
        '#required' => TRUE,
        '#default_value' => $text,
      );
    }

    return $element;
  }
}
