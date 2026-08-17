<?php

namespace AgileStoreLocator\Vendors;

defined('ABSPATH') || exit;

/**
 * Implement the SeoPress class
 */
class SeoPress {

    private $store = null;
    private $store_resolved = false;

    public function __construct() {
        add_filter('seopress_sitemaps_cpt', [$this, 'add_custom_post_type']);
        add_filter('seopress_titles_title', [$this, 'filter_titles_title']);
        add_filter('seopress_titles_canonical', [$this, 'filter_titles_canonical']);
        add_filter('seopress_titles_desc', [$this, 'filter_description']);
        add_filter('seopress_social_og_title', [$this, 'filter_social_title']);
        add_filter('seopress_social_og_desc', [$this, 'filter_social_description']);
        add_filter('seopress_social_og_url', [$this, 'filter_social_url']);
        add_filter('seopress_social_twitter_card_title', [$this, 'filter_social_title']);
        add_filter('seopress_social_twitter_card_desc', [$this, 'filter_social_description']);
    }

    public function add_custom_post_type($post_types) {
        if (defined('ASL_REGISTER_TYPE')) {
            $post_types['asl_stores'] = (object)[
                'name' => 'asl_stores',
                'labels' => (object)[
                    'name' => 'asl_stores'
                ],
            ];
        }
        return $post_types;
    }

    /**
     * Filter the description
     *
     * @param string $description
     * @return string
     */
    public function filter_description($description) {
        if ($this->get_store()) {
            $description = $this->get_store_description();
        }

        return $description;
    }
    

    /**
     * Filter the title
     *
     * @param string $title
     * @return string
     */
    public function filter_titles_title($title) {
        $store = $this->get_store();

        if ($store && isset($store->title)) {
            $title = $store->title;
        }

        return $title;
    }

    /**
     * Filter the canonical URL
     *
     * @param string $html
     * @return string
     */
    public function filter_titles_canonical($html) {
        if (!$this->get_store()) {
            return $html;
        }

        $canonical_url = \AgileStoreLocator\Schema\Slug::update_canonical_tag('');

        if (!$canonical_url) {
            return $html;
        }

        // Legacy SEOPress filters pass the complete tag; newer services may
        // pass only the URL value.
        if (is_string($html) && strpos($html, '<link') !== false) {
            return '<link rel="canonical" href="'.esc_url($canonical_url).'">';
        }

        return $canonical_url;
    }

    /**
     * Use the store title for Open Graph and Twitter title metadata.
     *
     * @param string $title
     * @return string
     */
    public function filter_social_title($title) {
        $store = $this->get_store();

        if (!$store || empty($store->title)) {
            return $title;
        }

        $store_title = sanitize_text_field($store->title);

        if (is_string($title) && strpos($title, '<meta') !== false) {
            $attribute = (strpos($title, 'twitter:') !== false) ? 'name="twitter:title"' : 'property="og:title"';
            return '<meta '.$attribute.' content="'.esc_attr($store_title).'">';
        }

        return $store_title;
    }

    /**
     * Use the store description for Open Graph and Twitter metadata.
     *
     * @param string $description
     * @return string
     */
    public function filter_social_description($description) {
        if (!$this->get_store()) {
            return $description;
        }

        $store_description = $this->get_store_description();

        if (is_string($description) && strpos($description, '<meta') !== false) {
            $attribute = (strpos($description, 'twitter:') !== false) ? 'name="twitter:description"' : 'property="og:description"';
            return '<meta '.$attribute.' content="'.esc_attr($store_description).'">';
        }

        return $store_description;
    }

    /**
     * Use the current store detail URL for Open Graph metadata.
     *
     * @param string $html
     * @return string
     */
    public function filter_social_url($html) {
        if (!$this->get_store()) {
            return $html;
        }

        $store_url = \AgileStoreLocator\Schema\Slug::update_canonical_tag('');

        if (!$store_url) {
            return $html;
        }

        if (is_string($html) && strpos($html, '<meta') !== false) {
            return '<meta property="og:url" content="'.esc_url($store_url).'">';
        }

        return $store_url;
    }

    /**
     * Resolve the store represented by the current detail-page rewrite.
     *
     * @return object|null
     */
    private function get_store() {
        if ($this->store_resolved) {
            return $this->store;
        }

        $this->store_resolved = true;

        if (!get_query_var('sl-store', false)) {
            return null;
        }

        $this->store = \AgileStoreLocator\Model\Store::get_store_id_via_slug();

        return $this->store;
    }

    /**
     * Return a plain-text store description suitable for metadata.
     *
     * @return string
     */
    private function get_store_description() {
        return html_entity_decode(
            \AgileStoreLocator\Schema\Slug::get_meta_description_by_store_slug(),
            ENT_QUOTES,
            get_bloginfo('charset') ?: 'UTF-8'
        );
    }
}
