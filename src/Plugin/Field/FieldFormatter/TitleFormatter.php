<?php

namespace Drupal\external_reference_field\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'title_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "title_formatter",
 *   label = @Translation("Title formatter"),
 *   field_types = {
 *     "external_reference"
 *   }
 * )
 */
class TitleFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      // Implement settings form.
    ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    // Getting the language code.
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    foreach ($items as $delta => $item) {
      $endpoint_individual = $items->getFieldDefinition()->getItemDefinition()->getSetting('endpoint_individual_' . $language);

      $json = file_get_contents($endpoint_individual . $item->value);
      $element_json = json_decode($json);
      $title = $element_json->dc_title;

      $elements[$delta] = ['#markup' => $title];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->value));
  }

}
