<?php

/**
 * @file
 * Definition of Drupal\message\MessageViewBuilder.
 */

namespace Drupal\message;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\entity\Entity\EntityViewDisplay;

/**
 * Render controller for Messages.
 */
class MessageViewBuilder extends EntityViewBuilder {

  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $build = parent::view($entity, $view_mode, $langcode);

    // Load the partials in the correct language.
    $partials = $entity->getType()->getText(NULL, array('text' => TRUE));


    // Get the partials the user selected for the current view mode.
    $extra_fields = entity_get_display('message', $entity->bundle(), $view_mode);
    foreach (array_keys($extra_fields->getComponents()) as $partials) {
      list(, $delta) = explode('_', $partials);
    }

    // todo: Add this to the rendered array.
    return render($build);
  }
}
