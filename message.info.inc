<?php

/**
 * @file
 * Provides Entity metadata integration.
 */

/**
 * Extend the defaults.
 */
class MessageMetadataController extends EntityDefaultMetadataController {

  public function entityPropertyInfo() {
    $info = parent::entityPropertyInfo();
    $properties = &$info[$this->type]['properties'];

    $properties['name'] = array(
      'setter callback' => 'entity_metadata_verbatim_set',
      // @todo: add in permission
      // 'setter permission' => 'administer profiles',
      'required' => TRUE,
      'description' => t('The message type.'),
    ) + $properties['name'];

    // Use "user" instead of the uid property.
    unset($properties['uid']);

    $properties['user'] = array(
      'label' => t("User"),
      'type' => 'user',
      'description' => t("The user for which to log the message."),
      'getter callback' => 'entity_metadata_getter_method',
      'setter callback' => 'entity_metadata_setter_method',
      // @todo: add in permission
      // 'setter permission' => 'administer profiles',
      'required' => TRUE,
    );

    $properties['arguments'] = array(
      'label' => t("Arguments"),
      'type' => 'text',
      'description' => t("The arguments to associate with the message."),
      'getter callback' => 'entity_metadata_verbatim_get',
      'setter callback' => 'entity_metadata_verbatim_set',
      // @todo: add in permission
      // 'setter permission' => 'administer profiles',
    );
    return $info;
  }
}
