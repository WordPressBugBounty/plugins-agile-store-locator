<?php

namespace AgileStoreLocator\Frontend;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Receives and stores frontend analytics events.
 */
class Analytics
{
    const SCHEMA_VERSION = '2.0';

    /**
     * Add or update the analytics table without requiring plugin reactivation.
     */
    public static function maybe_upgrade_schema()
    {
        if (get_option('asl_analytics_schema_version') === self::SCHEMA_VERSION) {
            return;
        }

        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $table = ASL_PREFIX . 'stores_view';
        $sql   = "CREATE TABLE {$table} (
            id int unsigned NOT NULL AUTO_INCREMENT,
            store_id int DEFAULT NULL,
            search_str varchar(255) DEFAULT NULL,
            place_id varchar(255) DEFAULT NULL,
            search_source varchar(30) DEFAULT NULL,
            search_kind varchar(40) DEFAULT NULL,
            search_key varchar(191) DEFAULT NULL,
            country_code varchar(6) DEFAULT NULL,
            latitude decimal(10,7) DEFAULT NULL,
            longitude decimal(10,7) DEFAULT NULL,
            result_count int unsigned DEFAULT NULL,
            visitor_hash char(64) DEFAULT NULL,
            is_search tinyint DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            created_on timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY created_on (created_on),
            KEY event_date (is_search, created_on),
            KEY store_date (store_id, created_on),
            KEY search_date (search_key, created_on),
            KEY visitor_hash (visitor_hash)
        ) DEFAULT CHARSET=UTF8MB4;";

        dbDelta($sql);
        update_option('asl_analytics_schema_version', self::SCHEMA_VERSION, false);
    }

    /**
     * Capture an AJAX analytics event.
     */
    public function capture()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'asl_remote_nonce')) {
            wp_send_json_error(['message' => __('Invalid analytics request.', 'asl_locator')], 403);
        }

        self::maybe_upgrade_schema();

        $is_search = isset($_POST['is_search']) && '1' === (string) $_POST['is_search'];
        $visitor   = isset($_POST['visitor_id']) ? sanitize_text_field(wp_unslash($_POST['visitor_id'])) : '';
        $ip        = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
        $agent     = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';

        $visitor_seed = $visitor ?: $ip . '|' . $agent;
        $visitor_hash = hash_hmac('sha256', substr($visitor_seed, 0, 512), wp_salt('nonce'));

        if ($is_search) {
            $this->capture_search($visitor_hash, substr($ip, 0, 45));
        } else {
            $this->capture_store_view($visitor_hash, substr($ip, 0, 45));
        }
    }

    /**
     * Store a search event.
     */
    private function capture_search($visitor_hash, $ip_address)
    {
        global $wpdb;

        $table         = ASL_PREFIX . 'stores_view';
        $search_str    = isset($_POST['search_str']) ? sanitize_text_field(wp_unslash($_POST['search_str'])) : '';
        $place_id      = isset($_POST['place_id']) ? sanitize_text_field(wp_unslash($_POST['place_id'])) : '';
        $search_source = isset($_POST['search_source']) ? sanitize_key(wp_unslash($_POST['search_source'])) : 'legacy';
        $search_kind   = isset($_POST['search_kind']) ? sanitize_key(wp_unslash($_POST['search_kind'])) : 'place';
        $search_key    = isset($_POST['search_key']) ? sanitize_text_field(wp_unslash($_POST['search_key'])) : '';
        $country_code  = isset($_POST['country_code']) ? strtoupper(sanitize_text_field(wp_unslash($_POST['country_code']))) : '';
        $latitude      = $this->coordinate('latitude', -90, 90);
        $longitude     = $this->coordinate('longitude', -180, 180);
        $result_count  = isset($_POST['result_count']) ? max(0, absint($_POST['result_count'])) : null;

        if (!$search_str) {
            wp_send_json_error(['message' => __('A search value is required.', 'asl_locator')], 400);
        }

        if (!$search_key) {
            $search_key = $place_id ? 'google:' . $place_id : $search_source . ':' . $search_kind . ':' . sha1(strtolower($search_str));
        }

        $search_key   = substr($search_key, 0, 191);
        $country_code = preg_match('/^[A-Z]{2,6}$/', $country_code) ? $country_code : '';

        // Ignore rapid duplicate requests from the same browser session only.
        $duplicate = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table}
             WHERE created_on > NOW() - INTERVAL 30 SECOND
             AND is_search = 1 AND visitor_hash = %s AND search_key = %s
             LIMIT 1",
            $visitor_hash,
            $search_key
        ));

        if (!$duplicate) {
            $wpdb->insert(
                $table,
                [
                    'search_str'   => substr($search_str, 0, 255),
                    'place_id'     => $place_id ? substr($place_id, 0, 255) : null,
                    'search_source'=> substr($search_source, 0, 30),
                    'search_kind'  => substr($search_kind, 0, 40),
                    'search_key'   => $search_key,
                    'country_code'=> $country_code ?: null,
                    'latitude'     => $latitude,
                    'longitude'    => $longitude,
                    'result_count' => $result_count,
                    'visitor_hash' => $visitor_hash,
                    'is_search'    => 1,
                    'ip_address'   => $ip_address,
                ],
                ['%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%d', '%s', '%d', '%s']
            );
        }

        wp_send_json_success(['recorded' => !$duplicate]);
    }

    /**
     * Store an interaction with a store.
     */
    private function capture_store_view($visitor_hash, $ip_address)
    {
        global $wpdb;

        $table    = ASL_PREFIX . 'stores_view';
        $store_id = isset($_POST['store_id']) ? absint($_POST['store_id']) : 0;

        if (!$store_id) {
            wp_send_json_error(['message' => __('A store ID is required.', 'asl_locator')], 400);
        }

        $duplicate = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table}
             WHERE created_on > NOW() - INTERVAL 30 SECOND
             AND is_search = 0 AND visitor_hash = %s AND store_id = %d
             LIMIT 1",
            $visitor_hash,
            $store_id
        ));

        if (!$duplicate) {
            $wpdb->insert(
                $table,
                [
                    'store_id'    => $store_id,
                    'visitor_hash'=> $visitor_hash,
                    'is_search'   => 0,
                    'ip_address'  => $ip_address,
                ],
                ['%d', '%s', '%d', '%s']
            );
        }

        wp_send_json_success(['recorded' => !$duplicate]);
    }

    /**
     * Return a valid coordinate or null.
     */
    private function coordinate($key, $minimum, $maximum)
    {
        if (!isset($_POST[$key]) || '' === $_POST[$key] || !is_numeric($_POST[$key])) {
            return null;
        }

        $value = (float) $_POST[$key];
        return ($value >= $minimum && $value <= $maximum) ? $value : null;
    }
}
