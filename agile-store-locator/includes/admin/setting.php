<?php

namespace AgileStoreLocator\Admin;

if (!defined('ABSPATH')) {
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

class Setting extends Base
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * [backup_template Backup the Template into theme Root Directory]
     * @return [type] [description]
     */
    public function backup_template()
    {
        $template  = isset($_REQUEST['template']) ? sanitize_text_field($_REQUEST['template']) : null;
        $response  = \AgileStoreLocator\Helper::backup_template($template);

        return $this->send_response($response);
    }

    /**
     * [remove_template Remove the template file from the Theme Directory]
     * @return [type] [description]
     */
    public function remove_template()
    {
        $template  = isset($_REQUEST['template']) ? sanitize_text_field($_REQUEST['template']) : null;
        $response  = \AgileStoreLocator\Helper::remove_template($template);

        return $this->send_response($response);
    }

    /**
     * [expertise_level description]
     * @return [type] [description]
     */
    public function expertise_level()
    {
        $level_status = (isset($_REQUEST['status']) && $_REQUEST['status'] == '1') ? '1' : '0';

        //  Update the expertise level
        update_option('asl-expertise', $level_status);

        return $this->send_response(['success' => true, 'msg' => esc_attr__('Level has been changed.', 'asl_locator')]);
    }

    /**
     * [change_options Save the Settings in the Settings table]
     */
    public function change_options($json_return = false)
    {
        global $wpdb;
        $prefix = ASL_PREFIX;

        // Data
        $content = isset($_POST['content']) ? stripslashes_deep($_POST['content']) : null;
        $type    = isset($_POST['stype']) ? stripslashes_deep($_POST['stype']) : null;

        //  Response
        $response          = new \stdclass();
        $response->success = false;

        //  When type is hidden
        if (in_array($type, ['hidden', 'cache'])) {
            $c = $wpdb->get_results("SELECT count(*) AS 'count' FROM {$prefix}settings WHERE `type` = '{$type}'");

            $data_params = ['content' => json_encode($content), 'type' => $type];

            if ($c[0]->count >= 1) {
                $wpdb->update($prefix . 'settings', $data_params, ['type' => $type]);
            } else {
                $wpdb->insert($prefix . 'settings', $data_params);
            }

            $response->msg     = esc_attr__('Settings has been updated.', 'asl_locator');
            $response->success = true;
        }

        //  return as JSON
        if ($json_return) {
            return $response;
        }

        return $this->send_response($response);
    }

    /**
     * [save_setting save ASL Setting]
     * @return [type] [description]
     */
    public function save_setting()
    {
        global $wpdb;

        // Focused dashboard onboarding request: save only the browser Maps API key.
        if (isset($_POST['dashboard_google_maps_setup']) && '1' === sanitize_text_field(wp_unslash($_POST['dashboard_google_maps_setup']))) {
            return $this->save_dashboard_google_maps_key();
        }

        $response  = new \stdclass();

        //  Settings data
        $data_     = isset($_POST['data']) ? stripslashes_deep($_POST['data']) : [];
        $previous_api_key = array_key_exists('api_key', $data_)
            ? (string) $wpdb->get_var("SELECT `value` FROM " . ASL_PREFIX . "configs WHERE `key` = 'api_key'")
            : null;

        //  Custom Map Style
        $custom_map_style = isset($_POST['map_style']) ? wp_unslash($_POST['map_style']) : '';
        $custom_map_style = \AgileStoreLocator\Helper::sanitize_custom_map_style($custom_map_style, false);

        if ($custom_map_style === false) {
            $response->msg     = esc_attr__('Invalid custom map style. Please provide a valid JSON array.', 'asl_locator');
            $response->success = false;

            return $this->send_response($response);
        }

        //  Remove Script tag will be saved in wp_options
        $remove_script_tag = isset($data_['remove_maps_script']) ? $data_['remove_maps_script'] : '0';
        unset($data_['remove_maps_script']);

        //  Config keys
        $keys     =  array_keys($data_);

        // Hava a value?
        if (isset($data_['country_restrict']) && $data_['country_restrict']) {
            // Restrict the country validation
            $validation_result = $this->validate_country_restrictions($data_['country_restrict']);

            if (!$validation_result['valid']) {
                $response->msg = esc_attr__('Invalid value for country restriction. Only ISO 3166-1 alpha-2 country codes are supported.', 'asl_locator');
                return $this->send_response($response);
            }

            $data_['country_restrict'] = $validation_result['country_restrict'];
        }

        if (isset($data_['store_page_address_format'])) {
            $data_['store_page_address_format'] = sanitize_text_field($data_['store_page_address_format']);
        }

        //  Loop over the setting items
        foreach ($keys as $key) {
            $wpdb->update(
                ASL_PREFIX . 'configs',
                ['value' => $data_[$key]],
                ['key'   => $key]
            );
        }

        // A changed browser key must pass validation again before Step 1 is complete.
        if (null !== $previous_api_key && $previous_api_key !== (string) $data_['api_key']) {
            Dashboard::set_onboarding_step('connect_google_maps', false);
        }

        //  Custom Map Style
        \AgileStoreLocator\Helper::set_setting($custom_map_style, 'map_style', 'map_style');

        $custom_slug_fields = $_POST['slug_attr_ddl'];

        //  Slug Attributes
        \AgileStoreLocator\Helper::set_setting(stripslashes($custom_slug_fields), 'slug_attr_ddl');

        // Store form fields ordering/visibility
        $store_form_fields = isset($_POST['store_form_fields']) ? wp_unslash($_POST['store_form_fields']) : '';

        if ($store_form_fields !== '') {
            $store_form_fields_decoded = json_decode($store_form_fields, true);

            if (is_array($store_form_fields_decoded)) {
                \AgileStoreLocator\Helper::set_setting(wp_json_encode($store_form_fields_decoded), 'store_form_fields');
            }
        }

        // Store form dropdown ordering/visibility
        $store_form_controls = isset($_POST['store_form_controls']) ? wp_unslash($_POST['store_form_controls']) : '';

        if ($store_form_controls !== '') {
            $store_form_controls_decoded = json_decode($store_form_controls, true);

            if (is_array($store_form_controls_decoded)) {
                \AgileStoreLocator\Helper::set_setting(wp_json_encode($store_form_controls_decoded), 'store_form_controls');
            }
        }

        // Bulk edit fields visibility
        $bulk_edit_fields = isset($_POST['bulk_edit_fields']) ? wp_unslash($_POST['bulk_edit_fields']) : '';

        if ($bulk_edit_fields !== '') {
            $bulk_edit_fields_decoded = json_decode($bulk_edit_fields, true);

            if (is_array($bulk_edit_fields_decoded)) {
                \AgileStoreLocator\Helper::set_setting(wp_json_encode($bulk_edit_fields_decoded), 'bulk_edit_fields');
            }
        }

        update_option('asl-remove_maps_script', $remove_script_tag);

        $response->msg     = esc_attr__('Setting has been updated successfully.', 'asl_locator');
        $response->success = true;

        //  Valid the Default Coordinates
        $is_valid  = \AgileStoreLocator\Helper::validate_coordinate($data_['default_lat'], $data_['default_lng']);

        //  is invalid?
        if (!$is_valid) {
            $response->msg .= '<br>' . esc_attr__('Error! Default Lat & Lng are invalid values, please try to swap them.', 'asl_locator');
            $response->success  = false;
        }

        return $this->send_response($response);
    }

    /**
     * Save the browser Google Maps API key from the focused dashboard setup.
     * Validation happens in the browser so referrer-restricted keys are tested
     * in the context where the Maps JavaScript API will actually run.
     */
    private function save_dashboard_google_maps_key()
    {
        global $wpdb;

        $api_key = isset($_POST['data']['api_key'])
            ? sanitize_text_field(wp_unslash($_POST['data']['api_key']))
            : '';

        if ('' === $api_key) {
            return $this->send_response([
                'success' => false,
                'error'   => esc_attr__('Please enter a Google Maps API key.', 'asl_locator'),
            ]);
        }

        $updated = $wpdb->update(
            ASL_PREFIX . 'configs',
            ['value' => $api_key],
            ['key' => 'api_key']
        );

        if (false === $updated) {
            return $this->send_response([
                'success' => false,
                'error'   => esc_attr__('Unable to save the Google Maps API key.', 'asl_locator'),
            ]);
        }

        // Saving a key never proves it works. Validation must set this true.
        Dashboard::set_onboarding_step('connect_google_maps', false);

        return $this->send_response([
            'success' => true,
            'msg'     => esc_attr__('Google Maps API key saved. Verifying configuration…', 'asl_locator'),
        ]);
    }

    /**
     * [validate_country_restrictions Validate the country restriction]
     * @param  [type] $country_restrict [description]
     * @return [type]                   [description]
     */
    private function validate_country_restrictions($country_restrict)
    {
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

        $countries             = explode(',', $country_restrict);
        $valid                 = true;
        $countries_to_restrict = [];

        foreach ($countries as $country) {
            $country                 = strtoupper(trim($country));
            $countries_to_restrict[] = $country;
            if (!in_array($country, $valid_countries)) {
                $valid = false;
                break;
            }
        }

        return [
            'valid'            => $valid,
            'country_restrict' => implode(',', $countries_to_restrict)
        ];
    }

    /**
     * [manage_cache Refresh the JSON]
     * @return [type] [description]
     */
    public function manage_cache()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $cache_status = (isset($_REQUEST['status']) && $_REQUEST['status'] == '1') ? '1' : '0';
        $cache_lang   = (isset($_REQUEST['asl-lang'])) ? sanitize_text_field($_REQUEST['asl-lang']) : null;

        //  Todo, Make sure the folder exist?
        if (!file_exists(ASL_UPLOAD_DIR)) {
            mkdir(ASL_UPLOAD_DIR, 0775, true);
        }

        if (!$cache_lang) {
            $response->error = esc_attr__('Error! Lang is not defined.', 'asl_locator');
            ;
        }

        //  en_US is default
        if ($cache_lang == 'en_US') {
            $cache_lang = '';
        }

        //  JSON file
        $json_file = 'locator-data' . (($cache_lang) ? '-' . $cache_lang : '') . '.json';

        //  Generate the JSON file when enabled
        if ($cache_status == '1') {
            //  Generate the Output
            $public_request = new \AgileStoreLocator\Frontend\Request();
            $output_result  = $public_request->load_stores(true, $cache_lang);

            //  Save the output
            $response->output   = file_put_contents(ASL_UPLOAD_DIR . $json_file, json_encode($output_result));

            //  When fails
            if (!$response->output) {
                $response->path   = ASL_UPLOAD_DIR . $json_file;
            }

            $response->msg      = esc_attr__('Cache JSON has been generated successfully for language ' . $cache_lang, 'asl_locator');
        } else {
            $response->msg      = esc_attr__('Cache JSON is disabled for language ' . $cache_lang, 'asl_locator');
        }

        //  Save the cache settings
        $this->change_options(true);

        //  Show as success
        $response->success  = true;

        return $this->send_response($response);
    }


    /*[load_custom_template Load ASL Custom Template]
     * @return [type] [description]
     */
    public function load_custom_template()
    {
        global $wpdb;

        $response          = new \stdClass();
        $response->success = false;

        // Deep sanitize all POST data
        $data_    = stripslashes_deep($_POST);
        $template = isset($data_['template']) ? sanitize_key($data_['template']) : '';
        $section  = isset($data_['section']) ? sanitize_key($data_['section']) : '';

        // Validate inputs
        if (!$template || !$section) {
            $response->error = esc_html__('Invalid request parameters.', 'asl_locator');
            return $this->send_response($response);
        }

        if (!$this->is_allowed_customizer_section($template, $section)) {
            $response->error = esc_html__('Invalid template section.', 'asl_locator');
            return $this->send_response($response);
        }

        $html = '';

        // CASE 1: Template is NOT cards-templates
        if ($template !== 'cards-templates') {
            // Use prepared statement to count matching rows
            $count = $wpdb->get_var(
                $wpdb->prepare(
                    'SELECT COUNT(*) FROM ' . ASL_PREFIX . 'settings WHERE `name` = %s AND `type` = %s',
                    $template,
                    $section
                )
            );

            if ((int)$count >= 1) {
                // Fetch saved content
                $result = $wpdb->get_var(
                    $wpdb->prepare(
                        'SELECT `content` FROM ' . ASL_PREFIX . 'settings WHERE `name` = %s AND `type` = %s',
                        $template,
                        $section
                    )
                );
                if ($result !== null) {
                    $html = $result;
                }
            } else {
                $view_file_path = $this->get_custom_template_file_path($template, $section, false);

                if ($view_file_path) {
                    $html = file_get_contents($view_file_path);
                }
                else {
                    $response->error = esc_html__('Invalid template path.', 'asl_locator');
                    return $this->send_response($response);
                }
            }
        }


        if (!empty($html)) {
            $response->html    = $html; // allow raw HTML from trusted editor
            $response->msg     = esc_html__('HTML added in TextEditor', 'asl_locator');
            $response->success = true;
        }

        return $this->send_response($response);
    }

    /**
     * [save_custom_template Load ASL Custom Template]
     * @return [type] [description]
     */
    public function save_custom_template()
    {
        global $wpdb;

        $response          = new \stdClass();
        $response->success = false;

        // Strip slashes and sanitize inputs
        $data_    = stripslashes_deep($_POST);
        $template = isset($data_['template']) ? sanitize_key($data_['template']) : '';
        $section  = isset($data_['section']) ? sanitize_key($data_['section']) : '';
        $html     = isset($data_['html']) ? trim($data_['html']) : '';

        // Basic validation
        if (empty($template) || empty($section) || empty($html)) {
            $response->error = esc_html__('Missing required data.', 'asl_locator');
            return $this->send_response($response);
        }

        if (!$this->is_allowed_customizer_section($template, $section)) {
            $response->error = esc_html__('Invalid template section.', 'asl_locator');
            return $this->send_response($response);
        }

        // Restrict PHP file saving to admins only
        if ($template === 'cards-templates' && !current_user_can('manage_options')) {
            $response->error = esc_html__('Only administrators can update this template.', 'asl_locator');
            return $this->send_response($response);
        }

        // CASE 1: Save to database
        if ($template !== 'cards-templates') {
            $count = $wpdb->get_var(
                $wpdb->prepare(
                    'SELECT COUNT(*) FROM ' . ASL_PREFIX . 'settings WHERE `name` = %s AND `type` = %s',
                    $template,
                    $section
                )
            );

            $data_params = [
                'name'    => $template,
                'type'    => $section,
                'content' => ($html) // Only allow safe HTML
            ];

            if ((int)$count >= 1) {
                $wpdb->update(ASL_PREFIX . 'settings', $data_params, ['name' => $template, 'type' => $section]);
            } else {
                $wpdb->insert(ASL_PREFIX . 'settings', $data_params);
            }

            $response->msg     = esc_html__('Template Updated', 'asl_locator');
            $response->success = true;
        }

        // CASE 2: Save as PHP file to theme
        else {
            $filename  = sanitize_file_name($section) . '.php';
            $file_path = STYLESHEETPATH . '/' . $filename;

            // Secure: prevent directory traversal
            $real_theme_path = realpath(STYLESHEETPATH);
            $real_file_path  = realpath(dirname($file_path)) . '/' . $filename;

            if (!$real_file_path || !str_starts_with($real_file_path, $real_theme_path)) {
                $response->error = esc_html__('Invalid template path.', 'asl_locator');
                return $this->send_response($response);
            }

            // Optionally: validate against dangerous PHP functions
            if (preg_match('/\b(eval|exec|shell_exec|system|passthru|base64_decode)\b/i', $html)) {
                $response->error = esc_html__('Disallowed PHP functions detected.', 'asl_locator');
                return $this->send_response($response);
            }

            // Write file
            file_put_contents($real_file_path, $html);

            $response->msg     = esc_html__('Cards Template Updated', 'asl_locator');
            $response->success = true;
        }

        return $this->send_response($response);
    }

    /**
     * [reset_custom_template Load ASL Custom Template]
     * @return [type] [description]
     */
    public function reset_custom_template()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $data_ = stripslashes_deep($_POST);
        $template = isset($data_['template']) ? sanitize_key($data_['template']) : '';
        $section  = isset($data_['section']) ? sanitize_key($data_['section']) : '';

        if (!$template || !$section || !$this->is_allowed_customizer_section($template, $section)) {
            $response->error = esc_html__('Invalid template section.', 'asl_locator');
            return $this->send_response($response);
        }

        $view_file_path = $this->get_custom_template_file_path($template, $section, false);

        if (!$view_file_path) {
            $response->error = esc_html__('Invalid template path.', 'asl_locator');
            return $this->send_response($response);
        }

        // include simple products HTML
        $html = file_get_contents($view_file_path);

        $response->html    = $html;
        $response->msg     = esc_attr__('Default template is loaded', 'asl_locator');
        $response->success = true;

        return $this->send_response($response);
    }

    /**
     * Confirm the requested template section exists in the customizer registry.
     *
     * @param string $template Template key.
     * @param string $section Section key.
     * @return bool
     */
    private function is_allowed_customizer_section($template, $section)
    {
        $templates = \AgileStoreLocator\Helper::customizer_tmpls();

        if (!isset($templates[$template]) || empty($templates[$template]['options'])) {
            return false;
        }

        foreach ($templates[$template]['options'] as $option) {
            if (isset($option['value']) && $option['value'] === $section) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolve a customizer template path after section whitelist validation.
     *
     * @param string $template Template key.
     * @param string $section Section key.
     * @param bool $allow_theme_override Whether theme card template overrides are allowed.
     * @return string|false
     */
    private function get_custom_template_file_path($template, $section, $allow_theme_override = false)
    {
        if (!$this->is_allowed_customizer_section($template, $section)) {
            return false;
        }

        $view_file_path = \AgileStoreLocator\Helper::get_customizer_file_path($template, $section);
        $allowed_bases  = [ASL_PLUGIN_PATH . 'public/views'];

        if (defined('WP_PLUGIN_DIR')) {
            $allowed_bases[] = WP_PLUGIN_DIR;
        }

        $allowed_bases = apply_filters('asl_customizer_allowed_view_base_paths', $allowed_bases, $template, $section, $view_file_path);

        return $this->is_path_within_allowed_bases($view_file_path, $allowed_bases) ? realpath($view_file_path) : false;
    }

    /**
     * Check a file path is inside one of the allowed base directories.
     *
     * @param string $path File path.
     * @param array $allowed_bases Base directories.
     * @return bool
     */
    private function is_path_within_allowed_bases($path, $allowed_bases)
    {
        $real_path = realpath($path);

        if (!$real_path || !is_file($real_path)) {
            return false;
        }

        foreach ($allowed_bases as $base) {
            $real_base = realpath($base);

            if ($real_base && strpos($real_path, rtrim($real_base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * [add_cards_shortcode_presets Save Shortcode Presets]
     * @return [type] [description]
     */
    public function cards_shortcode_presets()
    {
        $response          = new \stdclass();
        $response->success = false;
        $db_action         = $_POST['db_action'];
        $shortcode_preset  = $_POST['shortcode'];

        // Retrive Shortcodes from DB
        $cards_shortcode_presets = \AgileStoreLocator\Helper::get_setting('cards_shortcode_presets', 'cards_shortcode_presets');
        $cards_shortcode_presets = $cards_shortcode_presets ? maybe_unserialize($cards_shortcode_presets) : [];

        $target_key      = array_search($shortcode_preset, $cards_shortcode_presets);
        $shortcode_exist = $target_key !== false ? true : false;

        switch ($db_action) {
            case 'add':
                $cards_shortcode_presets[] = $shortcode_preset;
                $succss_msg                = 'Shortcode Presets have been Successfully Saved!';
                break;

            case 'edit':
                if ($shortcode_exist) {
                    $cards_shortcode_presets[$target_key] = $_POST['updated_shortcode'];
                    $succss_msg                           = 'Shortcode Presets have been Successfully Updated!';
                } else {
                    $cards_shortcode_presets[] = $_POST['updated_shortcode'];
                    $succss_msg                = 'Shortcode have not been found to edit!, added instead';
                    // return $response;
                }
                break;

            case 'delete':
                if ($shortcode_exist) {
                    unset($cards_shortcode_presets[$target_key]);
                    $succss_msg = 'Shortcode Presets have been Successfully Deleted!';
                } else {
                    $succss_msg    = 'Shortcode have not been found!';
                    $response->msg = esc_attr__($succss_msg, 'asl_locator');
                    return $response;
                }
                break;
        }

        // Remove Duplicate Items from Array
        $cards_shortcode_presets = array_unique($cards_shortcode_presets);
        // Reset Index Keys
        $cards_shortcode_presets = array_values($cards_shortcode_presets);

        $db_response = \AgileStoreLocator\Helper::set_setting(maybe_serialize($cards_shortcode_presets), 'cards_shortcode_presets', 'cards_shortcode_presets');

        if ($db_response) {
            $response->data    = $cards_shortcode_presets[$target_key];
            $response->success = true;
            $response->msg     = esc_attr__($succss_msg, 'asl_locator');
        }

        return $response;
    }

    /**
     * [load_ui_settings Load ASL Custom Template]
     * @return [type] [description]
     */
    public function load_ui_settings()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $template = isset($_POST['template']) ? sanitize_text_field($_POST['template']) : '';

        $colors   = [
            'template-0'  => [
                'primary'           => 'clr-primary',
                'header'            => 'light-90',
                'header-color'      => '',
                'infobox-color'     => '',
                'infobox-bg'        => '',
                'infobox-a'         => 'clr-copy',
                'search-text'         => 'clr-copy',
                'search-btn-color'  => '',
                'search-btn-bg'     => 'clr-copy',
                'action-btn-color'  => '',
                'action-btn-bg'     => 'clr-copy',
                'color'             => '',
                'list-bg'           => '',
                'list-title'        => '',
                'list-sub-title'    => '',
                'highlighted'       => ''
            ],
            'template-wc'  => [
                'primary'                => 'clr-primary',
                'button-color'           => '',
                'button-background'      => 'clr-copy',
                'label-color'            => '',
                'control-color'          => '',
                'control-background'     => '',
                'control-border-color'   => '',
                'input-color'            => '',
                'input-background'       => ''
            ],
        ];

        $white                  = '#FFFFFF';
        $black                  = '#000000';
        $light_gray             = '#eeeeee';


        $tmpl_0_primary         = '#cb2800';
        $tmpl_0_title_color     = '#32373c';
        $tmpl_0_sub_title_color = '#6a6a6a';
        $tmpl_0_list_color      = '#555d66';
        $tmpl_0_header_bg       = '#F7F7F7';
        $tmpl_0_header_color    = '#32373c';
        $tmpl_0_highlighted     = '#F7F7F7';


        //  the default colors that will load with the customizer
        $default_colors = [
            'template-0'  => [
                'primary'                => $tmpl_0_primary,
                'header'                 => $tmpl_0_header_bg,
                'header-color'           => $tmpl_0_header_color,
                'infobox-color'          => $tmpl_0_list_color,
                'infobox-bg'             => $white,
                'infobox-a'              => $tmpl_0_primary,
                'search-text'            => $tmpl_0_primary,
                'search-btn-color'       => $white,
                'search-btn-bg'          => $tmpl_0_primary,
                'action-btn-color'       => $white,
                'action-btn-bg'          => $tmpl_0_primary,
                'color'                  => $tmpl_0_list_color,
                'list-bg'                => $white,
                'list-title'             => $tmpl_0_title_color,
                'list-sub-title'         => $tmpl_0_sub_title_color,
                'highlighted'            => $tmpl_0_highlighted,
                'highlighted-list-color' => $tmpl_0_primary
            ],
            'template-wc'  => [
                'primary'                => $tmpl_0_primary,
                'button-color'           => $white,
                'button-background'      => $tmpl_0_primary,
                'label-color'            => '#010a10',
                'control-color'          => '#010a10',
                'control-background'     => $white,
                'control-border-color'   => '#dee2e6',
                'input-color'            => $black,
                'input-background'       => $white
            ]
        ];

        $default_fonts  = [
            'template-0'  => [
                'title-size'    => 16,
                'font-size'     => 13,
                'btn-size'      => 14,
                'label-size'    => 16,
                'input-size'    => 16
            ],
            'template-wc'  => [
                'font-size'         => 13,
                'font-small-size'   => 10,
                'title-size'        => 16,
                'btn-size'          => 13
            ]
        ];

        $font_labels = [
            'heading-size'      => 'Heading Font',
            'heading-sub-size'  => 'Heading Para Font',
            'label-size'        => 'Label Font',
            'input-size'        => 'Input Font',
            'tag-size'          => 'Category Tags Font',
            'small-size'        => 'Description Font',
            'font-size'         => 'Content Font',
            'title-size'        => 'Title Font',
            'sub-title-size'    => 'Sub-title Font',
            'list-title-size'   => 'List Title Font',
            'btn-size'                => 'Button Font',
            'state-btn-size'          => 'State Btn Font',
            'state-label-size'        => 'State Label Font',
            'list-order-btn-size'     => 'List Order Btn Font',
            'list-font-size'          => 'List Text Font',
            'action-btn-size'         => 'Action Btn Font'

        ];

        $html     = '';
        $fields   = '';

        //  Only get the array of active default color
        $default_colors  = $default_colors[$template];
        $default_fonts   = $default_fonts[$template];

        $fields_settings = \AgileStoreLocator\Helper::get_setting('ui-template', $template);

        if ($fields_settings) {
            $fields = json_decode($fields_settings);
        }

        $ui_settings = apply_filters(
            'asl_ui_settings',
            [
                'colors'         => isset($colors[$template]) ? $colors[$template] : [],
                'default_colors' => $default_colors,
                'default_fonts'  => $default_fonts,
                'font_labels'    => $font_labels,
                'fields'         => $fields,
            ],
            $template
        );

        $colors[$template] = isset($ui_settings['colors']) && is_array($ui_settings['colors']) ? $ui_settings['colors'] : [];
        $default_colors    = isset($ui_settings['default_colors']) && is_array($ui_settings['default_colors']) ? $ui_settings['default_colors'] : [];
        $default_fonts     = isset($ui_settings['default_fonts']) && is_array($ui_settings['default_fonts']) ? $ui_settings['default_fonts'] : [];
        $font_labels       = isset($ui_settings['font_labels']) && is_array($ui_settings['font_labels']) ? $ui_settings['font_labels'] : [];
        $fields            = isset($ui_settings['fields']) ? $ui_settings['fields'] : $fields;

        //  Start Stream
        ob_start();

        // include ui customizer fields products HTML
        include ASL_PLUGIN_PATH . 'admin/partials/ui-customizer-fields.php';

        $html = ob_get_contents();

        //  Clean it
        ob_end_clean();

        $response->html     = $html;
        $response->msg      = esc_attr__('Template UI settings updated', 'asl_locator');
        $response->success  = true;

        return $this->send_response($response);
    }

    /**
     * [sl_theme_ui_save Save ASL UI Settings]
     * @return [type] [description]
     */
    public function sl_theme_ui_save()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $data_    = stripslashes_deep($_POST['sl_formData']);
        $template = sanitize_text_field($_POST['sl_template']);

        $data     = json_encode($data_);

        $saved = \AgileStoreLocator\Helper::set_setting($data, 'ui-template', $template);

        if (false === $saved) {
            $response->error = esc_attr__('Unable to save the template settings.', 'asl_locator');

            return $this->send_response($response);
        }

        // Completing the Color Customizer save finishes the final onboarding step.
        Dashboard::set_onboarding_step('customize_locator', true);

        $response->msg     = esc_attr__('Template updated', 'asl_locator');
        $response->success = true;

        return $this->send_response($response);
    }

    /**
     * [reset_ui_template Remove saved UI template settings]
     *
     * Deletes the stored "ui-template" record for the selected template so
     * defaults are applied next time it is loaded.
     */
    public function reset_ui_template()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $template = isset($_POST['template']) ? sanitize_text_field($_POST['template']) : '';

        if (empty($template)) {
            $response->error = esc_html__('Missing template identifier.', 'asl_locator');

            return $this->send_response($response);
        }

        $deleted = $wpdb->delete(
            ASL_PREFIX . 'settings',
            [
                'type' => 'ui-template',
                'name' => $template,
            ],
            ['%s', '%s']
        );

        if ($deleted === false) {
            $response->error = esc_html__('Unable to reset the template. Please try again.', 'asl_locator');

            return $this->send_response($response);
        }

        $response->success  = true;
        $response->template = $template;
        $response->msg      = esc_attr__('Template settings have been reset.', 'asl_locator');

        return $this->send_response($response);
    }

    /**
     * [save_custom_fields Save Custom Fields AJAX]
     * @return [type] [description]
     */
    public function save_custom_fields()
    {
        global $wpdb;
        $prefix = ASL_PREFIX;

        $response          = new \stdclass();
        $response->success = false;

        $fields = isset($_POST['fields']) ? ($_POST['fields']) : [];

        //  Filter the JSON for XSS
        $filter_fields = [];

        foreach ($fields as $field_key => $field) {
            $field_key = strip_tags($field_key);

            $field['type']  = strip_tags(sanitize_text_field($field['type']));
            $field['name']  = strip_tags(sanitize_text_field($field['name']));
            $field['label'] = strip_tags(sanitize_text_field($field['label']));
            $field['section'] = isset($field['section']) && in_array($field['section'], ['address', 'other'], true)
                ? $field['section']
                : 'other';

            $filter_fields[$field_key] = $field;
        }

        $c = $wpdb->get_results("SELECT count(*) AS 'count' FROM {$prefix}settings WHERE `type` = 'fields'");

        $data_params = ['content' => json_encode($filter_fields), 'type' => 'fields'];

        if ($c[0]->count >= 1) {
            $wpdb->update($prefix . 'settings', $data_params, ['type' => 'fields']);
        } else {
            $wpdb->insert($prefix . 'settings', $data_params);
        }

        /*$wpdb->show_errors = true;
        $response->error = $wpdb->print_error();
        $response->error1 = $wpdb->last_error;*/

        $response->msg     = esc_attr__('Fields has been updated successfully.', 'asl_locator');
        $response->success = true;

        return $this->send_response($response);
    }

    /**
     * \AgileStoreLocator\Admin\Setting::reset_all_slugs();
     * [Reset all store slug AJAX]
     * @return [type] [description]
     */
    public function reset_all_slugs()
    {
        $response  = new \stdclass();

        $counter = \AgileStoreLocator\Schema\Slug::regenerate_all_slugs();

        $response->msg     = esc_attr__(" $counter Slugs has been updated.", 'asl_locator');
        $response->success = ($counter) ? true : false;

        return $this->send_response($response);
    }

    /**
     * [import_configs Import the Configs]
     * @return [type] [description]
     */
    public function import_configs()
    {
        $response          = new \stdclass();
        $response->title   = esc_attr__('Configuration Import', 'asl_locator');

        // Must be an administrator
        if (current_user_can('administrator')) {
            $jsonText          = isset($_POST['configs']) ? stripslashes_deep($_POST['configs']) : null;
            $import_results    = ($jsonText) ? (\AgileStoreLocator\Model\Config::import_configuration($jsonText)) : false;

            //  Has imported or not?
            if ($import_results > 0) {
                $response->message = esc_attr__('Config has been imported successfully', 'asl_locator');
                $response->success = true;
            } else {
                $response->message = esc_attr__('Failed to import configuration, contact support for help.', 'asl_locator');
            }
        } else {
            $response->message = esc_attr__('Administrator permissions are required.', 'asl_locator');
        }

        return $this->send_response($response);
    }

    /**
     * [export_configs Export the Configs]
     * @return [type] [description]
     */
    public function export_configs()
    {
        $response  = new \stdclass();

        $response->configs = \AgileStoreLocator\Model\Config::export_config();

        $response->message              = esc_attr__('Config exported successfully', 'asl_locator');
        $response->copy_message         = esc_attr__('Copied successfully', 'asl_locator');
        $response->export_text_content  = esc_attr__('Warning! Export includes all configuration including API keys, labels, customizations and maps related settings.', 'asl_locator');
        $response->success              = true;

        return $this->send_response($response);
    }
}
