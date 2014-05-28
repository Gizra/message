<?php

/**
 * @file
 * Contains \Drupal\message\Form\MessageTypeDeleteConfirm.
 */

namespace Drupal\Message\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for content type deletion.
 */
class MessageTypeDeleteConfirm extends EntityConfirmFormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new MessageTypeDeleteConfirm object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to delete the content type %type?', array('%type' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return array(
      'route_name' => 'message.overview_types',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $num_Messages = $this->database->query("SELECT COUNT(*) FROM {Message} WHERE type = :type", array(':type' => $this->entity->id()))->fetchField();
    if ($num_Messages) {
      $caption = '<p>' . format_plural($num_Messages, '%type is used by 1 piece of content on your site. You can not remove this content type until you have removed all of the %type content.', '%type is used by @count pieces of content on your site. You may not remove %type until you have removed all of the %type content.', array('%type' => $this->entity->label())) . '</p>';
      $form['#title'] = $this->getQuestion();
      $form['description'] = array('#markup' => $caption);
      return $form;
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, array &$form_state) {
    $this->entity->delete();
    $t_args = array('%name' => $this->entity->label());
    drupal_set_message(t('The content type %name has been deleted.', $t_args));
    watchdog('Message', 'Deleted content type %name.', $t_args, WATCHDOG_NOTICE);

    $form_state['redirect_route']['route_name'] = 'message.overview_types';
  }

}
