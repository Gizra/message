<?php
// $Id: context_condition_node.inc,v 1.1.2.2 2010/02/09 04:38:01 yhahn Exp $

/**
 * Expose node views/node forms of specific node types as a context condition.
 */
class message_plugin_og extends message_plugin {

  public function access() {
    // Check if the user has access to the realm by their ID.
    return array_intersect(array_keys($this->account->og_groups), $this->messages[$this->realm]);
  }

  public function __get_views_handlers() {
    return array(
      'message_handler_filter_plugin_og_realm' => array(
        'parent' => 'views_handler_filter',
        'path' => drupal_get_path('module', 'message') .'/plugins/views',
      ),
    );
  }

  public function get_views_data() {
    $data = array();

    $data['message_realm']['plugin_og_realm'] = array(
      'title' => t('Organic groups realm'),
      'help' => t('Filter messages by membership in groups realm.'),

      'real field' => 'realm_id',

      'relationship' => array(
        'group' => t('Message realm'),
        'title' => t('Organic groups'),
        'help' => t('Join the Organic groups table to the Message table'),
        'handler' => 'views_handler_relationship',
        'base' => 'og_uid',
        'base field' => 'uid',
        'label' => t('Message realm organic groups'),
      ),

      'filter' => array(
        'handler' => 'views_handler_filter_user_current',
        'label' => t('Organic groups'),
      ),
    );

    return $data;
  }

}
