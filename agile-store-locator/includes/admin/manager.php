<?php

namespace AgileStoreLocator\Admin;


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


use AgileStoreLocator\Activator;
use AgileStoreLocator\Deactivator;
use AgileStoreLocator\Frontend\Request;
use AgileStoreLocator\Helper;
use AgileStoreLocator\Admin\Store;
use AgileStoreLocator\Admin\Base;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/Admin/Manager
 * @author     AgileStoreLocator Team <support@agilelogix.com>
 */
class Manager extends Base {

  /**
   * Search published, publicly queryable content for the Internal Page Link field.
   */
  public function search_internal_pages() {
    check_ajax_referer('asl-nounce', 'nonce');

    if (!current_user_can('edit_posts')) {
      wp_send_json_error(['message' => esc_html__('You are not allowed to search pages.', 'asl_locator')], 403);
    }

    $search = isset($_GET['search']) ? sanitize_text_field(wp_unslash($_GET['search'])) : '';
    $post_types = get_post_types(['public' => true, 'show_ui' => true], 'names');
    unset($post_types['attachment']);

    $query = new \WP_Query([
      'post_type'              => array_values($post_types),
      'post_status'            => 'publish',
      'posts_per_page'         => 20,
      's'                      => $search,
      'orderby'                => $search === '' ? 'date' : 'relevance',
      'order'                  => 'DESC',
      'no_found_rows'          => true,
      'ignore_sticky_posts'    => true,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
    ]);

    $items = [];
    foreach ($query->posts as $post) {
      $url = get_permalink($post);
      if (!$url) {
        continue;
      }

      $items[] = [
        'title' => html_entity_decode(get_the_title($post), ENT_QUOTES, get_bloginfo('charset')),
        'type'  => get_post_type_object($post->post_type)->labels->singular_name ?? $post->post_type,
        'url'   => wp_make_link_relative($url),
      ];
    }

    wp_send_json_success(['items' => $items]);
  }


  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string    $AgileStoreLocator    The ID of this plugin.
   */
  protected $AgileStoreLocator;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string    $version    The current version of this plugin.
   */
  protected $version;


  /**
   * [$scripts_data load the scripts]
   * @var array
   */
  protected $scripts_data = array();


  /**
   * [$load_config this configuration is loaded on class initialization to perform rewrite and hook validation]
   * @var [type]
   */
  protected $load_config;
  
  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $AgileStoreLocator       The name of this plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct( $AgileStoreLocator, $version ) {

    $this->AgileStoreLocator = $AgileStoreLocator;
    $this->version           = function_exists('wp_get_environment_type') && wp_get_environment_type() == 'development' ? time(): $version;


    parent::__construct();

    
    //  Not for the activation
    if(!isset($_REQUEST['action']) || $_REQUEST['action'] != 'activate') {

      //  Fetch the basic configs such as rewrites and hook info
      $this->load_config = \AgileStoreLocator\Helper::get_configs(['rewrite_slug', 'rewrite_id', 'cf7_hook']);

      //  Pretty URL for the Store Locator
      add_action('init', array($this,'rewrite_slug') );
      
      //  Run the scheduling job
      \AgileStoreLocator\Admin\Schedule::init();
    }
    
    // Whitelist the Variable 
    add_filter( 'query_vars', array($this,'rewrite_query_vars'));

    // Shortcode Button (Classic editor)
    add_action('media_buttons', array($this,'add_shortcode_button'), 15); 

    // Generate shortcode popup 
    add_action('admin_head', array($this,'shortcode_gen_popup'));

    // Shortcode registration can translate WPBakery labels, so run it after i18n is ready.
    add_action('init', array($this,'shortcode_registration'));

    
  }

  /**
   * Register the stylesheets for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_styles() {

    /**
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in \AgileStoreLocator\Loader as all of the hooks are defined
     * in that particular class.
     *
     * The \AgileStoreLocator\Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     */

    $asl_page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';

    if ('sl-ui-customizer' === $asl_page) {
      wp_enqueue_style( 'asl_ui_customizer', ASL_URL_PATH . 'admin/css/ui-customizer.css', array(), $this->version, 'all' );
      return;
    }

    $asl_bootstrap_css_path = ASL_PLUGIN_PATH . 'admin/css/bootstrap.min.css';
    $asl_bootstrap_css_ver  = file_exists($asl_bootstrap_css_path) ? filemtime($asl_bootstrap_css_path) : $this->version;
    wp_enqueue_style( $this->AgileStoreLocator, ASL_URL_PATH . 'admin/css/bootstrap.min.css', array(), $asl_bootstrap_css_ver, 'all' );

    wp_enqueue_style( 'asl_chosen_plugin', ASL_URL_PATH . 'admin/css/chosen.min.css', array(), $this->version, 'all' );
    $asl_admin_css_path = ASL_PLUGIN_PATH . 'admin/css/style.css';
    $asl_admin_css_ver  = file_exists($asl_admin_css_path) ? filemtime($asl_admin_css_path) : $this->version;
    wp_enqueue_style( 'asl_locator', ASL_URL_PATH . 'admin/css/style.css', array(), $asl_admin_css_ver, 'all' );
    if (\AgileStoreLocator\Helper::expertise_level()) {
      wp_add_inline_style( 'asl_locator', '.sl-complx { display: none !important; }' );
    }
    $asl_sweetalert_css_path = ASL_PLUGIN_PATH . 'admin/css/sweetalert-ui.css';
    $asl_sweetalert_css_ver  = file_exists($asl_sweetalert_css_path) ? filemtime($asl_sweetalert_css_path) : $this->version;
    wp_enqueue_style( 'asl_sweetalert_ui', ASL_URL_PATH . 'admin/css/sweetalert-ui.css', array('asl_locator'), $asl_sweetalert_css_ver, 'all' );
    wp_enqueue_style( 'asl_cards', ASL_URL_PATH . 'admin/css/asl-cards.css', array(), $this->version, 'all' );
    wp_enqueue_style( 'asl_cards_public', ASL_URL_PATH . 'public/css/cards/cards.css', array(), $this->version, 'all' );
    wp_enqueue_style( 'fontello', ASL_URL_PATH . 'public/css/icons/fontello.css', array(), $this->version, 'all' );
    wp_enqueue_style( 'asl_datatable2', ASL_URL_PATH . 'admin/datatable/media/css/jquery.dataTables.min.css', array(), $this->version, 'all' );
    wp_enqueue_style( 'asl_datetimepicker', ASL_URL_PATH . 'admin/css/daterangepicker.css', array(), $this->version, 'all' );

    $asl_grid_pages = array(
      'manage-agile-store',
      'manage-store-markers',
      'manage-store-logos',
      'manage-asl-categories',
      'manage-asl-attributes',
      'create-agile-store',
      'edit-agile-store',
      'asl-settings',
      'import-store-list',
      'customize-map',
    );

    if (in_array($asl_page, $asl_grid_pages, true)) {
      $asl_grid_css_path = ASL_PLUGIN_PATH . 'admin/css/admin-grid.css';
      $asl_grid_css_ver  = file_exists($asl_grid_css_path) ? filemtime($asl_grid_css_path) : $this->version;
      wp_enqueue_style( 'asl_admin_grid', ASL_URL_PATH . 'admin/css/admin-grid.css', array('asl_locator', 'asl_datatable2'), $asl_grid_css_ver, 'all' );
    }

    if ('agile-dashboard' === $asl_page) {
      wp_enqueue_style( 'asl_dashboard', ASL_URL_PATH . 'admin/css/dashboard.css', array($this->AgileStoreLocator, 'asl_locator'), $this->version, 'all' );
      wp_enqueue_style( 'asl_dashboard_palette', ASL_URL_PATH . 'admin/css/dashboard-palette.css', array('asl_dashboard'), $this->version, 'all' );
      wp_enqueue_style( 'asl_dashboard_features', ASL_URL_PATH . 'admin/css/dashboard-features.css', array('asl_dashboard'), $this->version, 'all' );
    }

  }

  /**
   * Register the JavaScript for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts() {

    //  store locator bootstrap
    wp_register_script( 'asl-bootstrap', ASL_URL_PATH . 'admin/js/bootstrap.min.js', array('jquery'), $this->version, false );

    //  Store locator libraries
    wp_register_script( $this->AgileStoreLocator.'-lib', ASL_URL_PATH . 'admin/js/libs.min.js', array('jquery'), $this->version, false );

    // Load feedback assets before the admin header renders on both entry pages.
    $feedback_page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
    if (in_array($feedback_page, ['agile-dashboard', 'asl-settings'], true)) {
      Feedback::enqueue($this->AgileStoreLocator.'-lib');
    }

    //  Shortcode
    wp_register_script( $this->AgileStoreLocator.'-shortcode', ASL_URL_PATH . 'admin/js/shortcode.js', array('jquery'), $this->version, false );    

    //  Sviper library
    wp_register_script( $this->AgileStoreLocator.'-sviper', ASL_URL_PATH . 'admin/js/sviper.js', array('jquery'), $this->version, false );

    //  CHosen library
    wp_register_script( $this->AgileStoreLocator.'-choosen', ASL_URL_PATH . 'admin/js/chosen.proto.min.js', array('jquery'), $this->version, false );
      
    //  Datatable
    wp_register_script( $this->AgileStoreLocator.'-datatable', ASL_URL_PATH . 'admin/datatable/media/js/jquery.dataTables.min.js', array('jquery'), $this->version, false );
      
    //  Uploader
    wp_register_script( $this->AgileStoreLocator.'-upload', ASL_URL_PATH . 'admin/js/jquery.fileupload.min.js', array('jquery', 'jquery-ui-core'), $this->version, false );

    //  jscript
    $asl_jscript_path = ASL_PLUGIN_PATH . 'admin/js/jscript.js';
    $asl_jscript_ver  = file_exists($asl_jscript_path) ? filemtime($asl_jscript_path) : $this->version;
    wp_register_script( $this->AgileStoreLocator.'-jscript', ASL_URL_PATH . 'admin/js/jscript.js', array('jquery'), $asl_jscript_ver, false );
    $asl_common_map_path = ASL_PLUGIN_PATH . 'public/js/asl-common-map.js';
    $asl_common_map_ver = file_exists($asl_common_map_path) ? filemtime($asl_common_map_path) : $this->version;
    wp_register_script( $this->AgileStoreLocator.'-common-map', ASL_URL_PATH . 'public/js/asl-common-map.js', array(), $asl_common_map_ver, false );
    wp_localize_script($this->AgileStoreLocator.'-common-map', 'ASL_NOMINATIM_CONFIG', ['ajaxUrl' => admin_url('admin-ajax.php')]);
    wp_register_script( $this->AgileStoreLocator.'-maplibre', ASL_URL_PATH . 'public/js/maplibre-gl.js', array(), $this->version, false );
    wp_register_style( $this->AgileStoreLocator.'-maplibre', ASL_URL_PATH . 'public/css/maplibre-gl.css', array(), $this->version );
    wp_register_style( $this->AgileStoreLocator.'-maplibre-asl', ASL_URL_PATH . 'public/css/asl-maplibre.css', array($this->AgileStoreLocator.'-maplibre'), $this->version );
    wp_register_style( $this->AgileStoreLocator.'-autocomplete', ASL_URL_PATH . 'public/css/asl-autocomplete.css', array(), $this->version );

    //  drawing
    wp_register_script( $this->AgileStoreLocator.'-draw', ASL_URL_PATH . 'admin/js/drawing.js', array('jquery'), $this->version, false );

    //  Datetimepicker
    wp_register_script( $this->AgileStoreLocator.'-datetimepicker', ASL_URL_PATH . 'admin/js/datetimepicker.min.js', array('jquery'), $this->version, false );
    wp_register_script( $this->AgileStoreLocator.'-daterangepicker', ASL_URL_PATH . 'admin/js/unminified/daterangepicker.js', array('jquery'), $this->version, false );
    
    //  Dashboard
    wp_register_script( $this->AgileStoreLocator.'-dashboard', ASL_URL_PATH . 'admin/js/dashboard.js', array('jquery', $this->AgileStoreLocator.'-lib', $this->AgileStoreLocator.'-datetimepicker'), $this->version, false );

    // UI Customizer
    wp_register_script( $this->AgileStoreLocator.'-ui-customizer', ASL_URL_PATH . 'admin/js/ui-customizer.js', array('jquery'), $this->version, true );
  }

  /**
   * [_enqueue_scripts a private enqueue scripts]
   * @return [type] [description]
   */
  public function _enqueue_scripts($all_scripts  = true, $tag = 'jscript') {
    
    $langs = array(
      'select_category'   => esc_attr__('Select Some Options','asl_locator'),
      'no_category'       => esc_attr__('Select Some Options','asl_locator'),
      'geocode_fail'      => esc_attr__('Geocode was not Successful:','asl_locator'),
      'upload_fail'       => esc_attr__('Upload Failed! Please try Again.','asl_locator'),
      'delete_category'   => esc_attr__('Delete Category','asl_locator'),
      'delete_categories' => esc_attr__('Delete Categories','asl_locator'),
      'warn_question'     => esc_attr__('Are you sure you want to ','asl_locator'),
      'delete_it'     => esc_attr__('Delete it!','asl_locator'),
      'duplicate_it'  => esc_attr__('Duplicate it!','asl_locator'),
      'create'        => esc_attr__('Create','asl_locator'),
      'create_it'     => esc_attr__('Create it!','asl_locator'),
      'add_new_question' => esc_attr__('Do you want to add new %s?','asl_locator'),
      'backup_tmpl'   => esc_attr__('Backup Template','asl_locator'),
      'backup_tmpl_msg'   => esc_attr__('Backup of templates is not need if you haven\'t customize the template via plugin editor, are you sure to backup Template into theme root directory?','asl_locator'),
      'backup'            => esc_attr__('Backup','asl_locator'),
      'remove_tmpl'       => esc_attr__('Remove Template','asl_locator'),
      'remove_tmpl_msg'   => esc_attr__('Are you sure to remove Template from the theme root directory?','asl_locator'),
      'remove'          => esc_attr__('Remove','asl_locator'),
      'delete_marker'   => esc_attr__('Delete Marker','asl_locator'),
      'delete_markers'  => esc_attr__('Delete Markers','asl_locator'),
      'delete_logo'     => esc_attr__('Delete Logo','asl_locator'),
      'delete_logos'    => esc_attr__('Delete Selected Logos','asl_locator'),
      'select_special'  => asl_esc_lbl('select') . ' ' . asl_esc_lbl('special'),
      'select_brand'    => asl_esc_lbl('select') . ' ' . asl_esc_lbl('brand'),
      'brand'         => asl_esc_lbl('brand'),
      'special'       => asl_esc_lbl('special'),
      'delete_store'  => esc_attr__('Delete Store','asl_locator'),
      'delete_stores'  => esc_attr__('Delete Stores','asl_locator'),
      'duplicate_stores'  => esc_attr__('Duplicate Selected Store','asl_locator'),
      'start_time'        => esc_attr__('Start Time','asl_locator'),
      'select_logo'       => esc_attr__('Select Logo','asl_locator'),
      'no_logo'           => esc_attr__('No Logo','asl_locator'),
      'select_columns'    => esc_attr__('Select Columns','asl_locator'),
      'no_columns'        => esc_attr__('No Columns','asl_locator'),
      'select_filters'    => esc_attr__('Select Filters','asl_locator'),
      'no_filter'         => esc_attr__('No Filter','asl_locator'),
      'select_slugs'      => esc_attr__('Select Slugs','asl_locator'),
      'search_options'    => esc_attr__('Search...','asl_locator'),
      'no_options_found'  => esc_attr__('No options found','asl_locator'),
      'none'              => esc_attr__('None','asl_locator'),
      'use_image'         => esc_attr__('Use Image','asl_locator'),
      'select_marker'     => esc_attr__('Select Marker','asl_locator'),
      'remove_duplicates' => esc_attr__('Remove Duplicates','asl_locator'),
      'remove_duplicates_text' => esc_attr__('Are you sure you want to remove all duplicate stores?','asl_locator'),
      'yes_remove'        => esc_attr__('Yes, Remove','asl_locator'),
      'end_time'          => esc_attr__('End Time','asl_locator'),
      'select_country'    => esc_attr__('Select Country','asl_locator'),
      'delete_all_stores' => esc_attr__('DELETE ALL STORES','asl_locator'),
      'truncate_stores'   => esc_attr__('Truncate Stores Table','asl_locator'),
      'truncate_stores_text'  => esc_attr__('Are you sure to delete all stores of all languages?','asl_locator'),
      'invalid_file_error'    => esc_attr__('Invalid File, Accepts JPG, PNG, GIF or SVG.','asl_locator'),
      'error_try_again'       => esc_attr__('Error Occured, Please try Again.','asl_locator'),
      'delete_all'            => esc_attr__('DELETE ALL','asl_locator'),
      'api_key_missing'       => esc_attr__('Error! Search and Map will not work due to missing API Key','asl_locator'),
      'warn_save_setting'     => esc_attr__('Save Settings to apply the changes','asl_locator'),
      'close'                 => esc_attr__('Close','asl_locator'),
      'copy'                  => esc_attr__('Copy','asl_locator'),
      'import'                => esc_attr__('Import','asl_locator'),
      'import_config'         => esc_attr__('Import Configuration & Settings','asl_locator'),
      'paste_config_ph'       => esc_attr__('Paste Configuration JSON','asl_locator'),
      'import_config_warn'    => esc_attr__('Warning! the existing configuration will be removed and replaced including the customizations that you have made through the Customizer section!','asl_locator'),
      'export_config'         => esc_attr__('Export Configuration','asl_locator'),
      'required_field'        => esc_attr__('Please correct the error in field','asl_locator'),
      'select_media'          => esc_attr__('Select or Upload Media', 'asl_locator'),
      'enabled'               => esc_attr__('Enabled', 'asl_locator'),
      'disabled'              => esc_attr__('Disabled', 'asl_locator'),
      'no_store_views'        => esc_attr__('No Store Views!', 'asl_locator'),
      'no_search_results'     => esc_attr__('No Search Results!', 'asl_locator')
    );

    wp_enqueue_script( 'asl-bootstrap');
    
    wp_enqueue_script( $this->AgileStoreLocator.'-lib');

    $admin_page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
    if (in_array($admin_page, ['create-agile-store', 'edit-agile-store', 'customize-map', 'asl-settings'], true)) {
      wp_enqueue_script($this->AgileStoreLocator.'-common-map');
      wp_enqueue_style($this->AgileStoreLocator.'-autocomplete');
      $map_vendor = \AgileStoreLocator\Helper::get_configs('map_vendor');
      if ('maplibre' === strtolower((string) $map_vendor)) {
        wp_enqueue_style($this->AgileStoreLocator.'-maplibre');
        wp_enqueue_style($this->AgileStoreLocator.'-maplibre-asl');
        wp_enqueue_script($this->AgileStoreLocator.'-maplibre');
      }
    }

    
    //  These scripts are not need on other pages
    if($all_scripts) {
      wp_enqueue_script( $this->AgileStoreLocator.'-datetimepicker2');
      wp_enqueue_script( $this->AgileStoreLocator.'-daterangepicker');
      wp_enqueue_script( $this->AgileStoreLocator.'-choosen');
      wp_enqueue_script( $this->AgileStoreLocator.'-sviper');
      wp_enqueue_script( $this->AgileStoreLocator.'-datatable');
      wp_enqueue_script( $this->AgileStoreLocator.'-upload');
    }


    //  Script for the page
    switch ($tag) {
      
      case 'dashboard':
        
        $tag = 'dashboard';

        wp_enqueue_script( $this->AgileStoreLocator.'-dashboard');

        break;


      case 'cards':
        
        $tag = 'cards';

        wp_enqueue_script( $this->AgileStoreLocator.'-sviper' );
        wp_enqueue_script( $this->AgileStoreLocator.'-jscript');

        break;


      case 'shortcode':
        
        $tag = 'shortcode';
        wp_enqueue_script( $this->AgileStoreLocator.'-shortcode');
      break;
      

      default:
      
        // Core sortable for drag/drop field ordering (WP bundled)
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script( $this->AgileStoreLocator.'-draw');
        wp_enqueue_script( $this->AgileStoreLocator.'-jscript');

        break;
    }
    
    $attribute_brands = array_values(\AgileStoreLocator\Model\Attribute::get_all_by_id('brands', $this->lang));

    $this->localize_scripts( $this->AgileStoreLocator.'-'.$tag, 'ASL_REMOTE',  array('nounce' => wp_create_nonce('asl-nounce'), 'sl_lang' => $this->lang, 'LANG' => $langs, 'URL' => admin_url( 'admin-ajax.php' ), 'home_url' => home_url('/'), 'logo' => ASL_URL_PATH.'/admin/images/example-logo.png', 'attribute_brands' => $attribute_brands));
    
    //  Inject script with inline_script
    //wp_add_inline_script( $this->AgileStoreLocator.'-'.$tag, $this->get_local_script_data(), 'before');
  }


  /**
   * [approve_via_email Approve the store via email link]
   * @return [type] [description]
   */
  public function approve_via_email() {

    $store_id    = isset($_REQUEST['sl-store'])? intval($_REQUEST['sl-store']): null;
    $verify_code = isset($_REQUEST['sl-verify'])? sanitize_text_field($_REQUEST['sl-verify']): null;

    return Store::verify_store_link($store_id, $verify_code);
  }



  /////////////////////////
  //////////Page Methods //
  /////////////////////////


  /**
   * [admin_manage_attributes Manage Attribute Page]
   * @return [type] [description]
   */
  public function page_manage_attributes() {

    // Ensure the specials-to-brand relationship exists for upgraded installations.
    Activator::add_special_brand_id();

    // add scripts
    $this->_enqueue_scripts();

    include ASL_PLUGIN_PATH.'admin/partials/attribute.php';
  }

  /**
   * [admin_ui_customizer ASL Settings Page]
   * @return [type] [description]
   */
  public function page_ui_customizer() {
    wp_enqueue_script($this->AgileStoreLocator.'-ui-customizer');
    wp_localize_script($this->AgileStoreLocator.'-ui-customizer', 'ASL_CUSTOMIZER', array(
      'ajaxUrl' => admin_url('admin-ajax.php'),
      'nonce'   => wp_create_nonce('asl-nounce'),
      'strings' => array(
        'loading'    => esc_html__('Loading…', 'asl_locator'),
        'saving'     => esc_html__('Saving…', 'asl_locator'),
        'saved'      => esc_html__('Settings saved successfully.', 'asl_locator'),
        'resetting'  => esc_html__('Resetting…', 'asl_locator'),
        'confirm'    => esc_html__('Reset this template to its default settings?', 'asl_locator'),
        'loadFirst'  => esc_html__('Load a template before saving.', 'asl_locator'),
        'error'      => esc_html__('Something went wrong. Please try again.', 'asl_locator'),
      ),
    ));

    $all_configs = array();

    include ASL_PLUGIN_PATH.'admin/partials/ui-customizer.php';
  } 

  /**
   * [admin_plugin_settings Admin Plugi]
   * @return [type] [description]
   */
  public function page_plugin_settings() {

    // add scripts
    $this->_enqueue_scripts();


    include ASL_PLUGIN_PATH.'admin/partials/add_store.php';
  }

  /**
   * [page_edit_store Edit a Store]
   * @return [type] [description]
   */
  public function page_edit_store() {

    $this->_enqueue_scripts();

    // For Logo
    wp_enqueue_media();

    // For textarea
    wp_enqueue_editor(); // Required for TinyMCE


    global $wpdb;
      
    $store_id = isset($_REQUEST['store_id'])? intval($_REQUEST['store_id']): 0;

    if(!$store_id) {

      die('Invalid Store Id.');
    }

    //  Store Data
    $store  = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."stores WHERE id = $store_id");    


    if(!$store || !$store[0]) {
      die('Invalid Store Id');
    }
  
    //  Take the first store    
    $store = $store[0];


    $storecategory = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."stores_categories WHERE store_id = $store_id");

    //  Current store lang
    $lang      = $store->lang;

    $countries  = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."countries ORDER BY `country`");

    $logos     = $this->prepare_logo_dropdown_rows($wpdb->get_results( "SELECT `id` as `value`, `name` as `text`, `path` as `imageSrc`  FROM ".ASL_PREFIX."storelogos ORDER BY name"));
    $markers   = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."markers");
    $category  = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."categories WHERE lang = '$lang'");

    //  Custom Fields
    $fields       = $this->_get_custom_fields();
    $field_sections = $this->_partition_custom_fields($fields);
    $address_custom_fields = $field_sections['address'];
    $other_custom_fields   = $field_sections['other'];
    $custom_data  = (isset($store->custom) && $store->custom)? json_decode($store->custom, true): []; 


    $all_configs = \AgileStoreLocator\Helper::get_configs(['api_key', 'time_format', 'branches', 'map_vendor', 'tile_provider', 'tile_provider_style', 'tile_provider_api_key', 'maplibre_style_url', 'geoapify_api_key', 'mapbox_access_token', 'search_provider', 'country_restrict', 'map_type', 'zoom', 'minzoom', 'maxzoom']);

    include ASL_PLUGIN_PATH.'admin/partials/edit_store.php';    
  }


  /**
   * [admin_add_new_store Add a New Store]
   * @return [type] [description]
   */
  public function page_add_new_store() {
    
    global $wpdb;

    $this->_enqueue_scripts();

    // For Logo
    wp_enqueue_media();

    // For textarea
    wp_enqueue_editor(); // Required for TinyMCE


    //api key
    $sql = "SELECT `key`,`value` FROM ".ASL_PREFIX."configs WHERE `key` IN ('api_key', 'time_format', 'default_lat', 'default_lng', 'map_vendor', 'tile_provider', 'tile_provider_style', 'tile_provider_api_key', 'maplibre_style_url', 'geoapify_api_key', 'mapbox_access_token', 'search_provider', 'country_restrict', 'map_type', 'zoom', 'minzoom', 'maxzoom')";
    $all_configs_result = $wpdb->get_results($sql);


    $all_configs = array();

    foreach($all_configs_result as $c) {
      $all_configs[$c->key] = $c->value;
    }

    //  Current store lang
    $lang       = $this->lang;

    $logos      = $this->prepare_logo_dropdown_rows($wpdb->get_results( "SELECT `id` as `value`, `name` as `text`, `path` as `imageSrc`  FROM ".ASL_PREFIX."storelogos ORDER BY name"));
    $markers    = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."markers");
    $category   = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."categories WHERE lang = '$lang';");
    $countries  = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."countries ORDER BY `country`");

    $fields = $this->_get_custom_fields();
    $field_sections = $this->_partition_custom_fields($fields);
    $address_custom_fields = $field_sections['address'];
    $other_custom_fields   = $field_sections['other'];
    
    include ASL_PLUGIN_PATH.'admin/partials/add_store.php';    
  }


  /**
   * [admin_dashboard Plugin Dashboard]
   * @return [type] [description]
   */
  public function page_dashboard() {

    $this->_enqueue_scripts(false, 'dashboard');

    global $wpdb;

    $all_configs = \AgileStoreLocator\Helper::get_configs([
      'api_key', 'map_vendor', 'tile_provider', 'geoapify_api_key',
      'mapbox_access_token', 'tile_provider_api_key', 'maplibre_style_url'
    ]);
    $all_stats = array();
    
    $temp = $wpdb->get_results( "SELECT count(*) as c FROM ".ASL_PREFIX."markers");;
    $all_stats['markers']  = $temp[0]->c; 

    $temp = $wpdb->get_results( "SELECT count(*) as c FROM ".ASL_PREFIX."stores");;
    $all_stats['stores']    = $temp[0]->c;

  
    $temp = $wpdb->get_results( "SELECT count(*) as c FROM ".ASL_PREFIX."categories");;
    $all_stats['categories'] = $temp[0]->c;

    $month_start = current_time('Y-m-01 00:00:00');

    $temp = $wpdb->get_results($wpdb->prepare(
      "SELECT count(*) as c FROM ".ASL_PREFIX."stores_view WHERE is_search = 1 AND created_on >= %s",
      $month_start
    ));
    $all_stats['searches'] = $temp[0]->c;

    $temp = $wpdb->get_results($wpdb->prepare(
      "SELECT count(*) as c FROM ".ASL_PREFIX."stores_view WHERE is_search = 0 AND created_on >= %s",
      $month_start
    ));
    $all_stats['clicks'] = $temp[0]->c;


    include ASL_PLUGIN_PATH.'admin/partials/dashboard.php';    
  }



  /**
   * [admin_manage_categories Manage Categories]
   * @return [type] [description]
   */
  public function page_manage_categories() {

    $this->_enqueue_scripts();

    include ASL_PLUGIN_PATH.'admin/partials/categories.php';
  }


  /**
   * [page_manage_cards Manage Grid]
   * @return [type] [description]
   */
  public function page_manage_cards() {

    $this->_enqueue_scripts(false, 'cards');

    // Load the external CSS file for the Cards
    wp_enqueue_style( 'asl_cards', ASL_URL_PATH . 'public/css/cards/cards.css', array(), $this->version, 'all' );

    include ASL_PLUGIN_PATH.'admin/partials/manage-cards.php';
  }
  
  /**
   * [admin_store_markers Manage Markers]
   * @return [type] [description]
   */
  public function page_store_markers() {
    
    $this->_enqueue_scripts();

    include ASL_PLUGIN_PATH.'admin/partials/markers.php';
  }


  /**
   * [admin_store_logos Manage Logos]
   * @return [type] [description]
   */
  public function page_store_logos() {

    wp_enqueue_script( $this->AgileStoreLocator.'-datatable');
    $this->_enqueue_scripts(false);
    wp_enqueue_media();

    include ASL_PLUGIN_PATH.'admin/partials/logos.php';
  }
  
  /**
   * [admin_manage_store Manage Stores]
   * @return [type] [description]
   */
  public function page_manage_store() {

    global $wpdb;
    $prefix = ASL_PREFIX;

    $this->_enqueue_scripts();
    wp_enqueue_media();


    $pending_stores = Store::pending_store_count();
    $lang = $this->lang;

    $logos    = $this->prepare_logo_dropdown_rows($wpdb->get_results( "SELECT `id` as `value`, `name` as `text`, `path` as `imageSrc`  FROM ".ASL_PREFIX."storelogos ORDER BY name"));
    $markers  = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."markers");
    $category = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."categories WHERE lang = '$lang'");

    // Field Columns
     $field_columns = array(
      '1' => 'Action',
      '2' => 'Scheduled',
      '3' => 'ID',
      '4' => 'Title',
      '5' => 'Lat',
      '6' => 'Lng',
      '7' => 'Street',
      '8' => 'State',
      '9' => 'City',
      '10' => 'Country',
      '11' => 'Phone',
      '12' => 'Email',
      '13' => 'URL',
      '14' => 'Zip',
      '15' => 'Disabled',
      '16' => 'Categories',
      '17' => 'Marker',
      '18' => 'Logo',
      '19' => 'Created'
    );


    $hidden_fields = $wpdb->get_results("SELECT `content` FROM {$prefix}settings WHERE `type` = 'hidden'");


    if($hidden_fields && isset($hidden_fields[0])) {

      $hidden_fields = $hidden_fields[0]->content;
    }
    else
        $hidden_fields = [];


    // Get all config
    $all_configs = \AgileStoreLocator\Helper::get_configs(); 

    $bulk_edit_fields_setting = \AgileStoreLocator\Helper::get_setting('bulk_edit_fields');
    $bulk_edit_fields = $bulk_edit_fields_setting ? json_decode($bulk_edit_fields_setting, true) : ['description', 'open_hours'];
    if (!is_array($bulk_edit_fields) || empty($bulk_edit_fields)) {
      $bulk_edit_fields = ['description', 'open_hours'];
    }

    include ASL_PLUGIN_PATH.'admin/partials/manage_store.php';
  }

  /**
   * [admin_import_stores Admin Import Store Page]
   * @return [type] [description]
   */
  public function page_import_stores() {
    $this->_enqueue_scripts();

    global $wpdb;

    $configs_result = $wpdb->get_results("SELECT `key`,`value` FROM " . ASL_PREFIX . "configs WHERE `key` = 'server_key'");
    $api_key        = isset($configs_result[0]) && $configs_result[0]->value
      ? $configs_result[0]->value
      : esc_attr__('Google API Key is Missing', 'asl_locator');
    $all_stats      = \AgileStoreLocator\Model\Store::get_coordinate_stats();

    include ASL_PLUGIN_PATH.'admin/partials/import_store.php';
  }


  /**
   * [admin_customize_map Customize the Map Page]
   * @return [type] [description]
   */
  public function page_customize_map() {

    $this->_enqueue_scripts();

    $customizer_config_keys = [
      'api_key',
      'default_lat',
      'default_lng',
      'zoom',
      'map_type',
      'map_vendor',
      'tile_provider',
      'tile_provider_style',
      'tile_provider_api_key',
      'maplibre_style_url',
      'geoapify_api_key',
      'mapbox_access_token',
      'search_provider',
      'country_restrict',
      'minzoom',
      'maxzoom',
      'cameracontrol',
      'zoomcontrol',
      'streetviewcontrol',
      'fullscreencontrol',
      'maptypecontrol',
    ];
    $config_list = \AgileStoreLocator\Helper::get_configs($customizer_config_keys);

    $all_configs = [
      'api_key'    => isset($config_list['api_key']) ? $config_list['api_key'] : '',
      'default_lat'=> isset($config_list['default_lat']) ? $config_list['default_lat'] : '-33.947128',
      'default_lng'=> isset($config_list['default_lng']) ? $config_list['default_lng'] : '25.591169',
      'zoom'       => isset($config_list['zoom']) ? $config_list['zoom'] : '5',
      'map_type'   => isset($config_list['map_type']) ? $config_list['map_type'] : 'roadmap',
      'map_vendor' => isset($config_list['map_vendor']) ? $config_list['map_vendor'] : 'google',
      'tile_provider' => isset($config_list['tile_provider']) ? $config_list['tile_provider'] : 'geoapify',
      'tile_provider_style' => isset($config_list['tile_provider_style']) ? $config_list['tile_provider_style'] : 'default',
      'tile_provider_api_key' => isset($config_list['tile_provider_api_key']) ? $config_list['tile_provider_api_key'] : '',
      'maplibre_style_url' => isset($config_list['maplibre_style_url']) ? $config_list['maplibre_style_url'] : '',
      'geoapify_api_key' => isset($config_list['geoapify_api_key']) ? $config_list['geoapify_api_key'] : '',
      'mapbox_access_token' => isset($config_list['mapbox_access_token']) ? $config_list['mapbox_access_token'] : '',
      'search_provider' => isset($config_list['search_provider']) ? $config_list['search_provider'] : 'automatic',
      'country_restrict' => isset($config_list['country_restrict']) ? $config_list['country_restrict'] : '',
      'minzoom' => isset($config_list['minzoom']) ? $config_list['minzoom'] : '',
      'maxzoom' => isset($config_list['maxzoom']) ? $config_list['maxzoom'] : '',
    ];


    $map_customize_json   = \AgileStoreLocator\Helper::get_setting('map', 'map_customize');
    $map_customize_json   = $map_customize_json ? $map_customize_json : '{}';
    $map_customize        = json_decode($map_customize_json, true);

    if (!is_array($map_customize)) {
      $map_customize = [];
    }

    $map_control_defaults = [];
    foreach (['cameracontrol', 'zoomcontrol', 'streetviewcontrol', 'fullscreencontrol', 'maptypecontrol'] as $control_key) {
      if (isset($map_customize['map_controls'][$control_key])) {
        $map_control_defaults[$control_key] = (bool) $map_customize['map_controls'][$control_key];
      } elseif (isset($config_list[$control_key])) {
        $map_control_defaults[$control_key] = in_array(strtolower((string) $config_list[$control_key]), ['1', 'true'], true);
      } else {
        $map_control_defaults[$control_key] = $control_key !== 'cameracontrol';
      }
    }

    // The modern Camera control and legacy Zoom control occupy the same role.
    if ($map_control_defaults['cameracontrol']) {
      $map_control_defaults['zoomcontrol'] = false;
    }

    $files     = \AgileStoreLocator\Helper::get_kml_files();
    $kml_files = [];
    foreach ($files as $file) {
      $file_details = $this->get_kml_file_details($file);
      if ($file_details) {
        $kml_files[] = $file_details;
      }
    }


    //add_action( 'init', 'my_theme_add_editor_styles' );
    include ASL_PLUGIN_PATH.'admin/partials/customize_map.php';
  }

  /**
   * Return display-safe metadata for an uploaded KML or KMZ file.
   */
  private function get_kml_file_details($file) {
    $file = basename((string) $file);
    $path = ASL_UPLOAD_DIR.'kml/'.$file;

    if (!$file || !is_file($path) || !in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['kml', 'kmz'], true)) {
      return null;
    }

    $modified = filemtime($path);
    $size     = filesize($path);

    return [
      'name'          => $file,
      'url'           => ASL_UPLOAD_URL.'kml/'.rawurlencode($file),
      'uploaded_on'   => $modified ? wp_date(get_option('date_format').' '.get_option('time_format'), $modified) : '—',
      'size'          => $size !== false ? size_format($size, 1) : '—',
      'overlay_count' => $this->get_kml_overlay_count($path),
    ];
  }

  /**
   * Count placemarks without extracting KMZ archives to disk.
   */
  private function get_kml_overlay_count($path) {
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $contents  = '';

    if ($extension === 'kml' && is_readable($path) && filesize($path) <= 10 * MB_IN_BYTES) {
      $contents = file_get_contents($path);
    } elseif ($extension === 'kmz' && class_exists('ZipArchive')) {
      $archive = new \ZipArchive();
      if ($archive->open($path) === true) {
        for ($index = 0; $index < $archive->numFiles; $index++) {
          $entry = $archive->statIndex($index);
          if ($entry && preg_match('/\.kml$/i', $entry['name']) && $entry['size'] <= 10 * MB_IN_BYTES) {
            $contents = $archive->getFromIndex($index);
            break;
          }
        }
        $archive->close();
      }
    }

    if (!is_string($contents) || $contents === '') {
      return null;
    }

    preg_match_all('/<\s*Placemark\b/i', $contents, $matches);
    return count($matches[0]);
  }

  /**
   * [addSlugs Add Slug to existing rows]
   */
  private function addSlugs() {

    global $wpdb;

    $ASL_PREFIX = ASL_PREFIX;
    $query      = "SELECT s.`id`, `title`,  `description`, `street`,  `city`,  `state`, `postal_code`, `lat`,`lng`,`phone`,  `fax`,`email`,`website`,`logo_id`,`marker_id`,`description_2`,`open_hours`, `ordr`, `custom`, `slug` FROM {$ASL_PREFIX}stores as s";

    $all_results = $wpdb->get_results($query);
    
    //  Generate Slug
    
    
    foreach ($all_results as $store ) {
      
      $a_store = (Array) $store;

      // Prevent dublication of slug (update function)
      $slug    = \AgileStoreLocator\Schema\Slug::slugify($a_store, null);

      //update into stores table
      $wpdb->update($ASL_PREFIX."stores", array('slug' => $slug),array('id' => $a_store['id']));
    }
  }



  
  /**
   * [admin_user_settings ASL Settings Page]
   * @return [type] [description]
   */
  public function page_user_settings() {
     
    $this->_enqueue_scripts();

     //  Current store lang
    $lang       = $this->lang;

    // CodeMirror Enqueue
    if(function_exists('wp_enqueue_code_editor'))
      wp_enqueue_code_editor(array(
        'type' => 'php'
      )
    );

    global $wpdb;

    //  Get the Cache Settings
    $cache_settings = \AgileStoreLocator\Helper::getSettings('cache');

    //  make it empty array when not saved
    if(!$cache_settings) {
      $cache_settings = [];
    }

    //  Langs
    $active_langs   = \AgileStoreLocator\Helper::getLangControl(true);

    ///////////////////////////////////////
    //  Check the upgrade is done or not? //
    ///////////////////////////////////////

    \AgileStoreLocator\Activator::validate_configs();


    //  Languages Label fix
    \AgileStoreLocator\Activator::fix_trans_labels();

    
    $sql = "SELECT `key`,`value` FROM ".ASL_PREFIX."configs";
    $all_configs_result = $wpdb->get_results($sql);

    $query = "SELECT `type`,`content` FROM ".ASL_PREFIX."settings";
    $all_setting_result = $wpdb->get_results($query);
    
    $all_configs = array();
    $all_settings = array();

    foreach ($all_setting_result as $key => $value) {
      $all_settings[$value->type] = $value->content;
    }
    

    foreach($all_configs_result as $config)
    {
      $all_configs[$config->key] = $config->value;  
    }

    $all_configs = array_merge($all_configs,$all_settings);

    ///get Countries
    $countries        = $wpdb->get_results("SELECT country,iso_code_2  as code FROM ".ASL_PREFIX."countries");
    
    $custom_map_style = \AgileStoreLocator\Helper::sanitize_custom_map_style(\AgileStoreLocator\Helper::get_setting('map_style', 'map_style'));

    //  Possible values for the slug
    $slug_attr = array('title' => esc_attr__('Title','asl_locator') , 'city' => esc_attr__('City','asl_locator'), 'postal_code' => esc_attr__('Post Code','asl_locator'), 'state' => esc_attr__('State','asl_locator'), 'description' => esc_attr__('Description', 'asl_locator'), 'lang' => esc_attr__('Lang', 'asl_locator'));

    // Remove Google Script tags
    $all_configs['remove_maps_script'] = get_option('asl-remove_maps_script');

    //  Get the Custom Fields
    $fields = $this->_get_custom_fields();

    //  Add the fields in the slug
    if($fields && is_array($fields)) {

      foreach($fields as $field) {

        $slug_attr[$field['name']] = $field['label'];
      }

    }

    
    //  Slug for the Store Details
    if(isset($all_configs['slug_attr_ddl']) && $all_configs['slug_attr_ddl']) {

      $ordered_slugs = explode(',', $all_configs['slug_attr_ddl']);
      
      foreach ($ordered_slugs as $value) {
        if (!isset($slug_attr[$value])) continue;

        $existing_value = $slug_attr[$value];
        unset($slug_attr[$value]);
        $slug_attr = array_merge($slug_attr, [$value => $existing_value]);
      }
    }

    // Store form controls ordering/visibility
    $store_form_controls_setting = \AgileStoreLocator\Helper::get_setting('store_form_controls');
    $store_form_controls = [];

    $ddl_controls = \AgileStoreLocator\Model\Attribute::get_controls();
    $store_form_controls_map = [];

    if ($store_form_controls_setting) {
      $decoded_controls = json_decode($store_form_controls_setting, true);

      if (is_array($decoded_controls)) {
        foreach ($decoded_controls as $control_item) {
          if (!isset($control_item['field'])) {
            continue;
          }
          $store_form_controls_map[$control_item['field']] = $control_item;
        }
      }
    }

    foreach ($ddl_controls as $ddl_control) {
      $field_key = $ddl_control['field'];

      if (isset($store_form_controls_map[$field_key])) {
        $store_form_controls[] = $store_form_controls_map[$field_key];
        unset($store_form_controls_map[$field_key]);
      } else {
        $store_form_controls[] = [
          'field'   => $ddl_control['field'],
          'label'   => $ddl_control['label'],
          'enabled' => 1
        ];
      }
    }

    // Add any remaining custom controls that are not in the default list
    if (!empty($store_form_controls_map)) {
      foreach ($store_form_controls_map as $remaining_control) {
        $store_form_controls[] = $remaining_control;
      }
    }

    // Store form fields (core + dropdown + custom)
    $store_form_field_manager = new \AgileStoreLocator\Form\StoreFormFields($ddl_controls, $fields);
    $store_form_fields = $store_form_field_manager->get_fields();

    include ASL_PLUGIN_PATH.'admin/partials/user_setting.php';
  }


  /**
   * [rewrite_slug ASL Settings Page]
   * @return [type] [description]
   *
   * Pretty URL for the Store Locator
   */
  public function rewrite_slug(){

   $slug      = isset($this->load_config['rewrite_slug'])? $this->load_config['rewrite_slug']: null;
   $page_id   = isset($this->load_config['rewrite_id'])? $this->load_config['rewrite_id']: null;
    
   // Make sure values exist
   if($slug && $page_id) {
     \AgileStoreLocator\Schema\Slug::register_rewrite_rules($slug, $page_id);
   }
  }


  /**
   * [rewrite_query_vars ASL Settings Page]
   * @return [type] [query_vars]
   * 
   * Whitelist the Variable 
   */
  public function rewrite_query_vars($query_vars){
      
      $query_vars[] = 'sl-store';

      return $query_vars;
  }

  /*
  *[add_shortcode_button]
  *Create add shortcode button on admin page 
  *
  */
  public function add_shortcode_button() {
    
    global $post;

    if ($post) {
      if($post->post_type == 'page' )
      {
        echo '<a href="#" id="sl-shortcode-insert" data-bs-toggle="smodal" data-bs-target="#insert-sl-shortcode" class="button">'.__('Add Store Locator Shortcode','asl_locator').'</a>';
      }
    }
  }

  /*
  *[shortcode_gen_popup]
  * shortcode Popup HTML
  *
  */
  public function shortcode_gen_popup() {
    
    global $post;

    if ($post) {
      
      if($post->post_type == 'page') {

        // Add scripts
        $this->_enqueue_scripts(false, 'shortcode');
        
        // Include Add Shortcode admin Popup HTML 
        include ASL_PLUGIN_PATH.'admin/partials/shortcode-popup-html.php';
      }
    }
  }


  /**
   * [asl_logo_uploader] Show the Loader for the Logos
   * Get Gallery HTML
   *
   * @since    0.0.1
   */
  public function asl_logo_uploader( $name, $value = '' ) {

    $html = '<div><ul class="asl_logo_mtb">';
    
    /* array with image IDs for hidden field */
    $hidden = array();

    if( $images = get_posts( array(
      'post_type' => 'attachment',
      'orderby'   => 'post__in', /* we have to save the order */
      'order'     => 'ASC',
      'post__in'  => explode(',',$value), /* $value is the image IDs comma separated */
      'numberposts'    => -1,
      'post_mime_type' => 'image'
    ) ) ) {

      foreach( $images as $image ) {
        $hidden[] = $image->ID;
        $image_src = wp_get_attachment_image_src( $image->ID, 'medium' );
        $html .= '<li data-id="' . $image->ID .  '"><img src="'. $image_src[0] . ')"></li>';
      }

    }

    $html .= '</ul><div style="clear:both"></div></div>';
    $html .= '<input type="hidden" id="'.$name.'" name="data['.$name.']" value="' . join(',',$hidden) . '" /><a class="button btn btn-primary asl_upload_logo_btn">'.esc_attr__('Select Image','asl_locator').'</a>';

    return $html;
  }

  /*
  *[shortcode_registration]
  *
  */
  public function shortcode_registration() {
    
    // For Gutenberg Shortcode button
    require_once ASL_PLUGIN_PATH.'admin/blocks/index.php';

    // For Elementor Shortcode
    //  Add the Elementor
    if ( class_exists( '\Elementor\Plugin' ) ) {
      $ele_addons = new \AgileStoreLocator\Vendors\Elementor\Addon( $this->AgileStoreLocator, $this->version );
    }

    // For WPBakery Shortcode
    if(function_exists('vc_map') && defined('WPB_VC_VERSION')) {
      $vc_addons  = new \AgileStoreLocator\Vendors\WPBakery\Addon( $this->AgileStoreLocator, $this->version );
    }

    //  Initialize the third party hooks to create association
    \AgileStoreLocator\Helper::third_party_hooks($this->load_config);
  }


  /**
   * [add_action_link render the settings button for the plugin]
   * @return [type] $links [description]
   */
  public function add_action_link( $links, $file ) {

    if ( $file !=  ASL_BASE_PATH.'/agile-store-locator.php' ) {
      return $links;
    }

    $settings_url = admin_url( 'admin.php?page=asl-settings' );
    $settings_link = '<a href="' . esc_url( $settings_url ) . '" >' . __( 'Settings', 'asl_locator' ) . '</a>';
    array_unshift( $links, $settings_link );    

    $docs_url = 'https://agilestorelocator.com/wiki/';
    $docs_link = '<a href="' . esc_url( $docs_url ) . '" target="__blank">' . __( 'Docs', 'asl_locator' ) . '</a>';
    array_push( $links, $docs_link );

    return $links;

  }



  /**
   * [localize_scripts description]
   * @param  [type] $script_name [description]
   * @param  [type] $variable    [description]
   * @param  [type] $data        [description]
   * @return [type]              [description]
   */
  private function localize_scripts($script_name, $variable, $data) {

    //$this->scripts_data[] = [$variable, $data]; 

    //  Since version 4.10.7
    wp_localize_script( $script_name, $variable, $data );
  }


  /**
   * Escape logo dropdown data before embedding it into admin JavaScript.
   *
   * @param array $logos Logo rows.
   * @return array
   */
  private function prepare_logo_dropdown_rows($logos) {

    foreach ($logos as $logo) {
      $logo->value    = isset($logo->value) ? intval($logo->value) : 0;
      $logo->text     = isset($logo->text) ? esc_html($logo->text) : '';
      $logo->imageSrc = isset($logo->imageSrc) ? sanitize_file_name($logo->imageSrc) : '';
    }

    return $logos;
  }


  /**
   * [get_local_script_data Render the scripts data]
   * @return [type] [description]
   */
  private function get_local_script_data($with_tags = false) {

    $scripts = '';

    foreach ($this->scripts_data as $script_data) {
        
      $scripts .= 'var '.$script_data[0].' = '.(($script_data[1] && !empty($script_data[1]))?wp_json_encode($script_data[1]): "''").';';
    }

    //  With script tags
    if($with_tags) {

      $scripts = "<script type='text/javascript' id='agile-store-locator-script-js'>".$scripts."</script>";
    }

    //  Clear it
    $this->scripts_data = [];

    return $scripts;
  }

}
