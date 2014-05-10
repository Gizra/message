<?php

/**
 * @file
 * Contains \Drupal\Message\MessageListBuilder.
 */

namespace Drupal\message;

use Drupal\Component\Utility\String;
use Drupal\Core\Datetime\Date;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Language\Language;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a class to build a listing of Message entities.
 *
 * @see \Drupal\Message\Entity\Message
 */
class MessageListBuilder extends EntityListBuilder {

  /**
   * The date service.
   *
   * @var \Drupal\Core\Datetime\Date
   */
  protected $dateService;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    // Enable language column and filter if multiple languages are added.
    $header = array(
      'title' => $this->t('Title'),
      'type' => array(
        'data' => $this->t('Type'),
        'class' => array(RESPONSIVE_PRIORITY_MEDIUM),
      ),
      'author' => array(
        'data' => $this->t('Author'),
        'class' => array(RESPONSIVE_PRIORITY_LOW),
      ),
      'created' => array(
        'data' => $this->t('Created'),
        'class' => array(RESPONSIVE_PRIORITY_LOW),
      ),
    );
    if (\Drupal::languageManager()->isMultilingual()) {
      $header['language_name'] = array(
        'data' => $this->t('Language'),
        'class' => array(RESPONSIVE_PRIORITY_LOW),
      );
    }
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    dpm($entity->getAuthor()->label());
    return array($entity->id());
    /** @var \Drupal\Message\MessageInterface $entity */
    $mark = array(
      '#theme' => 'mark',
      '#mark_type' => Message_mark($entity->id(), $entity->getChangedTime()),
    );
    $langcode = $entity->language()->id;
    $uri = $entity->urlInfo();
    $options = $uri->getOptions();
    $options += ($langcode != Language::LANGCODE_NOT_SPECIFIED && isset($languages[$langcode]) ? array('language' => $languages[$langcode]) : array());
    $uri->setOptions($options);
    $row['title']['data'] = array(
      '#type' => 'link',
      '#title' => $entity->label(),
      '#suffix' => ' ' . drupal_render($mark),
    ) + $uri->toRenderArray();
    $row['type'] = String::checkPlain(Message_get_type_label($entity));
    $row['author']['data'] = array(
      '#theme' => 'username',
      '#account' => $entity->getOwner(),
    );
    $row['status'] = $entity->isPublished() ? $this->t('published') : $this->t('not published');
    $row['changed'] = $this->dateService->format($entity->getChangedTime(), 'short');
    $language_manager = \Drupal::languageManager();
    if ($language_manager->isMultilingual()) {
      $row['language_name'] = $language_manager->getLanguageName($langcode);
    }
    $row['operations']['data'] = $this->buildOperations($entity);
    return $row + parent::buildRow($entity);
  }
}
