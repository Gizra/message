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

    // todo: Get the partial that been set in the current view mode.
    return render($build);
  }
}
