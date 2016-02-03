<?php

/**
 * @file
 * Builds placeholder replacement tokens for message-related data.
 */

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\user\Entity\User;

/**
 * Implements hook_token_info().
 */
function message_token_info() {
  $type = array(
    'name' => t('messages'),
    'description' => t('Tokens related to individual content items, or "messages".'),
    'needs-data' => 'message',
  );

  // Core tokens for messages.
  $message['mid'] = array(
    'name' => t("Message ID"),
    'description' => t('The unique ID of the message.'),
  );
  $message['type'] = array(
    'name' => t("Message type"),
  );
  $message['type-name'] = array(
    'name' => t("message type name"),
    'description' => t("The human-readable name of the message type."),
  );
  $message['uuid'] = array(
    'name' => t("Message UID"),
    'description' => t("The message UUID."),
  );

  $message['created'] = array(
    'name' => t("Date created"),
    'type' => 'date',
  );
  $message['author'] = array(
    'name' => t("Author"),
    'type' => 'user',
  );

  return array(
    'types' => array('message' => $type),
    'tokens' => array('message' => $message),
  );
}

/**
 * Implements hook_tokens().
 */
function message_tokens($type, $tokens, array $data = array(), array $options = array(), BubbleableMetadata $bubbleable_metadata) {
  $token_service = \Drupal::token();

  $replacements = array();

  if ($type == 'message' && !empty($data['message'])) {
    /** @var \Drupal\message\Entity\Message $message */
    $message = $data['message'];

    foreach ($tokens as $name => $original) {
      switch ($name) {
        // Simple key values on the message.
        case 'mid':
          $replacements[$original] = $message->id();
          break;

        case 'type':
          $replacements[$original] = $message->getType()->id();
          break;

        case 'type-name':
          $replacements[$original] = $message->getType()->label();
          break;

        case 'uuid':
          $replacements[$original] = $message->getUUID();
          break;

        // Default values for the chained tokens handled below.
        case 'author':
          $account = $message->getAuthor() ? $message->getAuthor() : User::load(0);
          $replacements[$original] = $account->label();
          break;

        case 'created':
          $replacements[$original] = format_date($message->getCreatedTime(), 'medium', '', NULL);
          break;
      }
    }

    if ($author_tokens = $token_service->findWithPrefix($tokens, 'author')) {
      $replacements += $token_service->generate('user', $author_tokens, array('user' => $message->getAuthor()), $options, $bubbleable_metadata);
    }

    if ($created_tokens = $token_service->findWithPrefix($tokens, 'created')) {
      $replacements += $token_service->generate('date', $created_tokens, array('date' => $message->getCreatedTime()), $options, $bubbleable_metadata);
    }

    if ($changed_tokens = $token_service->findWithPrefix($tokens, 'changed')) {
      $replacements += $token_service->generate('date', $changed_tokens, array('date' => $message->getChangedTime()), $options, $bubbleable_metadata);
    }
  }

  return $replacements;
}
