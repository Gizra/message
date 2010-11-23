<?php

/**
 * Implementation of hook_views_default_views().
 */
function message_views_default_views() {
  /*
   * Message view.
   */
  $view = new view;
  $view->name = 'message';
  $view->description = 'Show recent messages filtered by the user and role realms';
  $view->tag = '';
  $view->base_table = 'message';
  $view->api_version = '3.0-alpha1';
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

  /* Display: Defaults */
  $handler = $view->new_display('default', 'Defaults', 'default');
  $handler->display->display_options['title'] = 'Recent activity';
  $handler->display->display_options['access']['type'] = 'none';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['pager']['type'] = 'full';
  $handler->display->display_options['style_plugin'] = 'default';
  $handler->display->display_options['row_plugin'] = 'fields';
  $handler->display->display_options['row_options']['inline'] = array(
    'name_1' => 'name_1',
    'render' => 'render',
  );
  $handler->display->display_options['row_options']['hide_empty'] = 0;
  /* Relationship: Message: User */
  $handler->display->display_options['relationships']['uid']['id'] = 'uid';
  $handler->display->display_options['relationships']['uid']['table'] = 'message';
  $handler->display->display_options['relationships']['uid']['field'] = 'uid';
  $handler->display->display_options['relationships']['uid']['required'] = 0;
  /* Field: User: Name */
  $handler->display->display_options['fields']['name_1']['id'] = 'name_1';
  $handler->display->display_options['fields']['name_1']['table'] = 'users';
  $handler->display->display_options['fields']['name_1']['field'] = 'name';
  $handler->display->display_options['fields']['name_1']['relationship'] = 'uid';
  $handler->display->display_options['fields']['name_1']['label'] = '';
  $handler->display->display_options['fields']['name_1']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['name_1']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['name_1']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['name_1']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['name_1']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['name_1']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['name_1']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['name_1']['alter']['html'] = 0;
  $handler->display->display_options['fields']['name_1']['hide_empty'] = 0;
  $handler->display->display_options['fields']['name_1']['empty_zero'] = 0;
  $handler->display->display_options['fields']['name_1']['link_to_user'] = 1;
  $handler->display->display_options['fields']['name_1']['overwrite_anonymous'] = 0;
  /* Field: Message: Show message */
  $handler->display->display_options['fields']['render']['id'] = 'render';
  $handler->display->display_options['fields']['render']['table'] = 'message';
  $handler->display->display_options['fields']['render']['field'] = 'render';
  $handler->display->display_options['fields']['render']['label'] = '';
  $handler->display->display_options['fields']['render']['alter']['alter_text'] = 0;
  $handler->display->display_options['fields']['render']['alter']['make_link'] = 0;
  $handler->display->display_options['fields']['render']['alter']['absolute'] = 0;
  $handler->display->display_options['fields']['render']['alter']['trim'] = 0;
  $handler->display->display_options['fields']['render']['alter']['word_boundary'] = 1;
  $handler->display->display_options['fields']['render']['alter']['ellipsis'] = 1;
  $handler->display->display_options['fields']['render']['alter']['strip_tags'] = 0;
  $handler->display->display_options['fields']['render']['alter']['html'] = 0;
  $handler->display->display_options['fields']['render']['hide_empty'] = 0;
  $handler->display->display_options['fields']['render']['empty_zero'] = 0;
  $handler->display->display_options['fields']['render']['skip_access'] = 0;
  /* Sort criterion: Message: Creation date */
  $handler->display->display_options['sorts']['timestamp']['id'] = 'timestamp';
  $handler->display->display_options['sorts']['timestamp']['table'] = 'message';
  $handler->display->display_options['sorts']['timestamp']['field'] = 'timestamp';
  $handler->display->display_options['sorts']['timestamp']['order'] = 'DESC';
  /* Filter: Message: Message realm access */
  $handler->display->display_options['filters']['message_realm_user_access']['id'] = 'message_realm_user_access';
  $handler->display->display_options['filters']['message_realm_user_access']['table'] = 'field_data_message_realm';
  $handler->display->display_options['filters']['message_realm_user_access']['field'] = 'message_realm_user_access';
  $handler->display->display_options['filters']['message_realm_user_access']['value'] = array(
    'role' => 'role',
    'user' => 'user',
  );

  /* Display: Page */
  $handler = $view->new_display('page', 'Page', 'page_1');
  $handler->display->display_options['path'] = 'message';
  $handler->display->display_options['menu']['type'] = 'normal';
  $handler->display->display_options['menu']['title'] = 'Recent messages';
  $handler->display->display_options['menu']['weight'] = '0';

  $views[$view->name] = $view;

  return $views;
}
