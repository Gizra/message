<?php
// $Id: context_condition.inc,v 1.1.2.2 2010/02/09 04:38:01 yhahn Exp $

/**
 * Base class for a ron plugin.
 */
class context_condition {
  var $plugin;
  var $title;
  var $description;
  var $values = array();

  /**
   * Constructor. Do not override.
   */
  function __construct($plugin, $info) {
    $this->plugin = $plugin;
    $this->title = isset($info['title']) ? $info['title'] : $plugin;
    $this->description = isset($info['description']) ? $info['description'] : '';
  }

  /**
   * Ron values.
   */
  function ron_values() {
    return array();
  }

  /**
   * Ron form.
   */
  function ron_form($context) {
    return array(
      '#title' => $this->title,
      '#description' => $this->description,
      '#options' => $this->condition_values(),
      '#type' => 'checkboxes',
      '#default_value' => $this->fetch_from_context($context, 'values'),
    );
  }

  /**
   * Condition form submit handler.
   */
  function ron_form_submit($values) {
    // Editor forms are generally checkboxes -- do some checkbox processing.
    return drupal_map_assoc(array_keys(array_filter($values)));
  }

  /**
   * Options form. Provide additional options for your condition.
   */
  function options_form($context) {
    return array();
  }

  /**
   * Options form submit handler.
   */
  function options_form_submit($values) {
    return $values;
  }


  /**
   * Public method that is called from hooks or other integration
   * points with Drupal.
   */
  function execute($value) {
    foreach ($this->get_contexts($value) as $context) {
      $this->condition_met($context, $value);
    }
  }
}
