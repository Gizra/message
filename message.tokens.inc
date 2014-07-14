<?php

/**
 * @file
 * Builds placeholder replacement tokens for message-related data.
 */

use Drupal\Component\Utility\String;
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
function message_tokens($type, $tokens, array $data = array(), array $options = array()) {
  $token_service = \Drupal::token();

  $sanitize = !empty($options['sanitize']);

  $replacements = array();

  if ($type == 'message' && !empty($data['message'])) {
    /** @var \Drupal\message\Entity\Message */
    $message = $data['message'];

    foreach ($tokens as $name => $original) {
      switch ($name) {
        // Simple key values on the message.
        case 'mid':
          $replacements[$original] = $message->id();
          break;

        case 'type':
          $replacements[$original] = $sanitize ? String::checkPlain($message->getType()->id()) : $message->getType()->id();
          break;

        case 'type-name':
          $type_name = $message->getType()->label();
          $replacements[$original] = $sanitize ? String::checkPlain($type_name) : $type_name;
          break;

        case 'uuid':
          $replacements[$original] = $message->getUUID();
          break;

        // Default values for the chained tokens handled below.
        case 'author':
          $account = $message->getAuthor() ? $message->getAuthor() : User::load(0);
          $replacements[$original] = $sanitize ? String::checkPlain($account->label()) : $account->label();
          break;

        case 'created':
          $replacements[$original] = format_date($message->getCreatedTime(), 'medium', '', NULL);
          break;
      }
    }

    if ($author_tokens = $token_service->findWithPrefix($tokens, 'author')) {
      $replacements += $token_service->generate('user', $author_tokens, array('user' => $message->getAuthor()), $options);
    }

    if ($created_tokens = $token_service->findWithPrefix($tokens, 'created')) {
      $replacements += $token_service->generate('date', $created_tokens, array('date' => $message->getCreatedTime()), $options);
    }

    if ($changed_tokens = $token_service->findWithPrefix($tokens, 'changed')) {
      $replacements += $token_service->generate('date', $changed_tokens, array('date' => $message->getChangedTime()), $options);
    }
  }

  return $replacements;
}
