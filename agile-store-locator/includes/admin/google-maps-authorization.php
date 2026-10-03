<?php

namespace AgileStoreLocator\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Provisions a referrer-restricted Google Maps browser key after OAuth consent.
 *
 * Define ASL_GOOGLE_OAUTH_CLIENT_ID and ASL_GOOGLE_OAUTH_CLIENT_SECRET outside
 * the plugin (normally in wp-config.php), or provide them with the matching
 * filters. Never distribute the OAuth client secret with the plugin.
 */
class GoogleMapsAuthorization extends Base
{
    const TOKEN_TTL = 3300;

    /** Register the OAuth callback, which does not pass through the AJAX router. */
    public function register_hooks()
    {
        add_action('admin_post_asl_google_maps_oauth_callback', [$this, 'oauth_callback']);
    }

    /** Whether automatic setup has credentials supplied by the site/vendor. */
    public static function is_configured()
    {
        return '' !== self::client_id() && '' !== self::client_secret();
    }

    /** Start OAuth and return the Google consent URL. */
    public function authorization_url()
    {
        if (!self::is_configured()) {
            return $this->send_response([
                'success' => false,
                'error'   => esc_attr__('Automatic setup is not configured on this site.', 'asl_locator'),
            ]);
        }

        $state = wp_generate_password(48, false, false);
        set_transient($this->state_key($state), get_current_user_id(), 10 * MINUTE_IN_SECONDS);

        $url = add_query_arg([
            'client_id'              => self::client_id(),
            'redirect_uri'           => self::redirect_uri(),
            'response_type'          => 'code',
            'scope'                  => 'https://www.googleapis.com/auth/cloud-platform',
            'access_type'            => 'online',
            'include_granted_scopes' => 'true',
            'prompt'                 => 'consent select_account',
            'state'                  => $state,
        ], 'https://accounts.google.com/o/oauth2/v2/auth');

        return $this->send_response(['success' => true, 'authorization_url' => $url]);
    }

    /** Exchange the authorization code and return the administrator to the dashboard. */
    public function oauth_callback()
    {
        if (!current_user_can(ASL_PERMISSION)) {
            wp_die(
                esc_html__('You are not allowed to configure Google Maps.', 'asl_locator'),
                esc_html__('Google Maps authorization', 'asl_locator'),
                ['response' => 403]
            );
        }

        $state = isset($_GET['state']) ? sanitize_text_field(wp_unslash($_GET['state'])) : '';
        $code  = isset($_GET['code']) ? sanitize_text_field(wp_unslash($_GET['code'])) : '';
        $oauth_error = isset($_GET['error']) ? sanitize_text_field(wp_unslash($_GET['error'])) : '';
        $owner = $state ? (int) get_transient($this->state_key($state)) : 0;
        delete_transient($this->state_key($state));

        if ($oauth_error) {
            $this->redirect_with_status('error', __('Google authorization was cancelled or denied.', 'asl_locator'));
        }

        if (!$state || !$code || $owner !== get_current_user_id()) {
            $this->redirect_with_status('error', __('Google authorization could not be verified. Please try again.', 'asl_locator'));
        }

        $response = wp_remote_post('https://oauth2.googleapis.com/token', [
            'timeout' => 20,
            'body'    => [
                'code'          => $code,
                'client_id'     => self::client_id(),
                'client_secret' => self::client_secret(),
                'redirect_uri'  => self::redirect_uri(),
                'grant_type'    => 'authorization_code',
            ],
        ]);
        $body = $this->response_body($response);

        if (is_wp_error($body) || empty($body['access_token'])) {
            $message = is_wp_error($body) ? $body->get_error_message() : __('Google did not return an access token.', 'asl_locator');
            $this->redirect_with_status('error', $message);
        }

        set_transient($this->token_key(), sanitize_text_field($body['access_token']), min(self::TOKEN_TTL, max(300, (int) ($body['expires_in'] ?? self::TOKEN_TTL))));
        $this->redirect_with_status('authorized', __('Google Cloud authorization succeeded. Select a project to continue.', 'asl_locator'));
    }

    /** Return active Google Cloud projects visible to the authorized user. */
    public function list_projects()
    {
        $response = $this->google_request('GET', 'https://cloudresourcemanager.googleapis.com/v1/projects?filter=lifecycleState%3AACTIVE&pageSize=200');
        if (is_wp_error($response)) {
            return $this->send_error($response);
        }

        $projects = [];
        foreach (($response['projects'] ?? []) as $project) {
            if (empty($project['projectId']) || empty($project['projectNumber'])) {
                continue;
            }
            $projects[] = [
                'id'     => sanitize_text_field($project['projectId']),
                'number' => sanitize_text_field($project['projectNumber']),
                'name'   => sanitize_text_field($project['name'] ?? $project['projectId']),
            ];
        }

        return $this->send_response(['success' => true, 'projects' => $projects]);
    }

    /** Enable Maps services, create a restricted browser key, and save it. */
    public function provision_key()
    {
        global $wpdb;

        $project_id     = isset($_POST['project_id']) ? sanitize_text_field(wp_unslash($_POST['project_id'])) : '';
        $project_number = isset($_POST['project_number']) ? preg_replace('/\D/', '', wp_unslash($_POST['project_number'])) : '';
        if (!$project_id || !$project_number) {
            return $this->send_response(['success' => false, 'error' => esc_attr__('Select a Google Cloud project.', 'asl_locator')]);
        }

        // Validate that the submitted number belongs to the submitted project and user.
        $project = $this->google_request('GET', 'https://cloudresourcemanager.googleapis.com/v1/projects/' . rawurlencode($project_id));
        if (is_wp_error($project) || (string) ($project['projectNumber'] ?? '') !== $project_number) {
            return $this->send_response(['success' => false, 'error' => esc_attr__('The selected Google Cloud project could not be verified.', 'asl_locator')]);
        }

        $services = apply_filters('asl_google_maps_provisioned_services', [
            'serviceusage.googleapis.com',
            'apikeys.googleapis.com',
            'maps-backend.googleapis.com',
            'places-backend.googleapis.com',
            'geocoding-backend.googleapis.com',
            'directions-backend.googleapis.com',
        ]);

        $enabled = $this->google_request(
            'POST',
            'https://serviceusage.googleapis.com/v1/projects/' . rawurlencode($project_number) . '/services:batchEnable',
            ['serviceIds' => array_values(array_map('sanitize_text_field', $services))]
        );
        if (is_wp_error($enabled)) {
            return $this->send_error($enabled);
        }

        $operation = $this->wait_for_operation($enabled['name'] ?? '', 'https://serviceusage.googleapis.com/v1/');
        if (is_wp_error($operation)) {
            return $this->send_error($operation);
        }

        $referrers = apply_filters('asl_google_maps_allowed_referrers', $this->site_referrers());
        $key = $this->google_request(
            'POST',
            'https://apikeys.googleapis.com/v2/projects/' . rawurlencode($project_number) . '/locations/global/keys',
            [
                'displayName'  => 'Agile Store Locator - ' . wp_parse_url(home_url(), PHP_URL_HOST),
                'restrictions' => [
                    'browserKeyRestrictions' => ['allowedReferrers' => array_values($referrers)],
                    'apiTargets' => array_map(static function ($service) {
                        return ['service' => $service];
                    }, array_values(array_diff($services, ['serviceusage.googleapis.com', 'apikeys.googleapis.com']))),
                ],
            ]
        );
        if (is_wp_error($key)) {
            return $this->send_error($key);
        }

        $operation = $this->wait_for_operation($key['name'] ?? '', 'https://apikeys.googleapis.com/v2/');
        if (is_wp_error($operation) || empty($operation['response']['name'])) {
            return $this->send_error(is_wp_error($operation) ? $operation : new \WP_Error('asl_google_key', __('Google created the key but did not return its resource name.', 'asl_locator')));
        }

        $key_data = $this->google_request('GET', 'https://apikeys.googleapis.com/v2/' . ltrim($operation['response']['name'], '/') . '/keyString');
        if (is_wp_error($key_data) || empty($key_data['keyString'])) {
            return $this->send_error(is_wp_error($key_data) ? $key_data : new \WP_Error('asl_google_key', __('The generated API key could not be retrieved.', 'asl_locator')));
        }

        $updated = $wpdb->update(ASL_PREFIX . 'configs', ['value' => sanitize_text_field($key_data['keyString'])], ['key' => 'api_key']);
        if (false === $updated) {
            return $this->send_response(['success' => false, 'error' => esc_attr__('The key was created but could not be saved in WordPress.', 'asl_locator')]);
        }

        Dashboard::set_onboarding_step('connect_google_maps', false);
        delete_transient($this->token_key());

        return $this->send_response([
            'success' => true,
            'api_key' => sanitize_text_field($key_data['keyString']),
            'msg'     => esc_attr__('Google Maps APIs and restrictions were configured. Verifying the generated key…', 'asl_locator'),
        ]);
    }

    private function google_request($method, $url, array $body = null)
    {
        $token = get_transient($this->token_key());
        if (!$token) {
            return new \WP_Error('asl_google_auth_expired', __('Google authorization expired. Please connect again.', 'asl_locator'));
        }

        $args = ['method' => $method, 'timeout' => 30, 'headers' => ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json']];
        if (null !== $body) {
            $args['headers']['Content-Type'] = 'application/json';
            $args['body'] = wp_json_encode($body);
        }
        return $this->response_body(wp_remote_request($url, $args));
    }

    private function wait_for_operation($name, $base_url)
    {
        if (!$name) {
            return new \WP_Error('asl_google_operation', __('Google did not return an operation identifier.', 'asl_locator'));
        }
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $operation = $this->google_request('GET', $base_url . ltrim($name, '/'));
            if (is_wp_error($operation) || !empty($operation['done'])) {
                if (!is_wp_error($operation) && !empty($operation['error']['message'])) {
                    return new \WP_Error('asl_google_operation', sanitize_text_field($operation['error']['message']));
                }
                return $operation;
            }
            usleep(500000);
        }
        return new \WP_Error('asl_google_operation_timeout', __('Google Cloud configuration is taking too long. Please try again.', 'asl_locator'));
    }

    private function response_body($response)
    {
        if (is_wp_error($response)) {
            return $response;
        }
        $body = json_decode(wp_remote_retrieve_body($response), true);
        $code = wp_remote_retrieve_response_code($response);
        if ($code < 200 || $code >= 300) {
            return new \WP_Error('asl_google_api', sanitize_text_field($body['error']['message'] ?? __('Google Cloud rejected the request.', 'asl_locator')));
        }
        return is_array($body) ? $body : [];
    }

    private function send_error(\WP_Error $error)
    {
        return $this->send_response(['success' => false, 'error' => $error->get_error_message()]);
    }

    private function site_referrers()
    {
        $parts = wp_parse_url(home_url());
        $origin = ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? '');
        if (!empty($parts['port'])) {
            $origin .= ':' . absint($parts['port']);
        }
        return [$origin . '/*'];
    }

    private function redirect_with_status($status, $message)
    {
        wp_safe_redirect(add_query_arg(['page' => 'agile-dashboard', 'asl_google_auth' => $status, 'asl_google_message' => $message], admin_url('admin.php')));
        exit;
    }

    private function token_key()
    {
        return 'asl_gmaps_token_' . get_current_user_id();
    }

    private function state_key($state)
    {
        return 'asl_gmaps_state_' . hash('sha256', $state);
    }

    private static function redirect_uri()
    {
        return admin_url('admin-post.php?action=asl_google_maps_oauth_callback');
    }

    private static function client_id()
    {
        $value = defined('ASL_GOOGLE_OAUTH_CLIENT_ID') ? ASL_GOOGLE_OAUTH_CLIENT_ID : '';
        return trim((string) apply_filters('asl_google_oauth_client_id', $value));
    }

    private static function client_secret()
    {
        $value = defined('ASL_GOOGLE_OAUTH_CLIENT_SECRET') ? ASL_GOOGLE_OAUTH_CLIENT_SECRET : '';
        return trim((string) apply_filters('asl_google_oauth_client_secret', $value));
    }
}
