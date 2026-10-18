<?php
namespace AgileStoreLocator\Admin;

if (!defined('ABSPATH')) exit;

/** One-time Google setup. OAuth credentials belong only on agilelogix.com. */
class GoogleConnect {
    const PENDING = 'asl_google_connect_pending';
    const ENDPOINT = 'https://connect.agilelogix.com';

    public function __construct() {
        add_action('wp_ajax_asl_google_connect_start', [$this, 'start']);
        add_action('wp_ajax_asl_google_connect_check', [$this, 'check']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    public static function endpoint() {
        return untrailingslashit(defined('ASL_GOOGLE_CONNECT_URL') ? ASL_GOOGLE_CONNECT_URL : self::ENDPOINT);
    }

    public function enqueue() {
        if (!isset($_GET['page']) || $_GET['page'] !== 'agile-dashboard') return;
        wp_enqueue_script('asl-google-connect', ASL_URL_PATH . 'admin/js/google-connect.js', [], (string) filemtime(ASL_PLUGIN_PATH . 'admin/js/google-connect.js'), true);
        $pending = get_user_option(self::PENDING);
        wp_localize_script('asl-google-connect', 'aslGoogleConnect', [
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('asl_google_connect'),
            'pending' => is_array($pending) && ($pending['expires'] ?? 0) > time(),
            'text' => [
                'starting' => __('Starting Google setup…', 'asl_locator'),
                'continue' => __('Continue Google setup on Agilelogix', 'asl_locator'),
                'waiting' => __('Complete setup in the Agilelogix tab, then check the connection here.', 'asl_locator'),
                'checking' => __('Checking the connection and server key…', 'asl_locator'),
                'failed' => __('The connection could not be completed. Please retry.', 'asl_locator'),
                'saved' => __('Your keys are saved and the server key is verified. Reload this panel, then choose Save and Verify to finish Google Maps setup.', 'asl_locator'),
            ],
        ]);
    }

    public static function render() {
        if (!current_user_can('manage_options') || !current_user_can(ASL_PERMISSION)) return;
        $info = get_option('asl_google_connect_info', []);
        $keys = \AgileStoreLocator\Helper::get_configs(['api_key', 'server_key']);
        $limitsConfigured = is_array($info) && !empty($info['limits_configured_at']) && isset($info['key_fingerprint'])
            && hash_equals($info['key_fingerprint'], hash('sha256', ($keys['api_key'] ?? '') . "\n" . ($keys['server_key'] ?? '')));
        ?>
        <section class="asl-google-connect" id="asl-google-connect" aria-labelledby="asl-google-connect-title">
            <div class="asl-google-connect-heading">
                <span class="asl-google-connect-icon" aria-hidden="true"><svg viewBox="0 0 48 40" class="asl-cloud-mark"><path d="M14 13a13 13 0 0 1 23-2" stroke="#ea4335"/><path d="M7 27a10 10 0 0 1 8-16" stroke="#fbbc04"/><path d="M35 12a10 10 0 0 1 1 21H23" stroke="#4285f4"/><path d="M23 33H13a9 9 0 0 1-6-6" stroke="#34a853"/></svg></span>
                <div><h6 id="asl-google-connect-title"><?php esc_html_e('Connect with Google', 'asl_locator'); ?></h6><span class="asl-connect-secure"><?php esc_html_e("Secure & Automated", "asl_locator"); ?></span><p><?php esc_html_e('We’ll set up Google Maps for your website automatically.', 'asl_locator'); ?></p></div>
            </div>
            <ul class="asl-connect-features"><?php foreach ([__('Creates restricted browser and server API keys', 'asl_locator'), __('Enables required Google Maps APIs', 'asl_locator'), __('Offers daily limits to help manage free usage', 'asl_locator'), __('Secures keys for your website and server', 'asl_locator')] as $feature): ?><li><span aria-hidden="true">✓</span><?php echo esc_html($feature); ?></li><?php endforeach; ?></ul>
            <?php if ($limitsConfigured): ?>
            <div class="asl-google-connect-limits"><span class="asl-google-connect-limits-badge"><?php esc_html_e('Usage limits configured', 'asl_locator'); ?></span><a href="<?php echo esc_url('https://console.cloud.google.com/apis/dashboard?project=' . rawurlencode($info['project'])); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Review in Google Cloud', 'asl_locator'); ?> ↗</a><small><?php esc_html_e('Verified during setup. Changes in Google Cloud are not monitored. A $0 bill is not guaranteed.', 'asl_locator'); ?></small></div>
            <?php endif; ?>
            <div class="asl-google-connect-site"><span><?php esc_html_e('Your website', 'asl_locator'); ?></span><strong><?php echo esc_html(wp_parse_url(home_url('/'), PHP_URL_HOST)); ?></strong></div>
            <label class="asl-google-connect-subdomains"><input type="checkbox" class="form-check-input" id="asl-google-connect-subdomains" checked><span><?php esc_html_e('Include subdomains of this website', 'asl_locator'); ?></span></label>
            <div class="asl-google-connect-actions">
                <button type="button" class="btn asl-google-connect-primary" id="asl-google-connect-start"><?php esc_html_e('Connect Google Maps', 'asl_locator'); ?><span aria-hidden="true"> →</span></button>
                <a class="btn asl-google-connect-primary" id="asl-google-connect-launch" href="#" target="_blank" rel="noopener noreferrer" hidden></a>
                <button type="button" class="btn asl-google-connect-secondary" id="asl-google-connect-check" hidden><?php esc_html_e('Check connection', 'asl_locator'); ?></button>
            </div>
            <div class="asl-google-connect-divider"><span><?php esc_html_e('or', 'asl_locator'); ?></span></div><button type="button" class="btn asl-connect-manual" id="asl-connect-manual" aria-controls="asl-manual-key-panel" aria-expanded="false">⚿ <?php esc_html_e('Enter API keys manually', 'asl_locator'); ?> →</button>
            <p class="asl-google-connect-note"><?php esc_html_e('Uses your Google Cloud project. Google bills usage directly to you.', 'asl_locator'); ?></p>
            <p class="asl-maps-feedback" id="asl-google-connect-status" role="status" aria-live="polite"></p>
            <a class="asl-maps-guide" id="asl-google-connect-reload" href="<?php echo esc_url(admin_url('admin.php?page=agile-dashboard&asl-google-connected=1')); ?>" hidden><?php esc_html_e('Reload and finish setup', 'asl_locator'); ?> →</a>
        </section>
        <?php
    }

    private function authorize() {
        if (!current_user_can('manage_options') || !current_user_can(ASL_PERMISSION)) wp_send_json_error(['message' => __('Administrator access is required.', 'asl_locator')], 403);
        check_ajax_referer('asl_google_connect', 'nonce');
    }

    private function remote($route, $body) {
        $url = self::endpoint();
        $p = wp_parse_url($url);
        if (!$p || ($p['scheme'] ?? '') !== 'https' || isset($p['user']) || isset($p['pass']) || isset($p['query']) || isset($p['fragment'])) return new \WP_Error('connect_url', __('The Google connection service requires a valid HTTPS URL.', 'asl_locator'));
        $response = wp_safe_remote_post($url . '/?route=' . $route, [
            'timeout' => 25, 'redirection' => 0, 'headers' => ['Content-Type' => 'application/json'],
            'body' => wp_json_encode($body), 'limit_response_size' => 16384,
        ]);
        if (is_wp_error($response)) return new \WP_Error('connect_unavailable', __('The Agilelogix Google connection service could not be reached. Please retry later.', 'asl_locator'));
        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($data)) return new \WP_Error('connect_response', __('The Agilelogix Google connection service is unavailable or has not been deployed yet.', 'asl_locator'));
        if (wp_remote_retrieve_response_code($response) !== 200 || !empty($data['error'])) return new \WP_Error('connect_error', isset($data['error']) && is_string($data['error']) ? sanitize_text_field($data['error']) : __('Google setup could not continue.', 'asl_locator'));
        return $data;
    }

    private function fail($error) {
        wp_send_json_error(['message' => is_wp_error($error) ? $error->get_error_message() : $error], 400);
    }

    public function start() {
        $this->authorize();
        $last = (int) get_user_option('asl_google_connect_last_start');
        if ($last > time() - 10) $this->fail(__('Please wait a few seconds before starting again.', 'asl_locator'));
        update_user_option(get_current_user_id(), 'asl_google_connect_last_start', time());
        $verifier = bin2hex(random_bytes(32));
        $configs = \AgileStoreLocator\Helper::get_configs(['search_type', 'direction_redirect']);
        $response = $this->remote('start', [
            'challenge' => hash('sha256', $verifier), 'origin' => home_url('/'), 'admin_origin' => admin_url('/'),
            'subdomains' => isset($_POST['subdomains']) && $_POST['subdomains'] === '1',
            // Preserve the selected frontend modes; never silently switch search/directions.
            'legacy_places' => ($configs['search_type'] ?? '0') !== '4',
            'legacy_directions' => in_array((string) ($configs['direction_redirect'] ?? '2'), ['0', '1'], true),
        ]);
        if (is_wp_error($response)) $this->fail($response);
        $expected = self::endpoint() . '/?route=connect&';
        if (!isset($response['id'], $response['url']) || !is_string($response['id']) || !preg_match('/^[a-f0-9]{48}$/D', $response['id']) || !is_string($response['url']) || strpos($response['url'], $expected) !== 0) $this->fail(__('The connection service returned an invalid session.', 'asl_locator'));
        $pending = ['id' => $response['id'], 'verifier' => $verifier, 'expires' => time() + 1800];
        if (!update_user_option(get_current_user_id(), self::PENDING, $pending)) $this->fail(__('Could not save the temporary connection. Please retry.', 'asl_locator'));
        wp_send_json_success(['url' => $response['url']]);
    }

    public function check() {
        $this->authorize();
        $pending = get_user_option(self::PENDING);
        if (!is_array($pending) || ($pending['expires'] ?? 0) < time()) $this->fail(__('Connection expired. Please start Google setup again.', 'asl_locator'));
        // Prevent simultaneous checks from racing credential verification and saving.
        $lock = 'asl_google_connect_lock';
        if ((int) get_option($lock) < time() - 90) delete_option($lock);
        if (!add_option($lock, time(), '', false)) $this->fail(__('Another connection check is running. Please retry shortly.', 'asl_locator'));
        try { $result = $this->finish($pending); }
        catch (\Throwable $e) { $result = new \WP_Error('connect_failed', __('The connection could not be saved. Please retry.', 'asl_locator')); }
        finally { delete_option($lock); }
        if (is_wp_error($result)) $this->fail($result);
        wp_send_json_success($result);
    }

    private function finish($pending) {
        global $wpdb;
        $proof = ['id' => $pending['id'], 'verifier' => $pending['verifier']];
        // After a lost acknowledgment, don't redo a billable validation or replace keys again.
        if (!empty($pending['saved'])) {
            $ack = $this->remote('ack', $proof);
            if (!is_wp_error($ack)) delete_user_option(get_current_user_id(), self::PENDING);
            return ['status' => 'saved'];
        }
        $data = $this->remote('result', $proof);
        if (is_wp_error($data)) return $data;
        if (($data['status'] ?? '') !== 'ready') return ['status' => 'pending', 'message' => isset($data['message']) ? sanitize_text_field($data['message']) : __('Finish setup on Agilelogix first.', 'asl_locator')];
        foreach (['api_key', 'server_key'] as $name) {
            if (!isset($data[$name]) || !is_string($data[$name]) || !preg_match('/^[A-Za-z0-9_-]{20,200}$/D', $data[$name])) return new \WP_Error('invalid_key', __('The connection service returned an invalid key.', 'asl_locator'));
        }
        $probe = wp_remote_get(add_query_arg(['address' => 'New York, NY', 'key' => $data['server_key']], 'https://maps.googleapis.com/maps/api/geocode/json'), ['timeout' => 20, 'redirection' => 0, 'limit_response_size' => 65536]);
        if (is_wp_error($probe)) return new \WP_Error('probe_failed', __('Could not reach Google from this server. Existing keys were not changed.', 'asl_locator'));
        $check = json_decode(wp_remote_retrieve_body($probe), true);
        if (wp_remote_retrieve_response_code($probe) !== 200 || !in_array($check['status'] ?? '', ['OK', 'ZERO_RESULTS'], true)) return new \WP_Error('probe_denied', __('Google has not accepted the server key yet. Wait a few minutes for restrictions to propagate, then retry. If it still fails, check the public outbound IP addresses, billing and Geocoding API in Google Cloud. Existing keys were not changed.', 'asl_locator'));
        $count = $wpdb->get_var("SELECT COUNT(DISTINCT `key`) FROM " . ASL_PREFIX . "configs WHERE `key` IN ('api_key','server_key')");
        if ((int) $count !== 2) return new \WP_Error('missing_config', __('Google key settings are missing. Repair or update the plugin before connecting.', 'asl_locator'));
        // One statement replaces both keys together and marks the server key private.
        $saved = $wpdb->query($wpdb->prepare("UPDATE " . ASL_PREFIX . "configs SET `value` = CASE `key` WHEN 'api_key' THEN %s WHEN 'server_key' THEN %s END, `type` = CASE WHEN `key` = 'server_key' THEN 'priv' ELSE `type` END WHERE `key` IN ('api_key','server_key')", $data['api_key'], $data['server_key']));
        if ($saved === false) return new \WP_Error('save_failed', __('Could not save Google keys. Please retry.', 'asl_locator'));
        $readback = \AgileStoreLocator\Helper::get_configs(['api_key', 'server_key']);
        if (($readback['api_key'] ?? '') !== $data['api_key'] || ($readback['server_key'] ?? '') !== $data['server_key']) return new \WP_Error('save_failed', __('Could not verify saved Google keys. Please retry.', 'asl_locator'));
        update_option('asl_google_connect_info', ['project' => sanitize_text_field($data['project'] ?? ''), 'connected_at' => time(), 'limits_configured_at' => is_numeric($data['limits_configured_at'] ?? null) && (int)$data['limits_configured_at'] > 0 && (int)$data['limits_configured_at'] <= time() + 300 ? (int)$data['limits_configured_at'] : 0, 'key_fingerprint' => hash('sha256', $data['api_key'] . "\n" . $data['server_key'])], false);
        $pending['saved'] = true;
        update_user_option(get_current_user_id(), self::PENDING, $pending);
        $ack = $this->remote('ack', $proof);
        if (!is_wp_error($ack)) delete_user_option(get_current_user_id(), self::PENDING);
        return ['status' => 'saved'];
    }
}
