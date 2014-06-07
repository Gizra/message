<?php

/**
 * @file
 * Contains \Drupal\message\Form\MessageTypeConfigTranslationBaseForm.
 */

namespace Drupal\message\Form;

use Drupal\config_translation\Form\ConfigTranslationFormBase;
use Drupal\Core\Config\Schema\Element;

/**
 * Defines a form for adding configuration translations.
 */
abstract class MessageTypeConfigTranslationBaseForm extends ConfigTranslationFormBase {

  /**
   * Formats configuration schema as a form tree.
   *
   * @param \Drupal\Core\Config\Schema\Element $schema
   *   Schema definition of configuration.
   * @param array|string $config_data
   *   Configuration object of requested language, a string when done traversing
   *   the data building each sub-structure for the form.
   * @param array|string $base_config_data
   *   Configuration object of base language, a string when done traversing
   *   the data building each sub-structure for the form.
   * @param bool $open
   *   (optional) Whether or not the details element of the form should be open.
   *   Defaults to TRUE.
   * @param string|null $base_key
   *   (optional) Base configuration key. Defaults to an empty string.
   *
   * @return array
   *   An associative array containing the structure of the form.
   */
  protected function buildConfigForm(Element $schema, $config_data, $base_config_data, $open = TRUE, $base_key = '') {
    $build = array();
    foreach ($schema as $key => $element) {
      // Make the specific element key, "$base_key.$key".
      $element_key = implode('.', array_filter(array($base_key, $key)));
      $definition = $element->getDataDefinition() + array('label' => $this->t('N/A'));
      if ($element instanceof Element) {
        // Build sub-structure and include it with a wrapper in the form
        // if there are any translatable elements there.
        $sub_build = $this->buildConfigForm($element, $config_data[$key], $base_config_data[$key], FALSE, $element_key);
        if (!empty($sub_build)) {
          // For some configuration elements the same element structure can
          // repeat multiple times, (like views displays, filters, etc.).
          // So try to find a more usable title for the details summary. First
          // check if there is an element which is called title or label, then
          // check if there is an element which contains these words.
          $title = '';
          if (isset($sub_build['title']['source'])) {
            $title = $sub_build['title']['source']['#markup'];
          }
          elseif (isset($sub_build['label']['source'])) {
            $title = $sub_build['label']['source']['#markup'];
          }
          else {
            foreach (array_keys($sub_build) as $title_key) {
              if (isset($sub_build[$title_key]['source']) && (strpos($title_key, 'title') !== FALSE || strpos($title_key, 'label') !== FALSE)) {
                $title = $sub_build[$title_key]['source']['#markup'];
                break;
              }
            }
          }
          $build[$key] = array(
              '#type' => 'details',
              '#title' => (!empty($title) ? (strip_tags($title) . ' ') : '') . $this->t($definition['label']),
              '#open' => $open,
            ) + $sub_build;
        }
      }
      else {
        $definition = $element->getDataDefinition();

        // Invoke hook_config_translation_type_info_alter() implementations to
        // alter the configuration types.
        $definitions = array(
          $definition['type'] => &$definition,
        );
        $this->moduleHandler->alter('config_translation_type_info', $definitions);

        // Create form element only for translatable items.
        if (!isset($definition['translatable']) || !isset($definition['type'])) {
          continue;
        }

        $value = $config_data[$key];
        $build[$element_key] = array(
          '#theme' => 'config_translation_manage_form_element',
        );

        if (is_array($base_config_data[$key])) {
          $build[$element_key]['source'] = array(
            '#markup' => $base_config_data[$key] ? ('<span lang="' . $this->sourceLanguage->id . '">' . nl2br($this->t('Multiple field') . '</span>')) : t('(Empty)'),
            '#title' => $this->t(
                '!label <span class="visually-hidden">(!source_language)</span>',
                array(
                  '!label' => $this->t($definition['label']),
                  '!source_language' => $this->sourceLanguage->name,
                )
              ),
            '#type' => 'item',
          );

          $build[$element_key]['translation'] = array();

        }
        else {
          $build[$element_key]['source'] = array(
            '#markup' => $base_config_data[$key] ? ('<span lang="' . $this->sourceLanguage->id . '">' . nl2br($base_config_data[$key] . '</span>')) : t('(Empty)'),
            '#title' => $this->t(
                '!label <span class="visually-hidden">(!source_language)</span>',
                array(
                  '!label' => $this->t($definition['label']),
                  '!source_language' => $this->sourceLanguage->name,
                )
              ),
            '#type' => 'item',
          );

          $definition += array('form_element_class' => '\Drupal\config_translation\FormElement\Textfield');

          /** @var \Drupal\config_translation\FormElement\ElementInterface $form_element */
          $form_element = new $definition['form_element_class']();
          $build[$element_key]['translation'] = $form_element->getFormElement($definition, $this->language, $value);

        }
      }
    }
    return $build;
  }
}
