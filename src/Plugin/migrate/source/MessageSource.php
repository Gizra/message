<?php

/**
 * @file
 * Contains \Drupal\message\Plugin\migrate\source.
 */

namespace Drupal\message\Plugin\migrate\source;

use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;
use Drupal\migrate\Row;
use Drupal\message\Entity\Message;
/**
 * Drupal 7 message source from database.
 *
 * @MigrateSource(
 *   id = "d7_message_source",
 *   source_module = "message"
 * )
 */
class MessageSource extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('message', 'm');

    $query->fields('m', [
      'mid',
      'type',
      'arguments',
      'uid',
      'timestamp',
      'language',
    ]);

    $query->orderBy('timestamp');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'mid' => $this->t('Primary key: unique message ID.'),
      'type' => $this->t('Message type.'),
      'arguments' => $this->t('Message replace arguments.'),
      'uid' => $this->t('Message uid of author.'),
      'timestamp' => $this->t('Message create time.'),
      'language' => $this->t('Message language.'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['mid']['type'] = 'integer';

    return $ids;
  }

}
