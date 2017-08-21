<?php

namespace Drupal\external_reference_field\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ExternalReferenceAutocompleteController.
 *
 * @package Drupal\external_reference\Controller
 */
class ExternalReferenceAutocompleteController extends ControllerBase {

  /**
   * Autocomplete.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Page request.
   * @param string $endpoint_list
   *   Endpoint list.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Return the json objects.
   */
  public function Autocomplete(Request $request, $endpoint_list) {
    // Endpoint list.
    $endpoint_list = base64_decode($endpoint_list);
    $string = $request->query->get('q');
    $json = file_get_contents($endpoint_list . $string);
    $list = json_decode($json);
    $titles = [];

    if (isset($list->suggestions)) {
      $titles = array_column($list->suggestions, 'dc_title');
    }

    return new JsonResponse($titles);
  }

}
