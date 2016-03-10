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

    if (!$langcode) {
      $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
    }
    else {
      if (\Drupal::moduleHandler()->moduleExists('config_translation') && !isset($partials[$langcode])) {
        $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
      }
    }

    // Load the partials in the correct language.
    /* @var $entity \Drupal\message\Entity\Message */
    if ($langcode) {
      $entity->setLanguage($langcode);
    }
    $partials = $entity->getText();

    $extra = '';

    // Get the partials the user selected for the current view mode.
    $extra_fields = entity_get_display('message', $entity->bundle(), $view_mode);
    foreach (array_keys($extra_fields->getComponents()) as $extra_fields) {
      list(, $delta) = explode('_', $extra_fields);

      $extra .= $partials[$delta];
    }

    $build['#markup'] = $extra;

    return ($build);
  }
}
