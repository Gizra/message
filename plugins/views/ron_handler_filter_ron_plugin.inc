<?php
// $Id: flag_handler_filter_flagged.inc,v 1.1.2.5.2.3 2010/01/08 05:03:16 quicksketch Exp $

/**
 * @file
 * Contains the flagged content filter handler.
 */

/**
 * Handler to filter by role realm.
 *
 * @ingroup views
 */
class ron_handler_filter_ron_plugin extends views_handler_filter_boolean_operator {

  function option_definition() {
    $options = parent::option_definition();
    $options['value'] = array('default' => TRUE);
    return $options;
  }

  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    $form['value']['#type'] = 'radios';
    $form['value']['#title'] = t('Role realm');
    $form['value']['#options'] = array(TRUE => t('In role realm'), FALSE => t('Not in role realm'));
    if ($this->options['expose']['optional']) {
      $form['value']['#options']['All'] = t('All');
    }
    $form['value']['#default_value'] = empty($this->options['value']);
  }

  function query() {
    $operator = $this->value ? 'IS NOT' : 'IS';
    $this->query->add_where($this->options['group'], $this->relationship .'.uid '. $operator .' NULL');
  }
}
