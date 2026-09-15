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

    /** Category choices for the same per-page restriction used by the shortcode modal. */
    private function get_category_options()
    {
        $options = array();
        $lang = Helper::get_configs('locale') ? get_locale() : '';
        if ('en_US' === $lang) {
            $lang = '';
        }
        foreach ((array) Category::get_categories($lang) as $category) {
            $options[sprintf('%s (#%d)', $category->category_name, $category->id)] = (string) $category->id;
        }
        return $options;
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
                ),
                'std'         => '',
                'description' => __('Template 0 is available in Free. Additional templates are available in Pro.', 'asl_locator'),
                'group'       => __('Appearance & Search', 'asl_locator'),
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => __('Search Mode', 'asl_locator'),
                'param_name'  => 'search_mode',
                'value'       => $this->inherit_option() + array(
                    __('Address — Automatic', 'asl_locator') => 'automatic',
                    __('Address — Google Places', 'asl_locator') => 'google_new',
                    __('Address — Google Places (Legacy)', 'asl_locator') => 'google_legacy',
                    __('Address — Nominatim', 'asl_locator') => 'nominatim',
                    __('Address — Geoapify', 'asl_locator') => 'geoapify',
                    __('Address — Mapbox', 'asl_locator') => 'mapbox',
                    __('Address — Search on Enter', 'asl_locator') => 'geocode_enter',
                    __('Address search disabled', 'asl_locator') => 'disabled',
                ),
                'std'         => '',
                'description' => __('Override address search for this locator. Database search modes are available in Pro.', 'asl_locator'),
                'group'       => __('Appearance & Search', 'asl_locator'),
            ),
            array(
                'type'       => 'hidden',
                'param_name' => 'search_type',
                'std'        => '',
                'group'      => __('Appearance & Search', 'asl_locator'),
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
                'group'       => __('Location & Display', 'asl_locator'),
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
                'group'       => __('Location & Display', 'asl_locator'),
            ),
            array(
                'type'        => 'checkbox',
                'heading'     => __('Restrict to Categories', 'asl_locator'),
                'param_name'  => 'category',
                'value'       => $this->get_category_options(),
                'std'         => '',
                'settings'    => array('direction' => 'vertical'),
                'description' => __('Select one or more categories. Leave empty to show all stores.', 'asl_locator'),
                'group'       => __('Appearance & Search', 'asl_locator'),
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
                'group'       => __('Location & Display', 'asl_locator'),
            ),
            array(
                'type'       => 'dropdown',
                'heading'    => __('Geo-Location Dialog', 'asl_locator'),
                'param_name' => 'prompt_location',
                'value'      => $this->inherit_option() + array(
                    __('Disable', 'asl_locator') => '0',
                    __('Geo-location Modal', 'asl_locator') => '1',
                    __('Type your Location Modal', 'asl_locator') => '2',
                    __('Geolocation On Load', 'asl_locator') => '3',
                    __('GeoJS IP Service', 'asl_locator') => '4',
                ),
                'std'        => '',
                'group'      => __('Location & Display', 'asl_locator'),
            ),
            array(
                'type'       => 'dropdown',
                'heading'    => __('Time Format', 'asl_locator'),
                'param_name' => 'time_format',
                'value'      => $this->inherit_option() + array(
                    __('12 Hours', 'asl_locator') => '0',
                    __('24 Hours', 'asl_locator') => '1',
                ),
                'std'        => '',
                'group'      => __('Location & Display', 'asl_locator'),
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
        $atts = is_array($atts) ? $atts : array();
        $shortcode_attr = array();

        foreach ($this->get_shortcode_param_names() as $param_name) {
            if ('template' === $param_name && isset($atts[$param_name]) && '0' !== (string) $atts[$param_name]) {
                continue;
            }
            if (in_array($param_name, array('search_mode', 'search_type', 'category'), true)) {
                continue;
            }
            if (isset($atts[$param_name]) && is_scalar($atts[$param_name]) && $atts[$param_name] !== '') {
                $shortcode_attr[] = $param_name . '="' . esc_attr($atts[$param_name]) . '"';
            }
        }

        $search_modes = array(
            'automatic' => array('automatic', '4'), 'google_new' => array('google', '4'),
            'google_legacy' => array('google', '0'), 'nominatim' => array('nominatim', '4'),
            'geoapify' => array('geoapify', '4'), 'mapbox' => array('mapbox', '4'),
            'geocode_enter' => array('google', '3'), 'disabled' => array('disabled', '0'),
        );
        $mode = isset($atts['search_mode']) ? (string) $atts['search_mode'] : '';
        if (isset($search_modes[$mode])) {
            $shortcode_attr[] = 'search_provider="' . $search_modes[$mode][0] . '"';
            $shortcode_attr[] = 'search_type="' . $search_modes[$mode][1] . '"';
        } elseif (isset($atts['search_type']) && in_array((string) $atts['search_type'], array('0', '3', '4'), true)) {
            $shortcode_attr[] = 'search_type="' . $atts['search_type'] . '"';
        }

        if (isset($atts['category']) && is_scalar($atts['category'])) {
            $categories = array_filter(explode(',', (string) $atts['category']), 'ctype_digit');
            if ($categories) {
                $shortcode_attr[] = 'category="' . implode(',', array_unique($categories)) . '"';
            }
        }

        $shortcode = '[ASL_STORELOCATOR' . ($shortcode_attr ? ' ' . implode(' ', $shortcode_attr) : '') . ']';
        return '<div class="elementor-shortcode asl-free-addon">' . do_shortcode($shortcode) . '</div>';
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
