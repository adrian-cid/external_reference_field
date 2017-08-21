<?php

namespace Drupal\external_reference_field\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'external_reference' field type.
 *
 * @FieldType(
 *   id = "external_reference",
 *   label = @Translation("External reference"),
 *   description = @Translation("External Reference field"),
 *   category = @Translation("Reference"),
 *   default_widget = "auto_complete_widget",
 *   default_formatter = "title_formatter"
 * )
 */
class ExternalReferenceItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
      'is_ascii' => FALSE,
      'case_sensitive' => FALSE,
      'endpoint_list_en' => '',
      'endpoint_individual_en' => '',
      'endpoint_list_fr' => '',
      'endpoint_individual_fr' => '',
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Text value'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $columns = [
      'value' => [
        'description' => 'The ID of the target entity in the Lieux et Batiment system.',
        'type' => $field_definition->getSetting('is_ascii') === TRUE ? 'varchar_ascii' : 'varchar',
        'length' => 255,
        'binary' => $field_definition->getSetting('case_sensitive'),
      ],
    ];

    $schema = [
      'columns' => $columns,
      'indexes' => [
        'value' => ['value'],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $random = new Random();
    $values['value'] = $random->word(mt_rand(1, 255));
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = [];

    $element['endpoint_list_en'] = [
      '#type' => 'textfield',
      '#title' => t('English Endpoint List'),
      '#default_value' => $this->getSetting('endpoint_list_en'),
      '#required' => TRUE,
      '#description' => t('Enter here the endpoint that have a list of content. Example: <b>https://your_endpoint.com/list/q=</b>'),
      '#size' => 150,
      '#maxlength' => 200,
    ];

    $element['endpoint_individual_en'] = [
      '#type' => 'textfield',
      '#title' => t('English Endpoint Individual'),
      '#default_value' => $this->getSetting('endpoint_individual_en'),
      '#required' => TRUE,
      '#description' => t('Enter here the endpoint that have the information of a single content. If you access the content with id = 1 with this endpoint https://your_endpoint.com/item/1 you need to write here: <b>https://your_endpoint.com/item/</b>'),
      '#size' => 150,
      '#maxlength' => 200,
    ];

    $element['endpoint_list_fr'] = [
      '#type' => 'textfield',
      '#title' => t('French Endpoint List'),
      '#default_value' => $this->getSetting('endpoint_list_fr'),
      '#required' => TRUE,
      '#description' => t('Enter here the endpoint that have a list of content. Example: <b>https://your_endpoint.com/list/q=</b>'),
      '#size' => 150,
      '#maxlength' => 200,
    ];

    $element['endpoint_individual_fr'] = [
      '#type' => 'textfield',
      '#title' => t('French Endpoint Individual'),
      '#default_value' => $this->getSetting('endpoint_individual_fr'),
      '#required' => TRUE,
      '#description' => t('Enter here the endpoint that have the information of a single content. If you access the content with id = 1 with this endpoint https://your_endpoint.com/item/1 you need to write here: <b>https://your_endpoint.com/item/</b>'),
      '#size' => 150,
      '#maxlength' => 200,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

}
