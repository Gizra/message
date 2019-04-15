<?php

namespace Drupal\message\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *   id = "d7_message_template_text"
 * )
 */
class MessageTemplateProcessText extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   *
   * Transform the message template texts.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $source = $row->getSource();
    $message_tpl = \Drupal::entityTypeManager()->getStorage('message_template')->load($row->getSource()['name']);

    $texts = [];
    if (!empty($message_tpl)) {
      $texts = $message_tpl->getRawText();
    }

    $texts[] = [
      'value' => $source['message_text_value'],
      'format' => $source['message_text_format'],
    ];

    return $texts;
  }

}
