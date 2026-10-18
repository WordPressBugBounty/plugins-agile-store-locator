<?php

namespace AgileStoreLocator\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles dashboard-specific admin actions.
 */
class Dashboard extends Base
{
    const ONBOARDING_SETTING_TYPE = 'dashboard';
    const ONBOARDING_SETTING_NAME = 'onboarding_steps';
    const LOCATOR_PAGE_SETTING_NAME = 'locator_page_id';
    const LOCATOR_MANUAL_SETTING_NAME = 'locator_manual_completion';

    /**
     * Return the persisted onboarding checklist with stable defaults.
     */
    public static function get_onboarding_state($store_count = null)
    {
        $defaults = [
            'connect_google_maps' => false,
            'add_stores'          => false,
            'add_to_website'      => false,
            'customize_locator'   => false,
        ];

        $saved = \AgileStoreLocator\Helper::get_setting(
            self::ONBOARDING_SETTING_TYPE,
            self::ONBOARDING_SETTING_NAME
        );
        $saved = is_string($saved) ? json_decode($saved, true) : [];

        if (!is_array($saved)) {
            $saved = [];
        }

        $state = array_merge($defaults, array_intersect_key($saved, $defaults));

        foreach ($state as $step => $completed) {
            $state[$step] = (bool) $completed;
        }

        // A store may be created through the form, an import, or an integration.
        // Reconcile step 2 from the actual store count whenever it is available.
        if (null !== $store_count && 0 < (int) $store_count && !$state['add_stores']) {
            $state['add_stores'] = true;

            \AgileStoreLocator\Helper::set_setting(
                wp_json_encode($state),
                self::ONBOARDING_SETTING_TYPE,
                self::ONBOARDING_SETTING_NAME
            );
        }

        return $state;
    }

    /**
     * Persist one known onboarding step while preserving the other steps.
     */
    public static function set_onboarding_step($step, $completed)
    {
        $state = self::get_onboarding_state();

        if (!array_key_exists($step, $state)) {
            return false;
        }

        $state[$step] = (bool) $completed;

        return \AgileStoreLocator\Helper::set_setting(
            wp_json_encode($state),
            self::ONBOARDING_SETTING_TYPE,
            self::ONBOARDING_SETTING_NAME
        );
    }

    /**
     * Return the saved locator-page state without scanning page content.
     */
    public static function get_locator_setup_state()
    {
        $page_id = absint(\AgileStoreLocator\Helper::get_setting(
            self::ONBOARDING_SETTING_TYPE,
            self::LOCATOR_PAGE_SETTING_NAME
        ));
        $manual = (bool) \AgileStoreLocator\Helper::get_setting(
            self::ONBOARDING_SETTING_TYPE,
            self::LOCATOR_MANUAL_SETTING_NAME
        );
        $page = $page_id ? get_post($page_id) : null;
        $valid_page = $page
            && 'page' === $page->post_type
            && !in_array($page->post_status, ['trash', 'auto-draft'], true);

        return [
            'page_id'      => $page_id,
            'page'         => $valid_page ? $page : null,
            'valid_page'   => (bool) $valid_page,
            'missing_page' => 0 < $page_id && !$valid_page,
            'manual'       => $manual,
            'completed'    => (bool) ($valid_page || $manual),
        ];
    }

    /**
     * Mark the locator as manually added without modifying page-builder content.
     */
    public function complete_locator_manually()
    {
        $saved = \AgileStoreLocator\Helper::set_setting(
            '1',
            self::ONBOARDING_SETTING_TYPE,
            self::LOCATOR_MANUAL_SETTING_NAME
        );

        if (false === $saved || false === self::set_onboarding_step('add_to_website', true)) {
            return $this->send_response([
                'success' => false,
                'error'   => esc_attr__('Unable to save the setup status.', 'asl_locator'),
            ]);
        }

        return $this->send_response([
            'success' => true,
            'msg'     => esc_attr__('Your locator has been marked as added.', 'asl_locator'),
        ]);
    }

    /**
     * AJAX endpoint used by individual onboarding step implementations.
     */
    public function save_onboarding_step()
    {
        $step = isset($_POST['step'])
            ? sanitize_key(wp_unslash($_POST['step']))
            : '';
        $completed = isset($_POST['completed']) && '1' === sanitize_text_field(wp_unslash($_POST['completed']));

        if (!array_key_exists($step, self::get_onboarding_state())) {
            return $this->send_response([
                'success' => false,
                'error'   => esc_attr__('Invalid onboarding step.', 'asl_locator'),
            ]);
        }

        $saved = self::set_onboarding_step($step, $completed);

        if (false === $saved) {
            return $this->send_response([
                'success' => false,
                'error'   => esc_attr__('Unable to save the onboarding step.', 'asl_locator'),
            ]);
        }

        return $this->send_response([
            'success' => true,
            'msg'     => esc_attr__('Onboarding progress saved.', 'asl_locator'),
            'state'   => self::get_onboarding_state(),
        ]);
    }

    /** Find the first active store shortcode, ignoring escaped examples. */
    public static function get_store_page_shortcode($content)
    {
        preg_match_all('/' . get_shortcode_regex(['ASL_STORE']) . '/s', $content, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if ('[' !== $match[1] && ']' !== $match[6]) {
                return $match;
            }
        }
        return null;
    }

    /** Explain why a connected page cannot be updated automatically. */
    public static function store_page_update_error($page)
    {
        if (!$page || 'page' !== $page->post_type || in_array($page->post_status, ['trash', 'auto-draft'], true)) {
            return __('The connected Store Page is missing or unavailable. Review its page ID in Store Page Settings.', 'asl_locator');
        }
        foreach (['_elementor_data', '_fl_builder_data', '_bricks_page_content_2', '_bricks_page_content', '_oxygen_vsb_content', 'ct_builder_shortcodes', '_cornerstone_data'] as $key) {
            if (get_post_meta($page->ID, $key, true)) {
                return __('This page uses a page builder. Edit the store shortcode in the page editor to preserve its layout.', 'asl_locator');
            }
        }
        if ('builder' === get_post_meta($page->ID, '_elementor_edit_mode', true)
            || get_post_meta($page->ID, '_fl_builder_enabled', true)
            || 'on' === get_post_meta($page->ID, '_et_pb_use_builder', true)
            || preg_match('/\[(?:et_pb_|vc_|fusion_|fl_builder|cs_element|brizy)/i', $page->post_content)
            || preg_match('/<!--\s+wp:(?!core\/)[a-z0-9_-]+\//i', $page->post_content)) {
            return __('This page uses a page builder or custom blocks. Edit the store shortcode in the page editor to preserve its layout.', 'asl_locator');
        }
        preg_match_all('/' . get_shortcode_regex(['ASL_STORE']) . '/s', $page->post_content, $matches, PREG_SET_ORDER);
        $active = array_filter($matches, function ($match) { return '[' !== $match[1] && ']' !== $match[6]; });
        if (0 === count($active)) {
            return __('No store shortcode was found in the page content. Open the page editor to add or update it.', 'asl_locator');
        }
        if (1 !== count($active)) {
            return __('This page contains multiple store shortcodes. Update the intended shortcode in the page editor.', 'asl_locator');
        }
        $match = reset($active);
        // A shortcode in serialized block attributes, HTML attributes or examples is ambiguous.
        $stripped = preg_replace('/<(?:pre|code)\b[^>]*>[\s\S]*?<\/(?:pre|code)>|<!--(?!\s*\/?wp:shortcode\b)[\s\S]*?-->|<[^>]*>/i', '', $page->post_content);
        if (!self::get_store_page_shortcode($stripped) || false !== strpos($match[0], '[/ASL_STORE]')) {
            return __('The store shortcode is not in a supported standalone format. Update it in the page editor.', 'asl_locator');
        }
        return '';
    }

    /** Keep the configured store URL aligned with WordPress permalink changes. */
    public static function sync_store_page_slug($post_id, $post, $update)
    {
        if ('page' !== $post->post_type || in_array($post->post_status, ['trash', 'auto-draft'], true)
            || wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
            return;
        }
        $page_id = (int) \AgileStoreLocator\Helper::get_configs('rewrite_id');
        if (!$page_id) {
            return;
        }
        // A parent's slug also forms part of a child page's URL.
        if ($page_id !== (int) $post_id && !in_array((int) $post_id, array_map('intval', get_post_ancestors($page_id)), true)) {
            return;
        }
        $slug = get_page_uri($page_id);
        if (!$slug || $slug === \AgileStoreLocator\Helper::get_configs('rewrite_slug')) {
            return;
        }
        global $wpdb;
        $saved = $wpdb->update(ASL_PREFIX . 'configs', ['value' => $slug], ['key' => 'rewrite_slug']);
        if (false !== $saved) {
            // The save request may still hold the old rules; rebuild on the next init.
            update_option('asl_store_page_rewrite_pending', true, false);
        }
    }

    /** Refresh once after init has registered rules using the current configuration. */
    public static function refresh_store_page_rewrites()
    {
        if (!get_option('asl_store_page_rewrite_pending')) {
            return;
        }
        $page_id = (int) \AgileStoreLocator\Helper::get_configs('rewrite_id');
        $slug = \AgileStoreLocator\Helper::get_configs('rewrite_slug');
        if ($page_id && $slug) {
            \AgileStoreLocator\Schema\Slug::register_rewrite_rules($slug, $page_id);
            flush_rewrite_rules(false);
        }
        delete_option('asl_store_page_rewrite_pending');
    }

    /** Create or update the connected store details page. */
    public function create_store_page()
    {
        global $wpdb;

        if (!current_user_can('edit_pages')) {
            return $this->send_response(['success' => false, 'error' => __('You cannot create pages.', 'asl_locator')]);
        }

        $title = isset($_POST['page_title']) ? sanitize_text_field(wp_unslash($_POST['page_title'])) : '';
        if ('' === $title) {
            return $this->send_response(['success' => false, 'error' => __('Please enter a page title.', 'asl_locator')]);
        }

        $configured_page_id = \AgileStoreLocator\Helper::get_configs('rewrite_id');
        $page_id = (int) $configured_page_id;
        $page = $page_id ? get_post($page_id) : null;
        $updating = !empty($configured_page_id);
        if ($updating) {
            $update_error = self::store_page_update_error($page);
            if ($update_error) {
                return $this->send_response(['success' => false, 'error' => $update_error]);
            }
        }
        if ($updating && !current_user_can('edit_post', $page_id)) {
            return $this->send_response(['success' => false, 'error' => __('You cannot edit this page.', 'asl_locator')]);
        }
        if ($updating) {
            $expected = isset($_POST['page_version']) && is_string($_POST['page_version']) ? wp_unslash($_POST['page_version']) : '';
            if (!hash_equals(hash('sha256', $page->post_title . "\n" . $page->post_content), $expected)) {
                return $this->send_response(['success' => false, 'error' => __('The page changed since this dashboard was loaded. Reload the dashboard before updating it.', 'asl_locator')]);
            }
        }
        $match = $updating ? self::get_store_page_shortcode($page->post_content) : null;
        $attributes = $match ? shortcode_parse_atts($match[3]) : [];
        $attributes = is_array($attributes) ? $attributes : [];
        // Preserve other shortcode attributes when updating display options.
        unset($attributes['coords_direction']);
        $shortcode = '[ASL_STORE';
        $options = isset($_POST['page_options']) && is_array($_POST['page_options'])
            ? wp_unslash($_POST['page_options']) : [];
        foreach (['map', 'hide_hours', 'closed_label', 'additional_info', 'show_categories', 'store_page_show_country', 'store_schema', 'coords_direction'] as $option) {
            if (!isset($options[$option]) || !is_string($options[$option]) || !in_array($options[$option], ['0', '1'], true)) {
                continue;
            }
            // The template checks presence rather than the value for this attribute.
            if ('coords_direction' === $option && '1' !== $options[$option]) {
                continue;
            }
            $attributes[$option] = $options[$option];
        }
        foreach ($attributes as $key => $value) {
            if (is_string($key) && preg_match('/^[a-zA-Z0-9_-]+$/', $key)) {
                $shortcode .= ' ' . $key . '="' . esc_attr($value) . '"';
            } elseif (is_int($key)) {
                $shortcode .= ' "' . esc_attr($value) . '"';
            }
        }
        $shortcode .= ']';

        if ($updating) {
            // Keep all unrelated attributes byte-for-byte, including custom values and quoting.
            $controlled = ['map', 'hide_hours', 'closed_label', 'additional_info', 'show_categories', 'store_page_show_country', 'store_schema', 'coords_direction'];
            $raw_attributes = preg_replace_callback(get_shortcode_atts_regex(), function ($attribute) use ($controlled) {
                $name = !empty($attribute[1]) ? $attribute[1] : (!empty($attribute[3]) ? $attribute[3] : (isset($attribute[5]) ? $attribute[5] : ''));
                return in_array(strtolower($name), $controlled, true) ? '' : $attribute[0];
            }, $match[3]);
            $shortcode = '[ASL_STORE' . rtrim($raw_attributes);
            foreach ($controlled as $key) {
                if (isset($attributes[$key])) {
                    $shortcode .= ' ' . $key . '="' . esc_attr($attributes[$key]) . '"';
                }
            }
            $shortcode .= !empty($match[4]) ? ' /]' : ']';
            // Use the active match offset so escaped examples earlier in the page remain intact.
            preg_match_all('/' . get_shortcode_regex(['ASL_STORE']) . '/s', $page->post_content, $located, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
            foreach ($located as $candidate) {
                if ('[' !== $candidate[1][0] && ']' !== $candidate[6][0]) {
                    $opening_length = strlen($candidate[0][0]) - strlen($candidate[6][0]);
                    $content = substr_replace($page->post_content, $shortcode, $candidate[0][1], $opening_length);
                    break;
                }
            }
            $saved_page = wp_update_post(wp_slash([
                'ID' => $page_id,
                'post_title' => $title,
                'post_content' => $content,
            ]), true);
            if (is_wp_error($saved_page)) {
                return $this->send_response(['success' => false, 'error' => $saved_page->get_error_message()]);
            }
        } else {
            $page_id = wp_insert_post([
                'post_title' => $title,
                'post_name' => sanitize_title($title),
                'post_content' => $shortcode,
                'post_status' => 'draft',
                'post_type' => 'page',
                'post_author' => get_current_user_id(),
            ], true);
            if (is_wp_error($page_id)) {
                return $this->send_response(['success' => false, 'error' => $page_id->get_error_message()]);
            }
        }

        if (!$updating) {
            // Drafts skip WordPress's uniqueness check; reserve the eventual public slug now.
            $created_page = get_post($page_id);
            $unique_slug = wp_unique_post_slug($created_page->post_name, $page_id, 'publish', 'page', $created_page->post_parent);
            if ($unique_slug !== $created_page->post_name) {
                $saved_slug = wp_update_post(wp_slash(['ID' => $page_id, 'post_name' => $unique_slug]), true);
                if (is_wp_error($saved_slug)) {
                    return $this->send_response(['success' => false, 'error' => $saved_slug->get_error_message()]);
                }
            }
        }
        // Read the persisted page URI, including any suffix chosen by WordPress.
        $slug = get_page_uri($page_id);
        foreach (['rewrite_id' => (string) $page_id, 'rewrite_slug' => $slug, 'link_type' => '1'] as $key => $value) {
            $exists = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM ' . ASL_PREFIX . 'configs WHERE `key` = %s', $key));
            $saved = $exists
                ? $wpdb->update(ASL_PREFIX . 'configs', ['value' => $value], ['key' => $key])
                : $wpdb->insert(ASL_PREFIX . 'configs', ['key' => $key, 'value' => $value]);
            if (false === $saved) {
                return $this->send_response(['success' => false, 'error' => __('The page exists, but its settings could not be saved. Connect it in Store Page Settings.', 'asl_locator')]);
            }
        }
        \AgileStoreLocator\Schema\Slug::register_rewrite_rules($slug, $page_id);
        flush_rewrite_rules(false);

        return $this->send_response(['success' => true, 'page_id' => $page_id, 'edit_url' => get_edit_post_link($page_id, 'raw')]);
    }

    /**
     * Create a draft page containing the store locator shortcode.
     */
    public function create_locator_page()
    {
        $page_title = isset($_POST['page_title'])
            ? sanitize_text_field(wp_unslash($_POST['page_title']))
            : '';

        if ('' === $page_title) {
            return $this->send_response([
                'success' => false,
                'error'   => esc_attr__('Please enter a page title.', 'asl_locator'),
            ]);
        }

        $locator_state = self::get_locator_setup_state();

        if ($locator_state['valid_page']) {
            $page_id = $locator_state['page_id'];

            return $this->send_response([
                'success'  => true,
                'msg'      => esc_attr__('Your locator page already exists.', 'asl_locator'),
                'page_id'  => $page_id,
                'edit_url' => get_edit_post_link($page_id, 'raw'),
                'view_url' => get_permalink($page_id),
            ]);
        }

        $page_id = wp_insert_post([
            'post_title'   => $page_title,
            'post_content' => '[ASL_STORELOCATOR]',
            'post_status'  => 'draft',
            'post_type'    => 'page',
            'post_author'  => get_current_user_id(),
        ], true);

        if (is_wp_error($page_id)) {
            return $this->send_response([
                'success' => false,
                'error'   => $page_id->get_error_message(),
            ]);
        }

        $page_saved = \AgileStoreLocator\Helper::set_setting(
            (string) $page_id,
            self::ONBOARDING_SETTING_TYPE,
            self::LOCATOR_PAGE_SETTING_NAME
        );

        if (false === $page_saved || false === self::set_onboarding_step('add_to_website', true)) {
            return $this->send_response([
                'success' => false,
                'error'   => esc_attr__('The page was created, but its setup status could not be saved.', 'asl_locator'),
                'page_id' => $page_id,
            ]);
        }

        return $this->send_response([
            'success'  => true,
            'msg'      => esc_attr__('Locator page created successfully.', 'asl_locator'),
            'page_id'  => $page_id,
            'edit_url' => get_edit_post_link($page_id, 'raw'),
            'view_url' => get_permalink($page_id),
        ]);
    }
}

