<?php

namespace AgileStoreLocator\Admin;

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Free geocoding utilities retained from the Import/Export controller.
 */
class ImportExport extends Base {

  public function __construct() {
    parent::__construct();
  }

  /**
   * Validate the saved Google server API key.
   */
  public function validate_api_key() {

    global $wpdb;

    $response          = new \stdclass();
    $response->success = false;
    $configs_result    = $wpdb->get_results("SELECT `key`,`value` FROM " . ASL_PREFIX . "configs WHERE `key` = 'server_key'");

    if (isset($configs_result[0])) {
      $api_key = $configs_result[0]->value;

      if ($api_key) {
        $results          = \AgileStoreLocator\Helper::getLnt('1848 Provincial Road, N8W 5W3 Winsdor ON Canada', $api_key, true);
        $response->result = $results;

        if ($results && isset($results['body'])) {
          $decoded = json_decode($results['body'], true);

          if (isset($decoded['error_message'])) {
            $response->msg = $decoded['error_message'];
          } else {
            $response->msg     = esc_attr__('Valid API Key', 'asl_locator');
            $response->success = true;
          }
        }
      } else {
        $response->msg = esc_attr__('Server Google API Key is Missing', 'asl_locator');
      }
    } else {
      $response->msg = esc_attr__('Server Google API Key is not saved.', 'asl_locator');
    }

    return $this->send_response($response);
  }

  /**
   * Fetch coordinates for stores that do not have valid coordinates.
   */
  public function fill_missing_coords() {

    ini_set('memory_limit', '256M');
    ini_set('max_execution_time', 0);

    global $wpdb;

    $response          = new \stdclass();
    $response->success = false;
    $response->summary = array();
    $configs_result    = $wpdb->get_results("SELECT `key`,`value` FROM " . ASL_PREFIX . "configs WHERE `key` = 'server_key'");
    $api_key           = isset($configs_result[0]) ? $configs_result[0]->value : '';

    if ($api_key) {
      $stores = $wpdb->get_results("SELECT * FROM " . ASL_PREFIX . "stores WHERE (lat = '' OR lng = '') OR (lat = '0.0' OR lng = '0.0') OR (lat IS NULL OR lng IS NULL) OR !(lat BETWEEN -90.10 AND 90.10) OR !(lng BETWEEN -180.10 AND 180.10) OR !(lat REGEXP '^[+-]?[0-9]*([0-9]\\.|[0-9]|\\.[0-9])[0-9]*(e[+-]?[0-9]+)?$') OR !(lng REGEXP '^[+-]?[0-9]*([0-9]\\.|[0-9]|\\.[0-9])[0-9]*(e[+-]?[0-9]+)?$')");

      foreach ($stores as $store) {
        $coordinates = \AgileStoreLocator\Helper::getCoordinates($store->street, $store->city, $store->state, $store->postal_code, $store->country, $api_key);
        $address     = implode(', ', array($store->street, $store->city, $store->state, $store->postal_code));

        if ($coordinates && $wpdb->update(ASL_PREFIX . 'stores', array('lat' => $coordinates['lat'], 'lng' => $coordinates['lng']), array('id' => $store->id))) {
          $response->summary[] = 'Store ID: ' . $store->id . ', LAT/LNG Fetch Success, Address: ' . $address;
        } else {
          $response->summary[] = '<span class="red">Store ID: ' . $store->id . ', LAT/LNG Fetch Failed, Address: ' . $address . '</span>';
        }
      }

      if (!$stores) {
        $response->summary[] = esc_attr__('Missing Coordinates are not Found in Store Listing', 'asl_locator');
      }

      $response->msg     = esc_attr__('Missing Coordinates Request Completed', 'asl_locator');
      $response->success = true;
    } else {
      $response->msg = esc_attr__('Google Server API Key is Missing.', 'asl_locator');
    }

    return $this->send_response($response);
  }
}
