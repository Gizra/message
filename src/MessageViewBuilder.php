<?php

/**
 * @file
 * Definition of Drupal\message\MessageViewBuilder.
 */

namespace Drupal\message;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;

/**
 * Render controller for Messages.
 */
class MessageViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $build = parent::view($entity, $view_mode, $langcode);

    // Load the partials in the correct language.
    $partials = $entity->getType()->getText(NULL, array('text' => TRUE));

    if (!$langcode) {
      $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
    }
    else {
      if (\Drupal::moduleHandler()->moduleExists('config_translation') && !isset($partials[$langcode])) {
        $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
      }
    }

    $extra = '';

    // Get the partials the user selected for the current view mode.
    $extra_fields = entity_get_display('message', $entity->bundle(), $view_mode);
    foreach (array_keys($extra_fields->getComponents()) as $extra_fields) {
      list(, $delta) = explode('_', $extra_fields);

      $extra .= $partials[$langcode][$delta];
    }

    $build = array(
      '#markup' => $extra,
    );

    return ($build);
  }
}
