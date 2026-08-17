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
