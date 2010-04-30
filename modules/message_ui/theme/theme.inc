<?php

/**
 * Preprocessor for theme('message_ui_form').
 */
function template_preprocess_message_ui_form(&$vars) {
  drupal_add_css(drupal_get_path("module", "message_ui") ."/message_ui.css");
  $vars['buttons'] = $vars['form']['buttons'];
  unset($vars['form']['buttons']);
}

/**
 * Generates the main message_ui admin page with a tiered message listing.
 */
function theme_message_ui_admin($form) {
  drupal_add_css(drupal_get_path("module", "message_ui") ."/message_ui.css");

  // Iterate once to group by tag.
  $by_tag = array();
  foreach ($form['#messages'] as $message) {
    if (!empty($message->tag)) {
      $by_tag[$message->tag][$message->name] = $message;
    }
    else {
      $by_tag[''][$message->name] = $message;
    }
  }

  // Generate listing of existing messages
  ksort($by_tag);
  $rows = array();
  foreach ($by_tag as $tag => $messages) {
    if (!empty($tag)) {
      $rows[] = array(array('data' => check_plain($tag), 'colspan' => 3, 'class' => 'tag'));
    }
    ksort($messages);
    foreach ($messages as $message) {
      $row = array('data' => array());

      // message info
      $storage = t('Default');
      if ($message->export_type & EXPORT_IN_DATABASE) {
        $storage = $message->export_type & EXPORT_IN_CODE ? t('Overridden') : t('Normal');
      }
      $data = "<strong>{$message->name}</strong> <em class='storage'>({$storage})</em>";
      $data .= !empty($message->description) ? '<div class="description">'. filter_xss_admin($message->description) .'</div>' : '';
      $row['data'][] = array('data' => $data, 'colspan' => 2, 'class' => 'message-name');

      // message actions
      $links = array(
        'edit'    => l(t('Edit'), "admin/build/message/list/{$message->name}"),
        'delete'  => l(t('Delete'), "admin/build/message/list/{$message->name}/delete"),
        'revert'  => l(t('Revert'), "admin/build/message/list/{$message->name}/revert"),
        'export'  => l(t('Export'), "admin/build/message/list/{$message->name}/export"),
        'clone'   => l(t('Clone'), "admin/build/message/list/{$message->name}/clone"),
        'disable' => l(t('Disable'), "admin/build/message/list/{$message->name}/disable"),
        'enable'  => l(t('Enable'), "admin/build/message/list/{$message->name}/enable"),
      );
      foreach (array_keys($links) as $key) {
        if (!message_ui_task_access($message, $key)) {
          unset($links[$key]);
        }
      }
      $row['data'][] = array('data' => implode(' | ', $links), 'class' => 'message-links');
      $row['class'] = empty($message->disabled) ? 'enabled' : 'disabled';
      $rows[] = $row;
    }
  }
  $rows[] = array(
    drupal_render($form['tag']),
    drupal_render($form['name']),
    drupal_render($form['submit']),
  );
  $output = theme('table', array(t('message'), '', t('Operations')), $rows, array('class' => 'message-admin'));
  $output .= drupal_render($form);
  return $output;
}
