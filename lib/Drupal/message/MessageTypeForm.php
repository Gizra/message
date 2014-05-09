<?php

/**
 * @file
 * Contains \Drupal\node\NodeTypeForm.
 */

namespace Drupal\message;

use Drupal\Core\Entity\EntityForm;
use Drupal\Component\Utility\String;
use Drupal\Core\Entity\EntityTypeInterface;

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

    $form['name'] = array(
      '#title' => t('Name'),
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
        'exists' => 'message_type_load',
        'source' => array('name'),
      ),
      '#description' => t('A unique machine-readable name for this message type. It must only contain lowercase letters, numbers, and underscores. This name will be used for constructing the URL of the %message-add page, in which underscores will be converted into hyphens.', array(
        '%message-add' => t('Add message'),
      )),
    );

    // todo: check content translation.
    $form['language'] = array(
      '#title' => t('Field language'),
      '#description' => t('The language code that will be saved with the field values. This is used to allow translation of fields.'),
    );

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

    return $form;

    $form['message_type_fields'] = array(
      '#prefix' => '<div id="message-type-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
      '#parents' => array('message_type_fields'),
    );
    field_attach_form('message_type', $message_type, $form['message_type_fields'], $form_state, $field_language);

    $token_types = module_exists('entity_token') ? array('message') : array();
    if (!$token_types) {
      $form['entity_token'] = array('#markup' => '<p>' . t('Optional: Enable "Entity token" module to use Message and Message-type related tokens.') . '</p>');
    }

    if (module_exists('token')) {
      $form['token_tree'] = array(
        '#theme' => 'token_tree',
        '#token_types' => $token_types + array('all'),
      );

    }
    else {
      $form['token_tree'] = array(
        '#markup' => '<p>' . t("Optional: Install <a href='@token-url'>Token</a> module, to show a the list of available tokens.", array('@token-url' => 'http://drupal.org/project/token')) . '</p>',
      );
    }

    $params = array(
      '@url-rules' => 'http://drupal.org/project/rules',
      '!link' => 'http://api.drupal.org/api/drupal/includes--bootstrap.inc/function/t/7',
    );

    $form['argument_keys'] = array(
      '#title' => t('Replacement tokens'),
      '#type' => 'textfield',
      '#default_value' => implode(', ', (array) $message_type->argument_keys),
      '#description' => t('Optional: For <a href="@url-rules">Rules</a> module, in order to set argument using Rules actions, a comma-separated list of replacement tokens, e.g. %title or !url, of which the message text makes use of. Each replacement token has to start with one of the special characters "@", "%" or "!". This character controls the sanitization method used, analogously to the <a href="!link">t()</a> function.', $params),
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
      '#default_value' => isset($message_type->data['token options']['clear']) ? $message_type->data['token options']['clear'] : FALSE,
    );

    $form['data']['purge'] = array(
      '#type' => 'fieldset',
      '#title' => t('Purge settings'),
    );

    $form['data']['purge']['override'] = array(
      '#title' => t('Override global settings'),
      '#type' => 'checkbox',
      '#description' => t('Override global purge settings for messages of this type.'),
      '#default_value' => !empty($message_type->data['purge']['override']),
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
      '#default_value' => !empty($message_type->data['purge']['enabled']),
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
      '#default_value' => !empty($message_type->data['purge']['quota']) ? $message_type->data['purge']['quota'] : '',
      '#element_validate' => array('element_validate_integer_positive'),
      '#states' => $states,
    );

    $form['data']['purge']['days'] = array(
      '#type' => 'textfield',
      '#title' => t('Purge messages older than'),
      '#description' => t('Maximal message age in days, for messages of this type.'),
      '#default_value' => !empty($message_type->data['purge']['days']) ? $message_type->data['purge']['days'] : '',
      '#element_validate' => array('element_validate_integer_positive'),
      '#states' => $states,
    );

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save message type'),
      '#weight' => 40,
    );

    if (!$message_type->hasStatus(ENTITY_IN_CODE) && $op != 'add') {
      $form['actions']['delete'] = array(
        '#type' => 'submit',
        '#value' => t('Delete message type'),
        '#weight' => 45,
        '#limit_validation_errors' => array(),
        '#submit' => array('message_type_form_submit_delete')
      );
    }
    return $form;

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
  }
}
