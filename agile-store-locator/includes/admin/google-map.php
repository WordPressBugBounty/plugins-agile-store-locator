<?php

namespace AgileStoreLocator\Admin;


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

use AgileStoreLocator\Admin\Base;

/**
 * The map manager functionality of the admin
 *
 * @link       https://agilestorelocator.com
 * @since      4.7.32
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/Admin/GoogleMap
 */

class GoogleMap extends Base {


  /**
   * [__construct description]
   */
  public function __construct() {
    
    parent::__construct();
  }

  
  /**
   * [save_custom_map save customize map]
   * @return [type] [description]
   */
  public function save_custom_map() {

    $response  = new \stdclass();
    $response->success = false;


    if (isset($_POST['data_map'])) {
      $data_map = json_decode(wp_unslash($_POST['data_map']), true);

      if (!is_array($data_map)) {
        $response->error = esc_attr__('Invalid map customization data.', 'asl_locator');
        return $this->send_response($response);
      }

      $data_map = $this->sanitize_map_customization($data_map);
      $updated = \AgileStoreLocator\Helper::set_setting(
        wp_json_encode($data_map),
        'map',
        'map_customize'
      );

      if ($updated === false) {
        $response->error = esc_attr__('Error occurred while saving the map.', 'asl_locator');
      } else {
        $response->msg     = esc_attr__('Map has been updated successfully.', 'asl_locator');
        $response->success = true;
      }
    } else {
      $response->error = esc_attr__('Map customization data is missing.', 'asl_locator');
    }

        
    return $this->send_response($response);  
  }

  /**
   * Validate and normalize the map customization payload.
   */
  private function sanitize_map_customization($data_map) {
    $customization = [];

    foreach (['trafic_layer', 'transit_layer', 'bike_layer', 'marker_animations'] as $option_key) {
      $customization[$option_key] = empty($data_map[$option_key]) ? 0 : 1;
    }

    $customization['map_controls'] = [];
    foreach (['cameracontrol', 'zoomcontrol', 'streetviewcontrol', 'fullscreencontrol', 'maptypecontrol'] as $control_key) {
      $customization['map_controls'][$control_key] = empty($data_map['map_controls'][$control_key]) ? 0 : 1;
    }
    if ($customization['map_controls']['cameracontrol']) {
      $customization['map_controls']['zoomcontrol'] = 0;
    }

    $drawing = isset($data_map['drawing']) && is_array($data_map['drawing']) ? $data_map['drawing'] : [];
    $center  = isset($drawing['center']) ? $this->sanitize_coordinate_pair($drawing['center']) : null;
    $zoom    = isset($drawing['zoom']) && is_numeric($drawing['zoom']) ? (int) $drawing['zoom'] : 5;

    $customization['drawing'] = [
      'zoom'    => max(1, min(22, $zoom)),
      'center'  => $center ? $center : [0, 0],
      'shapes'  => [],
      'markers' => [],
    ];

    $shapes = isset($drawing['shapes']) && is_array($drawing['shapes']) ? array_slice($drawing['shapes'], 0, 500) : [];
    foreach ($shapes as $shape) {
      $sanitized_shape = $this->sanitize_map_shape($shape);
      if ($sanitized_shape) {
        $customization['drawing']['shapes'][] = $sanitized_shape;
      }
    }

    return $customization;
  }

  /**
   * Validate a saved map shape.
   */
  private function sanitize_map_shape($shape) {
    if (!is_array($shape) || !isset($shape['type'])) {
      return null;
    }

    $type = sanitize_key($shape['type']);
    if (!in_array($type, ['polygon', 'polyline', 'circle', 'rectangle'], true)) {
      return null;
    }

    $sanitized = [
      'type'        => $type,
      'color'       => $this->sanitize_shape_color(isset($shape['color']) ? $shape['color'] : ''),
      'strokeColor' => $this->sanitize_shape_color(isset($shape['strokeColor']) ? $shape['strokeColor'] : ''),
    ];

    if ($type === 'polygon' || $type === 'polyline') {
      $minimum_points = $type === 'polygon' ? 3 : 2;
      $coordinates    = isset($shape['coord']) && is_array($shape['coord']) ? array_slice($shape['coord'], 0, 1000) : [];
      $sanitized['coord'] = [];

      foreach ($coordinates as $coordinate) {
        $coordinate = $this->sanitize_coordinate_pair($coordinate);
        if ($coordinate) {
          $sanitized['coord'][] = $coordinate;
        }
      }

      return count($sanitized['coord']) >= $minimum_points ? $sanitized : null;
    }

    if ($type === 'circle') {
      $center = isset($shape['center']) ? $this->sanitize_coordinate_pair($shape['center']) : null;
      $radius = isset($shape['radius']) && is_numeric($shape['radius']) ? (float) $shape['radius'] : 0;

      if (!$center || $radius <= 0) {
        return null;
      }

      $sanitized['center'] = $center;
      $sanitized['radius'] = min($radius, 40075000);
      return $sanitized;
    }

    $north_east = isset($shape['ne']) ? $this->sanitize_coordinate_pair($shape['ne']) : null;
    $south_west = isset($shape['sw']) ? $this->sanitize_coordinate_pair($shape['sw']) : null;

    if (!$north_east || !$south_west) {
      return null;
    }

    $sanitized['ne'] = $north_east;
    $sanitized['sw'] = $south_west;
    return $sanitized;
  }

  /**
   * Validate a latitude/longitude pair.
   */
  private function sanitize_coordinate_pair($coordinate) {
    if (!is_array($coordinate) || count($coordinate) !== 2 || !is_numeric($coordinate[0]) || !is_numeric($coordinate[1])) {
      return null;
    }

    $latitude  = (float) $coordinate[0];
    $longitude = (float) $coordinate[1];
    if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
      return null;
    }

    return [$latitude, $longitude];
  }

  /**
   * Allow hex colors and the transparent fill value used by border-only shapes.
   */
  private function sanitize_shape_color($color) {
    if ($color === 'transparent') {
      return 'transparent';
    }

    $color = sanitize_hex_color($color);
    return $color ? $color : '#CC3333';
  }

  
  /**
   * [kml_file_filter Allow the KML file]
   * @param  [type] $mimes [description]
   * @return [type]        [description]
   */
  public function kml_file_filter( $mimes ) {
 
    // New allowed mime types.
    $mimes['kmz']  = 'application/vnd.google-earth.kmz';
    $mimes['kml']  = 'application/vnd.google-earth.kml+xml';
    
    return $mimes;
  }

  /**
   * [upload_kml_file Upload s new KML File]
   * @return [type] [description]
   */
  public function upload_kml_file() {

    //  Only for the administrator
    if(current_user_can('administrator') ) {
      
      //  Temporarily define to pass the KML file
      if (!defined('ALLOW_UNFILTERED_UPLOADS'))
      define( 'ALLOW_UNFILTERED_UPLOADS', true );
    }
    
    //  All the KML file
    add_filter( 'upload_mimes', array($this, 'kml_file_filter') );

    //  Upload the KML File
    $kml_upload  = $this->_file_uploader($_FILES["files"], 'kml');

    //  When the file is uploaded successfully
    if(isset($kml_upload['success']) && $kml_upload['success']) {

      return $this->send_response(['msg' => esc_attr__("KML File uploaded successfully.",'asl_locator'), 'success' => true]);
    }
    else
      return $this->send_response(['error' => $kml_upload['error']]);

    die;
  }


   /**
   * [remove_kml_file Delete the KML file]
   * @return [type] [description]
   */
  public function remove_kml_file() {

    $file_name  = sanitize_text_field($_REQUEST['data_']);
    $response   = \AgileStoreLocator\Helper::removeFile($file_name, ASL_UPLOAD_DIR.'kml/');

    return $this->send_response($response);
  }
  
  
}
