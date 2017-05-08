<?php

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

    // Ensure that wanted language exists for given message. If no langcode
    // given, check if users current language exists for message.
    $current_language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    /* @var \Drupal\message\Entity\Message $entity  */
    if ($langcode && $entity->getText($langcode)) {
      $entity->setLanguage($langcode);
    }
    elseif ($entity->getText($current_language)) {
      $entity->setLanguage($current_language);
    }

    // Load the partials in the correct language.
    $partials = $entity->getText();

    // Get the partials the user selected for the current view mode.
    $extra_fields = entity_get_display('message', $entity->bundle(), $view_mode);
    foreach ($extra_fields->getComponents() as $field_name => $settings) {
      // The partials are keyed with `partial_X`, check if that is set.
      if (strpos($field_name, 'partial_') === 0) {
        list(, $delta) = explode('_', $field_name);
        if (isset($partials[$delta])) {
          $build[$field_name]['#markup'] = $partials[$delta];
        }
      }
      else {
        // This is another field.
        $display = $this->getSingleFieldDisplay($entity, $field_name, $settings);
        $build += $display->build($entity);
      }
    }

    return $build;
  }

}
