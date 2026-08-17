<?php

namespace AgileStoreLocator\Vendors;

defined('ABSPATH') || exit;

/**
 * Keep Ocean Extra Open Graph metadata aligned with ASL store detail routes.
 */
class OceanExtra {

    private $store = null;

    public function __construct() {
        add_action('wp', [$this, 'replace_store_meta_tags']);
    }

    /**
     * Remove Ocean Extra's shortcode-page metadata on individual store URLs.
     */
    public function replace_store_meta_tags() {
        if (!get_query_var('sl-store', false) || !class_exists('Ocean_Extra')) {
            return;
        }

        $this->store = \AgileStoreLocator\Model\Store::get_store_id_via_slug();

        if (!$this->store) {
            return;
        }

        remove_action('wp_head', [\Ocean_Extra::instance(), 'meta_tags'], 1);

        // Yoast and Rank Math own social metadata after Ocean Extra is removed.
        if (defined('WPSEO_VERSION') || defined('RANK_MATH_FILE')) {
            return;
        }

        // Keep the Ocean Extra-compatible store metadata when SEOPress is also
        // active, and suppress its overlapping social tags on this route.
        if (defined('SEOPRESS_VERSION')) {
            $this->suppress_seopress_social_meta();
        }

        add_action('wp_head', [$this, 'output_store_meta_tags'], 1);
    }

    /**
     * Prevent duplicate SEOPress social tags when Ocean Extra owns store meta.
     */
    private function suppress_seopress_social_meta() {
        $filters = [
            'seopress_social_og_type',
            'seopress_social_og_title',
            'seopress_social_og_desc',
            'seopress_social_og_url',
            'seopress_social_og_site_name',
            'seopress_social_og_thumb',
            'seopress_social_twitter_card_summary',
            'seopress_social_twitter_card_title',
            'seopress_social_twitter_card_desc',
            'seopress_social_twitter_card_thumb',
        ];

        foreach ($filters as $filter) {
            add_filter($filter, '__return_empty_string', PHP_INT_MAX);
        }
    }

    /**
     * Output Ocean Extra-compatible metadata for the current ASL store.
     */
    public function output_store_meta_tags() {
        if (!$this->store) {
            return;
        }

        $title       = sanitize_text_field($this->store->title);
        $description = html_entity_decode(
            \AgileStoreLocator\Schema\Slug::get_meta_description_by_store_slug(),
            ENT_QUOTES,
            get_bloginfo('charset') ?: 'UTF-8'
        );
        $url         = \AgileStoreLocator\Schema\Slug::update_canonical_tag('');
        $image       = $this->get_store_image();

        $this->meta_tag('property', 'og:type', 'article');
        $this->meta_tag('property', 'og:title', $title);

        if ($description) {
            $this->meta_tag('property', 'og:description', $description);
        }

        if ($image) {
            $this->meta_tag('property', 'og:image', $image);
        }

        if ($url) {
            $this->meta_tag('property', 'og:url', $url);
        }

        $this->meta_tag('property', 'og:site_name', get_bloginfo('name'));
        $this->meta_tag('name', 'twitter:card', $image ? 'summary_large_image' : 'summary');
        $this->meta_tag('name', 'twitter:title', $title);

        if ($description) {
            $this->meta_tag('name', 'twitter:description', $description);
        }

        if ($image) {
            $this->meta_tag('name', 'twitter:image', $image);
        }
    }

    /**
     * Resolve the store logo used by the store detail page.
     */
    private function get_store_image() {
        if (empty($this->store->logo_id)) {
            return '';
        }

        global $wpdb;

        $path = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT `path` FROM '.ASL_PREFIX.'storelogos WHERE `id` = %d',
                $this->store->logo_id
            )
        );

        return $path ? ASL_UPLOAD_URL.'Logo/'.$path : '';
    }

    /**
     * Print one escaped Open Graph or Twitter metadata tag.
     */
    private function meta_tag($attribute, $property, $content) {
        echo '<meta '.esc_attr($attribute).'="'.esc_attr($property).'" content="'.esc_attr($content).'" />'."\n";
    }
}
