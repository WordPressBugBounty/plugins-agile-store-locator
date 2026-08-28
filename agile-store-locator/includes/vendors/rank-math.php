<?php

// namespace AgileStoreLocator\Vendors;

namespace RankMath\Sitemap\Providers;

defined('ABSPATH') || exit;

/**
 * Implement the MathRank sitemap
 */
class ASLRankMath implements Provider
{
    private static $store = null;
    private static $store_resolved = false;

    /**
     * [get_asl_slug Get the asl slug]
     * @return [type] [description]
     */
    public function get_asl_slug()
    {
        $slug = \AgileStoreLocator\Helper::get_configs('rewrite_slug');

        if (!empty($slug)) {
            return $slug;
        }

        return null;
    }

    /**
     * [handles_type description]
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public function handles_type($type)
    {
        $this->get_asl_slug();

        return $this->get_asl_slug() === $type;
    }

    /**
     * [get_index_links Add the main stores node in the sitemap]
     * @param  [type] $max_entries [description]
     * @return [type]              [description]
     */
    public function get_index_links($max_entries)
    {
        $slug_type = $this->get_asl_slug();

        return ($slug_type) ? [[
            'loc'     => \RankMath\Sitemap\Router::get_base_url($slug_type . '-sitemap.xml'),
            'lastmod' => ''
        ]] : [];
    }

    /**
     * [get_sitemap_links Add the sitemap links]
     * @param  [type] $type         [description]
     * @param  [type] $max_entries  [description]
     * @param  [type] $current_page [description]
     * @return [type]               [description]
     */
    public function get_sitemap_links($type, $max_entries, $current_page)
    {
        $post_type = 'asl_stores';
        $link_urls = [];

        //	Get all the languages
        $stores = \AgileStoreLocator\Model\Store::get_stores(['lang' => '*']);

        $output = '';

        if ($stores) {
            $chf 		= 'weekly';
            $pri 		= 1.0;

            $page_url = apply_filters('wpml_home_url', home_url('/'));

            // replace the double slash
            $page_url = preg_replace('#(?<!:)/+#im', '/', $page_url);

            //  must have a slash in the end
            if (substr($page_url, -1) != '/') {
                $page_url = $page_url . '/';
            }

            //  Get the detail page
            $detail_page = $this->get_asl_slug();

            //	 Loop over the stores
            foreach ($stores as $store) {
                $url = [];

                $url['mod'] = ($store->updated_on) ? $store->updated_on : $store->created_on;
                $url['loc'] = $page_url . $detail_page . '/' . $store->slug . '/';

                if (!empty($url)) {
                    $link_urls[] = $url;
                }
            }
        }

        $links     = $link_urls;

        return $links;
    }

    /**
     * [update_page_title_by_store_slug for updating <title> as store title]
     * @since  4.9.8 [<description>]
     * @param  $title [description]
     */
    public static function update_page_title_by_store_slug($title)
    {
        $store = self::get_store();

        return $store && !empty($store->title)
            ? sanitize_text_field($store->title)
            : $title;
    }

    /**
     * Replace the detail page description with the store description.
     */
    public static function update_page_description_by_store_slug($description)
    {
        $store = self::get_store();

        if (!$store) {
            return $description;
        }

        $store_description = !empty($store->description)
            ? $store->description
            : (!empty($store->description_2) ? $store->description_2 : '');

        if (!$store_description) {
            return '';
        }

        $store_description = html_entity_decode(
            $store_description,
            ENT_QUOTES,
            get_bloginfo('charset') ?: 'UTF-8'
        );

        return trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($store_description, true)));
    }

    /**
     * Return the store detail URL for Open Graph metadata.
     */
    public static function update_opengraph_url_by_store_slug($url)
    {
        return self::get_store()
            ? \AgileStoreLocator\Schema\Slug::update_canonical_tag($url)
            : $url;
    }

    /**
     * Resolve the current store once per request.
     */
    private static function get_store()
    {
        if (!self::$store_resolved) {
            self::$store_resolved = true;

            if (get_query_var('sl-store', false)) {
                self::$store = \AgileStoreLocator\Model\Store::get_store_id_via_slug();
            }
        }

        return self::$store;
    }
}
