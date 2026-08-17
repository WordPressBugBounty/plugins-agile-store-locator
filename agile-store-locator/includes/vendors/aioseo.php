<?php

namespace AgileStoreLocator\Vendors;

defined('ABSPATH') || exit;

/**
 * All in One SEO integration for store detail pages.
 */
class Aioseo {

    private $store = null;
    private $store_resolved = false;

    /**
     * Register AIOSEO metadata filters.
     */
    public function register_hooks() {
        add_filter('aioseo_title', [$this, 'filter_title']);
        add_filter('aioseo_description', [$this, 'filter_description']);
        add_filter('aioseo_canonical_url', [$this, 'filter_canonical']);
        add_filter('aioseo_facebook_tags', [$this, 'filter_facebook_tags']);
        add_filter('aioseo_twitter_tags', [$this, 'filter_twitter_tags']);
    }

    public function filter_title($title) {
        $store = $this->get_store();

        return $store && isset($store->title) && $store->title
            ? sanitize_text_field($store->title)
            : $title;
    }

    public function filter_description($description) {
        $store_description = $this->get_store_description();

        return $store_description ?: $description;
    }

    public function filter_canonical($url) {
        if (!$this->get_store()) {
            return $url;
        }

        return \AgileStoreLocator\Schema\Slug::update_canonical_tag($url);
    }

    public function filter_facebook_tags($tags) {
        if (!$this->get_store() || !is_array($tags)) {
            return $tags;
        }

        $tags['og:title'] = $this->filter_title($tags['og:title'] ?? '');
        $tags['og:url'] = $this->filter_canonical($tags['og:url'] ?? '');

        $description = $this->get_store_description();
        if ($description) {
            $tags['og:description'] = $description;
        }

        return $tags;
    }

    public function filter_twitter_tags($tags) {
        if (!$this->get_store() || !is_array($tags)) {
            return $tags;
        }

        $tags['twitter:title'] = $this->filter_title($tags['twitter:title'] ?? '');

        $description = $this->get_store_description();
        if ($description) {
            $tags['twitter:description'] = $description;
        }

        return $tags;
    }

    private function get_store() {
        if (!$this->store_resolved) {
            $this->store_resolved = true;

            if (get_query_var('sl-store', false)) {
                $this->store = \AgileStoreLocator\Model\Store::get_store_id_via_slug();
            }
        }

        return $this->store;
    }

    private function get_store_description() {
        if (!$this->get_store()) {
            return '';
        }

        return html_entity_decode(
            \AgileStoreLocator\Schema\Slug::get_meta_description_by_store_slug(),
            ENT_QUOTES,
            get_bloginfo('charset') ?: 'UTF-8'
        );
    }
}
