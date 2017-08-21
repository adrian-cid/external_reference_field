<?php

namespace Drupal\external_reference_field\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Textfield;

/**
 * Provides an entity autocomplete form element.
 *
 * The #default_value accepted by this element is either an entity object or an
 * array of entity objects.
 *
 * @FormElement("external_reference_autocomplete")
 */
class ExternalReferenceAutocomplete extends Textfield {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $info = parent::getInfo();
    $class = get_class($this);

    // Apply default form element properties.
    // This should only be set to FALSE if proper validation by the selection
    // handler is performed at another level on the extracted form values.
    $info['#validate_reference'] = TRUE;
    // IMPORTANT! This should only be set to FALSE if the #default_value
    // property is processed at another level (e.g. by a Field API widget) and
    // it's value is properly checked for access.
    $info['#process_default_value'] = TRUE;

    $info['#element_validate'] = [[$class, 'validateExternalReferenceAutocomplete']];
    array_unshift($info['#process'], [$class, 'processExternalReferenceAutocomplete']);

    return $info;
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    if ($input === FALSE && !empty($element['#endpoint_individual'])) {
      $json = file_get_contents($element['#endpoint_individual'] . $element['#default_value']);
      $element_json = json_decode($json);
      $title = $element_json->dc_title;

      return $title;
    }
  }

  /**
   * Adds entity autocomplete functionality to a form element.
   *
   * @param array $element
   *   The form element to process.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   *
   * @return array
   *   The form element.
   */
  public static function processExternalReferenceAutocomplete(array &$element, FormStateInterface $form_state, array &$complete_form) {
    // Nothing to do if there is no target entity type.
    if (!empty($element['#endpoint_list'])) {

      $element['#autocomplete_route_name'] = 'external_reference_field.autocomplete';
      $element['#autocomplete_route_parameters'] = [
        'endpoint_list' => base64_encode($element['#endpoint_list']),
      ];

      return $element;
    }
  }

  /**
   * Form element validation handler for entity_autocomplete elements.
   */
  public static function validateExternalReferenceAutocomplete(array &$element, FormStateInterface $form_state, array &$complete_form) {
    // Searching the id.
    $json = file_get_contents($element['#endpoint_list'] . rawurlencode($element['#value']));
    $list = json_decode($json);
    $external_id = $list->suggestions[0]->dc_identifier;
    $form_state->setValueForElement($element, $external_id);
  }

}
