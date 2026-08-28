<?php

namespace AgileStoreLocator\Vendors\WPBakery;

use AgileStoreLocator\Frontend\App;
use AgileStoreLocator\Helper;
use AgileStoreLocator\Model\Store;
use AgileStoreLocator\Model\Category;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed direptly.
}

class StoreLocator
{

    /**
     * Reusable "inherit global settings" option for WPBakery dropdown fields.
     *
     * @since 4.8.21
     */
    private function inherit_option()
    {
        return array(__('Inherit from ASL Settings', 'asl_locator') => '');
    }

    /**
     * Yes/No options with an inherited default.
     *
     * @since 4.8.21
     */
    private function yes_no_options()
    {
        return $this->inherit_option() + array(
            __('Yes', 'asl_locator') => '1',
            __('No', 'asl_locator')  => '0',
        );
    }

    /**
     * Fields exposed in WPBakery as per-shortcode ASL Settings overrides.
     *
     * @since 4.8.21
     */
    private function get_vc_params()
    {
        return array(
            array(
                'type'        => 'dropdown',
                'heading'     => __('Select Template', 'asl_locator'),
                'param_name'  => 'template',
                'value'       => $this->inherit_option() + array(
                    __('Template 0', 'asl_locator')      => '0',
                    __('Template 1', 'asl_locator')      => '1',
                    __('Template 2', 'asl_locator')      => '2',
                    __('Template 3', 'asl_locator')      => '3',
                    __('Template 4', 'asl_locator')      => '4',
                    __('Template 5', 'asl_locator')      => '5',
                    __('Template 6', 'asl_locator')      => '6',
                    __('Template 7', 'asl_locator')      => '7',
                    __('Template List', 'asl_locator')   => 'list',
                    __('Template List 2', 'asl_locator') => 'list-2',
                ),
                'std'         => '',
                'description' => __('Choose a locator template for this page only.', 'asl_locator'),
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => __('Search Type', 'asl_locator'),
                'param_name'  => 'search_type',
                'value'       => $this->inherit_option() + array(
                    __('Search By Address (Google)', 'asl_locator')                    => '0',
                    __('Search By Store Name (Database)', 'asl_locator')               => '1',
                    __('Search By Stores Cities, States (Database)', 'asl_locator')    => '2',
                    __('Geocoding on Enter key (Google Geocoding API)', 'asl_locator') => '3',
                    __('Search By New Place API (Google)', 'asl_locator')              => '4',
                ),
                'std'         => '',
                'description' => __('Override the search behavior for this locator instance.', 'asl_locator'),
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => __('Select Layout', 'asl_locator'),
                'param_name'  => 'layout',
                'value'       => $this->inherit_option() + array(
                    __('List Format', 'asl_locator')                              => '0',
                    __('Accordion (States, Cities, Countries)', 'asl_locator')    => '1',
                    __('Accordion (Categories)', 'asl_locator')                   => '2',
                ),
                'std'         => '',
                'description' => __('Choose how store results are grouped in the list.', 'asl_locator'),
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => __('Distance Control', 'asl_locator'),
                'param_name'  => 'distance_control',
                'value'       => $this->inherit_option() + array(
                    __('Slider', 'asl_locator')       => '0',
                    __('Dropdown', 'asl_locator')     => '1',
                    __('Boundary Box', 'asl_locator') => '2',
                ),
                'std'         => '',
                'description' => __('Choose how users control the search distance.', 'asl_locator'),
            ),
            array(
                'type'        => 'textfield',
                'heading'     => __('Category IDs', 'asl_locator'),
                'param_name'  => 'category',
                'std'         => '',
                'description' => __('Optional comma-separated category IDs to show.', 'asl_locator'),
            ),
            array(
                'type'        => 'textfield',
                'heading'     => __('Store IDs', 'asl_locator'),
                'param_name'  => 'stores',
                'std'         => '',
                'description' => __('Optional comma-separated store IDs to show.', 'asl_locator'),
            ),
            array(
                'type'        => 'textfield',
                'heading'     => __('Default Latitude', 'asl_locator'),
                'param_name'  => 'default_lat',
                'std'         => '',
                'group'       => __('Advanced', 'asl_locator'),
            ),
            array(
                'type'        => 'textfield',
                'heading'     => __('Default Longitude', 'asl_locator'),
                'param_name'  => 'default_lng',
                'std'         => '',
                'group'       => __('Advanced', 'asl_locator'),
            ),
            array(
                'type'        => 'textfield',
                'heading'     => __('Map Zoom', 'asl_locator'),
                'param_name'  => 'zoom',
                'std'         => '',
                'group'       => __('Advanced', 'asl_locator'),
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => __('Distance Unit', 'asl_locator'),
                'param_name'  => 'distance_unit',
                'value'       => $this->inherit_option() + array(
                    __('Miles', 'asl_locator')      => 'Miles',
                    __('Kilometers', 'asl_locator') => 'KM',
                ),
                'std'         => '',
                'group'       => __('Advanced', 'asl_locator'),
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => __('Map Type', 'asl_locator'),
                'param_name'  => 'map_type',
                'value'       => $this->inherit_option() + array(
                    __('Roadmap', 'asl_locator')   => 'roadmap',
                    __('Satellite', 'asl_locator') => 'satellite',
                    __('Hybrid', 'asl_locator')    => 'hybrid',
                    __('Terrain', 'asl_locator')   => 'terrain',
                ),
                'std'         => '',
                'group'       => __('Advanced', 'asl_locator'),
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => __('Display Store List', 'asl_locator'),
                'param_name'  => 'display_list',
                'value'       => $this->yes_no_options(),
                'std'         => '',
                'group'       => __('Advanced', 'asl_locator'),
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => __('Full Width', 'asl_locator'),
                'param_name'  => 'full_width',
                'value'       => $this->yes_no_options(),
                'std'         => '',
                'group'       => __('Advanced', 'asl_locator'),
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => __('Cluster Markers', 'asl_locator'),
                'param_name'  => 'cluster',
                'value'       => $this->inherit_option() + array(
                    __('Enabled', 'asl_locator')  => '1',
                    __('Disabled', 'asl_locator') => '0',
                    __('Grid', 'asl_locator')     => '2',
                ),
                'std'         => '',
                'group'       => __('Advanced', 'asl_locator'),
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => __('Load All Stores', 'asl_locator'),
                'param_name'  => 'load_all',
                'value'       => $this->inherit_option() + array(
                    __('Load all stores', 'asl_locator')        => '1',
                    __('Load stores in map bounds', 'asl_locator') => '0',
                    __('Reload stores when map moves', 'asl_locator') => '2',
                ),
                'std'         => '',
                'group'       => __('Advanced', 'asl_locator'),
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => __('Fit Bounds', 'asl_locator'),
                'param_name'  => 'fit_bound',
                'value'       => $this->yes_no_options(),
                'std'         => '',
                'group'       => __('Advanced', 'asl_locator'),
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => __('Scroll Wheel', 'asl_locator'),
                'param_name'  => 'scroll_wheel',
                'value'       => $this->yes_no_options(),
                'std'         => '',
                'group'       => __('Advanced', 'asl_locator'),
            ),
            array(
                'type'        => 'textfield',
                'heading'     => __('Dropdown Range', 'asl_locator'),
                'param_name'  => 'dropdown_range',
                'std'         => '',
                'description' => __('Example: 20,40,60,80,*100', 'asl_locator'),
                'group'       => __('Advanced', 'asl_locator'),
            ),
            array(
                'type'        => 'textfield',
                'heading'     => __('Map Language', 'asl_locator'),
                'param_name'  => 'map_language',
                'std'         => '',
                'description' => __('Optional Google Maps language code, such as en or ar.', 'asl_locator'),
                'group'       => __('Advanced', 'asl_locator'),
            ),
            array(
                'type'        => 'textfield',
                'heading'     => __('Map Region', 'asl_locator'),
                'param_name'  => 'map_region',
                'std'         => '',
                'description' => __('Optional Google Maps region code, such as US or AE.', 'asl_locator'),
                'group'       => __('Advanced', 'asl_locator'),
            ),
            array(
                'type'        => 'textfield',
                'heading'     => __('Country Restriction', 'asl_locator'),
                'param_name'  => 'country_restrict',
                'std'         => '',
                'description' => __('Optional comma-separated country codes.', 'asl_locator'),
                'group'       => __('Advanced', 'asl_locator'),
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => __('User Center', 'asl_locator'),
                'param_name'  => 'user_center',
                'value'       => $this->yes_no_options(),
                'std'         => '',
                'group'       => __('Advanced', 'asl_locator'),
            ),
        );
    }

    /**
     * Allowed shortcode attributes exposed by this WPBakery element.
     *
     * @since 4.8.21
     */
    private function get_shortcode_param_names()
    {
        return array_map(function ($param) {
            return $param['param_name'];
        }, $this->get_vc_params());
    }


   
    /**
     * Initialize the class and set its properties.
     *
     * @since      4.8.21
     */
    
    public function __construct()
    {

          // We safely integrate with VC 
        $this->integrate_with_vc();

        // Render shortcode hook
        add_shortcode('asl_store_locator', array($this, 'render_shortcode_store_locator'));
       
    }


    /**
    * [Lets call vc_map function to "register" our custom shortcode within Visual Composer interface.]
    * @since 4.8.21 
    */
    public function integrate_with_vc()
    {
        // Check if Visual Composer is installed
        if (!defined('WPB_VC_VERSION')) {
            // Display notice that Visual Compser is required
            add_action('admin_notices', array($this, 'show_vc_version_notice'));
            return;
        }

      
        // WPBackery Addon Fields
        vc_map(
            array(
                "name"              => __('Store Locator Widget', 'asl_locator'),
                "description"       => __("Display store locator widget", 'asl_locator'),
                "heading"           => __("Store Locator"),
                "class"             => "vc_admin_label",
                "icon"              => ASL_URL_PATH . 'admin/images/asl_grid.png',
                "base"              => 'asl_store_locator',
                "controls"          => "full",
                "category"          => __('content', 'asl_locator'),

                "params"      => $this->get_vc_params()
            )
        );


    }

    /*
    
    */
       
    /**
    * [Shortcode render vc_map]
    * @since 4.8.21 
    * @param [type] $atts                [Get filter data from vc_map]
    * @param [type] $content             [description]
    */
    public function render_shortcode_store_locator($atts, $content = null)
    {
        $shortcode_attr = array();

        foreach ($this->get_shortcode_param_names() as $param_name) {
            if (isset($atts[$param_name]) && $atts[$param_name] !== '') {
                $shortcode_attr[] = $param_name . '="' . esc_attr($atts[$param_name]) . '"';
            }
        }


        $shortcode_attr = implode(' ', $shortcode_attr);
        $shortcode = '[ASL_STORELOCATOR  '.$shortcode_attr.']';


        echo'<div class="elementor-shortcode asl-free-addon">';
        echo do_shortcode($shortcode);
        echo'</div>';

      
    }


    /**
    * [Show notice if your plugin is activated]
    * @since 4.8.21 
    */
    public function show_vc_version_notice()
    {
        $plugin_data = get_plugin_data(__FILE__);
        echo '
        <div class="updated">
          <p>' . sprintf(__('<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'vc_extend'), $plugin_data['Name']) . '</p>
        </div>';
    }
}
