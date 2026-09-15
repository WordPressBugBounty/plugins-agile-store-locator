<?php

namespace AgileStoreLocator\Vendors\Elementor;


use AgileStoreLocator\Vendors\Elementor\Widget;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Agile Store Locator Elementor Addon
 */
class Addon {


    /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string    $AgileStoreLocator    The ID of this plugin.
   */
    protected $AgileStoreLocator;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string    $version    The current version of this plugin.
   */
    protected $version;

    /**
     * Agile Store Locator Elementor Addon constructor.
     */
    public function __construct( $AgileStoreLocator, $version ) {

        $this->AgileStoreLocator = $AgileStoreLocator;
        $this->version = $version;

        add_action( 'elementor/widgets/register', array( $this, 'widgets_registered' ) );

        add_action( 'elementor/frontend/after_enqueue_styles', array($this, 'asl_ele_editor_style' ) );
    }

    /**
     * Register widget
     */
    public function widgets_registered( $widgets_manager ) {

        $widgets_manager->register(new StoreLocator());
        $widgets_manager->register(new StoreCards());
        $widgets_manager->register(new SearchWidget());
        $widgets_manager->register(new StoreDetail());
    }

    /**
     * Register bootstrap file for elementor Editor view
     */
    public function asl_ele_editor_style() {

        wp_register_style( $this->AgileStoreLocator.'-sl-bootstrap', ASL_URL_PATH.'public/css/sl-bootstrap.css', array(), $this->version, 'all');
        wp_enqueue_style( $this->AgileStoreLocator.'-sl-bootstrap' );

    }
}
