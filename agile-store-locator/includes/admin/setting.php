<?php

namespace AgileStoreLocator\Admin;


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


use AgileStoreLocator\Admin\Base;


/**
 * The settings manager including UI, templates, cache etc functionality of the plugin.
 *
 * @link       https://agilestorelocator.com
 * @since      1.4.3
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/Admin/Setting
 */

class Setting extends Base {


  /**
   * [__construct description]
   */
  public function __construct() {
    
    parent::__construct();
  }


  /**
   * [backup_template Backup the Template into theme Root Directory]
   * @return [type] [description]
   */
  public function backup_template() {

    $template  = isset($_REQUEST['template'])? sanitize_text_field($_REQUEST['template']): null;
    $response  = \AgileStoreLocator\Helper::backup_template($template);

    return $this->send_response($response);
  }


  /**
   * [remove_template Remove the template file from the Theme Directory]
   * @return [type] [description]
   */
  public function remove_template() {

    $template  = isset($_REQUEST['template'])? sanitize_text_field($_REQUEST['template']): null;
    $response  = \AgileStoreLocator\Helper::remove_template($template);

    return $this->send_response($response);
  }


  /**
   * [expertise_level description]
   * @return [type] [description]
   */
  public function expertise_level() {

    $level_status = (isset($_REQUEST['status']) && $_REQUEST['status'] == '1')?'1':'0';
    
    //  Update the expertise level
    update_option('asl-expertise', $level_status);

    $this->send_response(['success' => true, 'msg' => esc_attr__("Level has been changed.",'asl_locator')]);
  }

  
  /**
   * [change_options Save the Settings in the Settings table]
   */
  public function change_options($json_return = false) {

    global $wpdb;
    $prefix = ASL_PREFIX;

    // Data
    $content = isset($_POST['content'])? stripslashes_deep($_POST['content']): null;
    $type    = isset($_POST['stype'])? stripslashes_deep($_POST['stype']): null;

    //  Response
    $response  = new \stdclass();
    $response->success = false;

    //  When type is hidden
    if(in_array($type, ['hidden', 'cache'])) {

      $c = $wpdb->get_results("SELECT count(*) AS 'count' FROM {$prefix}settings WHERE `type` = '{$type}'");

      $data_params = array('content' => json_encode($content), 'type'=> $type);


      if($c[0]->count  >= 1) {
        $wpdb->update($prefix."settings", $data_params, array('type'=> $type));
      }
      else {
        $wpdb->insert($prefix."settings", $data_params);
      }

      $response->msg     = esc_attr__("Settings has been updated.",'asl_locator');
      $response->success = true;
    }

    //  return as JSON
    if($json_return) {
      return $response;
    }

  
    return $this->send_response($response);    
  }




  /**
   * [save_setting save ASL Setting]
   * @return [type] [description]
   */
  public function save_setting() {

    global $wpdb;

    $response  = new \stdclass();

    //  Settings data
    $data_     = stripslashes_deep($_POST['data']);

    //  Remove Script tag will be saved in wp_options
    $remove_script_tag = $data_['remove_maps_script'];
    unset($data_['remove_maps_script']);


    //  Config keys
    $keys     =  array_keys($data_);

    // Hava a value?
    if (isset($data_['country_restrict']) && $data_['country_restrict']) {

      // Restrict the country validation
      $validation_result = $this->validate_country_restrictions($data_['country_restrict']);
      
      if (!$validation_result['valid']) {
        
        $response->msg = esc_attr__("Invalid value for country restriction. Only ISO 3166-1 alpha-2 country codes are supported.", 'asl_locator');
        $response->success  = false;
        return $this->send_response($response);
      }

      $data_['country_restrict'] = $validation_result['country_restrict'];
    }

    //  Loop over the setting items
    foreach($keys as $key) {

      $wpdb->update(ASL_PREFIX."configs",
        array('value' => $data_[$key]),
        array('key' => $key)
      );
    }

    //  register/de-register the lead cron job for the follow-ups
    if(isset($data_['lead_follow_up']))
      \AgileStoreLocator\Cron\LeadCron::schedule_cron($data_['lead_follow_up'], $data_);

    ///////////////////////////
    //  Save Custom Settings //
    ///////////////////////////
    $custom_map_style = $_POST['map_style'];

    //  Custom Map Style
    \AgileStoreLocator\Helper::set_setting(stripslashes($custom_map_style), 'map_style', 'map_style');


    update_option('asl-remove_maps_script', $remove_script_tag);

    $response->msg     = esc_attr__("Setting has been updated successfully.",'asl_locator');
    $response->success = true;

    //  Valid the Default Coordinates
    $is_valid  = \AgileStoreLocator\Helper::validate_coordinate($data_['default_lat'], $data_['default_lng']);

    //  is invalid?
    if(!$is_valid) {

      $response->msg     .= '<br>'.esc_attr__("Error! Default Lat & Lng are invalid values, please try to swap them.",'asl_locator');
      $response->success  = false;
    }

    return $this->send_response($response);
  }  


  /**
   * [manage_cache Refresh the JSON]
   * @return [type] [description]
   */
  public function manage_cache() {

    global $wpdb;

    $response = new \stdclass();
    $response->success = false;


    $cache_status = (isset($_REQUEST['status']) && $_REQUEST['status'] == '1')?'1':'0';
    $cache_lang   = (isset($_REQUEST['asl-lang']))? sanitize_text_field($_REQUEST['asl-lang']): null;

    //  Todo, Make sure the folder exist?
    if(!file_exists(ASL_UPLOAD_DIR)) {
      mkdir( ASL_UPLOAD_DIR, 0775, true );
    }

    if(!$cache_lang) {
      $response->error = esc_attr__('Error! Lang is not defined.','asl_locator');;
    }

    //  en_US is default
    if($cache_lang == 'en_US')
      $cache_lang = '';


    //  JSON file
    $json_file = 'locator-data'.(($cache_lang)?'-'.$cache_lang: '').'.json';

    //  Generate the JSON file when enabled
    if($cache_status == '1') {

      //  Generate the Output
      $public_request = new \AgileStoreLocator\Frontend\Request();
      $output_result  = $public_request->load_stores(true, $cache_lang);

      //  Save the output
      $response->output   = file_put_contents(ASL_UPLOAD_DIR.$json_file, json_encode($output_result));

      //  When fails
      if(!$response->output) {
        $response->path   = ASL_UPLOAD_DIR.$json_file;
      }

      $response->msg      = esc_attr__('Cache JSON has been generated successfully for language '.$cache_lang,'asl_locator');

    }
    else
      $response->msg      = esc_attr__('Cache JSON is disabled for language '.$cache_lang,'asl_locator');


    //  Save the cache settings
    $this->change_options(true);

    //  Show as success
    $response->success  = true;
  
    return $this->send_response($response);
  }



  /**
   * [load_custom_template Load ASL Custom Template]
   * @return [type] [description]
   */
  public function load_custom_template() {

    global $wpdb;

    $response          = new \stdclass();
    $response->success = false;

    $data_ = stripslashes_deep($_POST);

    //  List template doesn't have any infobox
    if($data_['template'] == 'template-list' && $data_['section'] == 'infobox') {
      
      $response->error = esc_attr__("List Template has no Map.",'asl_locator');
      return $this->send_response($response);
    }

    $html  = '';
    $count = $wpdb->get_results($wpdb->prepare("SELECT COUNT('name') as 'count' FROM ".ASL_PREFIX."settings WHERE `name` = %s AND `type` = %s", $data_['template'], $data_['section']));

    if($count[0]->count  >= 1) {

      //  Template Query 
       $results = $wpdb->get_results($wpdb->prepare("SELECT `content` FROM ".ASL_PREFIX."settings WHERE `name` = %s AND `type` = %s", $data_['template'],$data_['section'])  ,ARRAY_A );

       if ($results)
        $html = $results[0]['content'];

    }
    else {

        //  open stream
        ob_start();
        // include simple products HTML
        include ASL_PLUGIN_PATH.'public/views/'.$data_['template'].'-'.$data_['section'].'.html';

        $html = ob_get_contents();
        
        //  clean it
        ob_end_clean();
        
    }

      
    if (!empty( $html)) {

      $response->html = $html;

      $response->msg     = esc_attr__("HTML added in TextEditor",'asl_locator');
      $response->success = true;
    }


    return $this->send_response($response);
  }


  /**
   * [save_custom_template Load ASL Custom Template]
   * @return [type] [description]
   */
  public function save_custom_template() {

    global $wpdb;

    $response   = new \stdclass();
    $response->success = false;

    $data_ = stripslashes_deep($_POST);

    //  get previous quantity
    $count = $wpdb->get_results($wpdb->prepare("SELECT COUNT('name') as 'count' FROM ".ASL_PREFIX."settings WHERE `name` = %s AND `type` = %s", $data_['template'],  $data_['section']));
    
    if (!empty($data_['html'])) {

      $data_params = array('name' =>  $data_['template'] ,'type' => $data_['section'],'content' => $data_['html'] );

      if($count[0]->count  >= 1) {
        
        //  Execute the Update Query
        $wpdb->update(ASL_PREFIX."settings", $data_params, array('name'=> $data_['template'] ,'type' => $data_['section']));

      }
      else{
        //  Execute the Insert Query
        $wpdb->insert(ASL_PREFIX."settings", $data_params);
      }

      $response->msg     = esc_attr__("Template Updated",'asl_locator');
      $response->success = true;
    }
        



    return $this->send_response($response);
  }

  /**
   * [reset_custom_template Load ASL Custom Template]
   * @return [type] [description]
   */
  public function reset_custom_template() {

    global $wpdb;

    $response  = new \stdclass();
    $response->success = false;

    $data_ = stripslashes_deep($_POST);

    //  open stream
    ob_start();

    // include simple products HTML
    include ASL_PLUGIN_PATH.'public/views/'.$data_['template'].'-'.$data_['section'].'.html';
    
    $html = ob_get_contents();
    
    //  clean it
    ob_end_clean();

    $response->html    = $html;
    $response->msg     = esc_attr__("Default template is loaded",'asl_locator');
    $response->success = true;

    return $this->send_response($response);
  }


    /**
   * [load_ui_settings Load ASL Custom Template]
   * @return [type] [description]
   */
  public function load_ui_settings() {

    global $wpdb;

    $response          = new \stdclass();
    $response->success = false;

    $template = $_POST['template'];

    $colors   = array(
      'template-0'  => array(
        'primary'   => 'clr-primary',
        'header'    => '',
        'header-color'  => '',
        'infobox-color' => '',
        'infobox-bg'    => '',
        'infobox-a'     => 'clr-copy',
        'action-btn-color'  => '',
        'action-btn-bg'     => 'clr-copy',
        'color'   => '',
        'list-bg' => '',
        'list-title'      => '',
        'list-sub-title'  => '',
        'highlighted'     => ''
      ),
      'template-wc'  => array(
        'primary'   => 'clr-primary'
      ),
    );


    $white                  = '#FFFFFF';
    $black                  = '#000000';

    $tmpl_0_primary         = '#cb2800';
    $tmpl_0_title_color     = '#32373c';
    $tmpl_0_sub_title_color = '#6a6a6a';
    $tmpl_0_list_color      = '#555d66';
    $tmpl_0_header_bg       = '#F7F7F7';
    $tmpl_0_header_color    = '#32373c';
    $tmpl_0_highlighted     = '#F7F7F7';

    $tmpl_1_primary         = '#000000';
    $tmpl_1_secondary       = '#EF5A28';
    $tmpl_1_title_color     = '#32373c';
    $tmpl_1_sub_title_color = '#6a6a6a';
    $tmpl_1_list_color      = '#555d66';
    $tmpl_1_header_bg       = $tmpl_1_primary;
    $tmpl_1_highlighted     = '#F7F7F7';

    $tmpl_2_primary         = '#cb2800';
    $tmpl_2_secondary       = '#cb2800';
    $tmpl_2_title_color     = '#32373c';
    $tmpl_2_sub_title_color = '#6a6a6a';
    $tmpl_2_list_color      = '#555d66';
    $tmpl_2_header_bg       = '#F7F7F7';
    $tmpl_2_highlighted     = '#F7F7F7';

    $tmpl_3_primary         = '#cb2800';
    $tmpl_3_title_color     = '#32373c';
    $tmpl_3_sub_title_color = '#6a6a6a';
    $tmpl_3_list_color      = '#555d66';
    $tmpl_3_header_bg       = '#F7F7F7';
    $tmpl_3_highlighted     = '#F7F7F7';

    //  the default colors that will load with the customizer
    $default_colors = array(
      'template-0'  => array(
        'primary'   => $tmpl_0_primary,
        'header'    => $tmpl_0_header_bg,
        'header-color'  => $tmpl_0_header_color,
        'infobox-color' => $tmpl_0_list_color,
        'infobox-bg'    => $white,
        'infobox-a'     => $tmpl_0_primary,
        'action-btn-color'  => $white,
        'action-btn-bg'     => $tmpl_0_primary,
        'color'   => $tmpl_0_list_color,
        'list-bg' => $white,
        'list-title'      => $tmpl_0_title_color,
        'list-sub-title'  => $tmpl_0_sub_title_color,
        'highlighted'     => $tmpl_0_highlighted,
        'highlighted-list-color' => $tmpl_0_primary
      ),
      'template-wc'  => array(
        'primary'   => $tmpl_0_primary
      )
    );


    $default_fonts  = array(
      'template-0'  => array(
        'font-size'   => 13,
        'title-size'  => 15,
        'btn-size'  => 13
      ),
      'template-wc'  => array(
        'font-size'   => 13,
        'title-size'  => 16,
        'btn-size'  => 13
      )
    );

    
    $html     = '';
    $fields   = '';


    //  Only get the array of active default color
    $default_colors  = $default_colors[$template];
    $default_fonts   = $default_fonts[$template];

    $fields_settings = \AgileStoreLocator\Helper::get_setting('ui-template', $template);

    if($fields_settings) {

      $fields = json_decode($fields_settings);
    }

    //  Start Stream
    ob_start();

    // include ui customizer fields products HTML
    include ASL_PLUGIN_PATH.'admin/partials/ui-customizer-fields.php';
  
    $html = ob_get_contents();

    //  Clean it
    ob_end_clean();

    $response->html     = $html;
    $response->msg      = esc_attr__("Template UI settings updated",'asl_locator');
    $response->success  = true;

    return $this->send_response($response);
  }


  /**
   * [sl_theme_ui_save Save ASL UI Settings]
   * @return [type] [description]
   */
  public function sl_theme_ui_save() {

    global $wpdb;


    $response  = new \stdclass();
    $response->success = false;

    $data_    = stripslashes_deep($_POST['sl_formData']);
    $template = sanitize_text_field($_POST['sl_template']);

    $data     = json_encode($data_);
      
    \AgileStoreLocator\Helper::set_setting($data, 'ui-template', $template);

    $response->msg     = esc_attr__("Template updated",'asl_locator');
    $response->success = true;

    return $this->send_response($response);
  }


  /**
   * [validate_country_restrictions Validate the country restriction]
   * @param  [type] $country_restrict [description]
   * @return [type]                   [description]
   */
  private function validate_country_restrictions($country_restrict) {
      
      // List of valid ISO 3166-1 alpha-2 country codes
      $valid_countries = [
          'AF', 'AX', 'AL', 'DZ', 'AS', 'AD', 'AO', 'AI', 'AQ', 'AG', 'AR', 'AM', 'AW', 'AU', 'AT', 'AZ',
          'BS', 'BH', 'BD', 'BB', 'BY', 'BE', 'BZ', 'BJ', 'BM', 'BT', 'BO', 'BQ', 'BA', 'BW', 'BV', 'BR',
          'IO', 'BN', 'BG', 'BF', 'BI', 'CV', 'KH', 'CM', 'CA', 'KY', 'CF', 'TD', 'CL', 'CN', 'CX', 'CC',
          'CO', 'KM', 'CG', 'CD', 'CK', 'CR', 'HR', 'CU', 'CW', 'CY', 'CZ', 'DK', 'DJ', 'DM', 'DO', 'EC',
          'EG', 'SV', 'GQ', 'ER', 'EE', 'SZ', 'ET', 'FK', 'FO', 'FJ', 'FI', 'FR', 'GF', 'PF', 'TF', 'GA',
          'GM', 'GE', 'DE', 'GH', 'GI', 'GR', 'GL', 'GD', 'GP', 'GU', 'GT', 'GG', 'GN', 'GW', 'GY', 'HT',
          'HM', 'VA', 'HN', 'HK', 'HU', 'IS', 'IN', 'ID', 'IR', 'IQ', 'IE', 'IM', 'IL', 'IT', 'JM', 'JP',
          'JE', 'JO', 'KZ', 'KE', 'KI', 'KP', 'KR', 'KW', 'KG', 'LA', 'LV', 'LB', 'LS', 'LR', 'LY', 'LI',
          'LT', 'LU', 'MO', 'MG', 'MW', 'MY', 'MV', 'ML', 'MT', 'MH', 'MQ', 'MR', 'MU', 'YT', 'MX', 'FM',
          'MD', 'MC', 'MN', 'ME', 'MS', 'MA', 'MZ', 'MM', 'NA', 'NR', 'NP', 'NL', 'NC', 'NZ', 'NI', 'NE',
          'NG', 'NU', 'NF', 'MK', 'MP', 'NO', 'OM', 'PK', 'PW', 'PS', 'PA', 'PG', 'PY', 'PE', 'PH', 'PN',
          'PL', 'PT', 'PR', 'QA', 'RE', 'RO', 'RU', 'RW', 'BL', 'SH', 'KN', 'LC', 'MF', 'PM', 'VC', 'WS',
          'SM', 'ST', 'SA', 'SN', 'RS', 'SC', 'SL', 'SG', 'SX', 'SK', 'SI', 'SB', 'SO', 'ZA', 'GS', 'SS',
          'ES', 'LK', 'SD', 'SR', 'SJ', 'SE', 'CH', 'SY', 'TW', 'TJ', 'TZ', 'TH', 'TL', 'TG', 'TK', 'TO',
          'TT', 'TN', 'TR', 'TM', 'TC', 'TV', 'UG', 'UA', 'AE', 'GB', 'US', 'UM', 'UY', 'UZ', 'VU', 'VE',
          'VN', 'VG', 'VI', 'WF', 'EH', 'YE', 'ZM', 'ZW'
      ];

      $countries = explode(',', $country_restrict);
      $valid = true;
      $countries_to_restrict = [];

      foreach ($countries as $country) {
          $country = strtoupper(trim($country));
          $countries_to_restrict[] = $country;
          if (!in_array($country, $valid_countries)) {
              $valid = false;
              break;
          }
      }

      return [
          'valid' => $valid,
          'country_restrict' => implode(',', $countries_to_restrict)
      ];
  }

  
  

  /**
   * [save_custom_fields Save Custom Fields AJAX]
   * @return [type] [description]
   */
  public function save_custom_fields() {

    global $wpdb;
    $prefix = ASL_PREFIX;

    $response  = new \stdclass();
    $response->success = false;

    $fields = isset($_POST['fields'])? ($_POST['fields']): [];

    //  Filter the JSON for XSS
    $filter_fields = [];

    foreach($fields as $field_key => $field) {

      $field_key = strip_tags($field_key);

      $field['type']  = strip_tags(sanitize_text_field($field['type']));
      $field['name']  = strip_tags(sanitize_text_field($field['name']));
      $field['label'] = strip_tags(sanitize_text_field($field['label']));

      $filter_fields[$field_key] = $field;
    }

    $c = $wpdb->get_results("SELECT count(*) AS 'count' FROM {$prefix}settings WHERE `type` = 'fields'");

    $data_params = array('content' => json_encode($filter_fields), 'type'=> 'fields');


    if($c[0]->count  >= 1) {
      $wpdb->update($prefix."settings", $data_params, array('type'=> 'fields'));
    }
    else {
      $wpdb->insert($prefix."settings", $data_params);
    }

    /*$wpdb->show_errors = true;
    $response->error = $wpdb->print_error();
    $response->error1 = $wpdb->last_error;*/

    

    $response->msg     = esc_attr__("Fields has been updated successfully.",'asl_locator');
    $response->success = true;

        
    return $this->send_response($response);
  }
}