<?php


namespace AgileStoreLocator\Vendors\Elementor;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed direptly.
}



/**
 * /**
 * Class Agile Store Locator Elementor StoreLocator
 * @since 1.0.0
 */

class StoreLocator extends \Elementor\Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'agile-store-locator-addon';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Store Locator', 'asl_locator' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-map-pin';
	}


	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'asl_locator' ];
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [];
	}


	/**
	 * Check for empty values and return provided default value if required
	 */
	protected function set_default( $value, $default ){
		if( isset($value) && $value!="" ){
			return $value;
		}else{
			return $default;
		}
	}


	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$category_options = [];
		$lang = \AgileStoreLocator\Helper::get_configs( 'locale' ) ? get_locale() : '';
		if ( 'en_US' === $lang ) {
			$lang = '';
		}
		foreach ( (array) \AgileStoreLocator\Model\Category::get_categories( $lang ) as $category ) {
			$category_options[ (string) $category->id ] = $category->category_name;
		}

		$this->start_controls_section(
			'section_appearance_search',
			[
				'label' => __( 'Appearance & Search', 'asl_locator' ),
			]
		);

		$this->add_control(
			'agileStoreLocator_notice',
			[
				'label' => __( '', 'asl_locator' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Choose page-specific options. Leave a field on Use saved setting to inherit the locator settings.', 'asl_locator' ),
				'content_classes' => 'agileStoreLocator_notice',
			]
		);

		$this->add_control( 'asl_pro_options_notice', [
			'type' => \Elementor\Controls_Manager::RAW_HTML,
			'raw' => esc_html__( 'Additional templates and database search modes are available in Pro.', 'asl_locator' ),
		] );

		$this->add_control(
			'template',
			[
				'label' => __( 'Select Template', 'asl_locator' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'default' => '',
				'options' => array(
					'' => esc_html__('Use saved setting','asl_locator'),
					'0' => esc_attr__('Template 0','asl_locator'),
				),
			]
		);

		$this->add_control(
			'search_mode',
			[
				'label' => __( 'Search Mode', 'asl_locator' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'default' => '',
				'options' => array(
					'' => esc_html__('Use saved setting','asl_locator'),
					'automatic' => esc_html__('Address — Automatic','asl_locator'),
					'google_new' => esc_html__('Address — Google Places','asl_locator'),
					'google_legacy' => esc_html__('Address — Google Places (Legacy)','asl_locator'),
					'nominatim' => esc_html__('Address — Nominatim','asl_locator'),
					'geoapify' => esc_html__('Address — Geoapify','asl_locator'),
					'mapbox' => esc_html__('Address — Mapbox','asl_locator'),
					'geocode_enter' => esc_html__('Address — Search on Enter','asl_locator'),
					'disabled' => esc_html__('Address search disabled','asl_locator'),
				),
			]
		);

		$this->add_control( 'category', [
			'label' => __( 'Restrict to Categories', 'asl_locator' ),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'multiple' => true,
			'label_block' => true,
			'default' => [],
			'options' => $category_options,
			'description' => __( 'Select one or more categories. Leave empty to show all stores.', 'asl_locator' ),
		] );

		// Preserve the old field for widgets saved before Search Mode existed.
		$this->add_control( 'search_type', [ 'type' => \Elementor\Controls_Manager::HIDDEN, 'default' => '' ] );
		$this->end_controls_section();

		$this->start_controls_section( 'section_location_display', [ 'label' => __( 'Location & Display', 'asl_locator' ) ] );

		$this->add_control(
			'layout',
			[
				'label' => __( 'Select Layout', 'asl_locator' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'default' => '',
				'options' => array(
					'' => esc_html__('Use saved setting','asl_locator'),
					'0' => esc_attr__('List Format','asl_locator'),
					'1' => esc_attr__('Accordion (States, Cities, Countries)','asl_locator'),
					'2' => esc_attr__('Accordion (Categories)','asl_locator'),
				),
			]
		);

		$this->add_control(
			'distance_control',
			[
				'label' => esc_html__( 'Distance Control', 'asl_locator' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'default' => '',
				'options' => [
					'' => esc_html__( 'Use saved setting', 'asl_locator' ),
					'0' => esc_html__( 'Slider', 'asl_locator' ),
					'1' => esc_html__( 'Dropdown', 'asl_locator' ),
					'2' => esc_html__( 'Boundary Box', 'asl_locator' ),
				],

			]
		);

		$this->add_control( 'prompt_location', [
			'label' => __( 'Geo-Location Dialog', 'asl_locator' ),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'default' => '',
			'options' => [
				'' => esc_html__( 'Use saved setting', 'asl_locator' ),
				'0' => esc_html__( 'Disable', 'asl_locator' ),
				'1' => esc_html__( 'Geo-location Modal', 'asl_locator' ),
				'2' => esc_html__( 'Type your Location Modal', 'asl_locator' ),
				'3' => esc_html__( 'Geolocation On Load', 'asl_locator' ),
				'4' => esc_html__( 'GeoJS IP Service', 'asl_locator' ),
			],
		] );
		$this->add_control( 'distance_unit', [
			'label' => __( 'Distance Unit', 'asl_locator' ),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'default' => '',
			'options' => [ '' => esc_html__( 'Use saved setting', 'asl_locator' ), 'KM' => 'KM', 'Miles' => esc_html__( 'Miles', 'asl_locator' ) ],
		] );
		$this->add_control( 'time_format', [
			'label' => __( 'Time Format', 'asl_locator' ),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'default' => '',
			'options' => [ '' => esc_html__( 'Use saved setting', 'asl_locator' ), '0' => esc_html__( '12 Hours', 'asl_locator' ), '1' => esc_html__( '24 Hours', 'asl_locator' ) ],
		] );



		$this->end_controls_section();


	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$attributes = [];
		$allowed = [
			'template' => ['0'],
			'layout' => ['0', '1', '2'],
			'distance_control' => ['0', '1', '2'],
			'prompt_location' => ['0', '1', '2', '3', '4'],
			'distance_unit' => ['KM', 'Miles'],
			'time_format' => ['0', '1'],
		];
		foreach ( $allowed as $key => $values ) {
			$value = isset( $settings[$key] ) ? (string) $settings[$key] : '';
			if ( '' !== $value && in_array( $value, $values, true ) ) {
				$attributes[] = $key . '="' . $value . '"';
			}
		}

		$search_modes = [
			'automatic' => ['automatic', '4'], 'google_new' => ['google', '4'],
			'google_legacy' => ['google', '0'], 'nominatim' => ['nominatim', '4'],
			'geoapify' => ['geoapify', '4'], 'mapbox' => ['mapbox', '4'],
			'geocode_enter' => ['google', '3'], 'disabled' => ['disabled', '0'],
		];
		$mode = isset( $settings['search_mode'] ) ? (string) $settings['search_mode'] : '';
		if ( isset( $search_modes[$mode] ) ) {
			$attributes[] = 'search_provider="' . $search_modes[$mode][0] . '"';
			$attributes[] = 'search_type="' . $search_modes[$mode][1] . '"';
		} elseif ( isset( $settings['search_type'] ) && in_array( (string) $settings['search_type'], ['0', '3', '4'], true ) ) {
			$attributes[] = 'search_type="' . $settings['search_type'] . '"';
		}

		$categories = isset( $settings['category'] ) && is_array( $settings['category'] ) ? $settings['category'] : [];
		$categories = array_filter( array_map( 'strval', $categories ), 'ctype_digit' );
		if ( $categories ) {
			$attributes[] = 'category="' . implode( ',', array_unique( $categories ) ) . '"';
		}

		$shortcode = '[ASL_STORELOCATOR' . ( $attributes ? ' ' . implode( ' ', $attributes ) : '' ) . ']';
		echo '<div class="elementor-shortcode asl-free-addon">' . $shortcode . '</div>';
	}
}
