<?php
/**
 * Handle frontend scripts
 *
 * @class       Uni_Cpo_Frontend_Scripts
 * @version     1.0.0
 * @package     Uni_Cpo/Classes/
 * @category    Class
 * @author      MooMoo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Uni_Cpo_Frontend_Scripts Class.
 */
class Uni_Cpo_Frontend_Scripts {

	/**
	 * Contains an array of script handles registered by Uni_Cpo.
	 * @var array
	 */
	private static $scripts = array();

	/**
	 * Contains an array of script handles registered by Uni_Cpo.
	 * @var array
	 */
	private static $styles = array();

	/**
	 * Contains an array of modules objects
	 * @var array
	 */
	private static $modules;

	/**
	 * Contains an array of options' class names
	 * @var array
	 */
	private static $options;

	/**
	 * Contains an array of settings objects
	 * @var array
	 */
	private static $settings;

	/**
	 * Hook in methods.
	 */
	public static function init() {
		self::load_modules();
		self::load_options();
		self::load_settings();
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
	}

	/**
	 * Get styles for the frontend.
	 * @return array
	 */
	public static function get_styles() {
		return apply_filters( 'uni_cpo_enqueue_styles', array(
			'uni-cpo-lity-css'        => array(
				'used_in' => array( 'frontend' ),
				'src'     => self::get_asset_url( '/includes/vendors/lity/lity.min.css' ),
				'deps'    => '',
				'version' => '2.3.1',
				'media'   => 'all'
			),
			'editablegrid'            => array(
				'used_in' => array( 'builder' ),
				'src'     => self::get_asset_url( '/includes/vendors/editablegrid/editablegrid.css' ),
				'deps'    => '',
				'version' => '2.0.1',
				'media'   => 'all'
			),
			'uni-cpo-font-awesome'    => array(
				'used_in' => array( 'builder', 'cart', 'frontend' ),
				'src'     => self::get_asset_url( '/includes/vendors/font-awesome/css/fontawesome-all.min.css' ),
				'deps'    => '',
				'version' => '5.0.10',
				'media'   => 'all'
			),
			'chosen'                  => array(
				'used_in' => array( 'builder' ),
				'src'     => self::get_asset_url( '/includes/vendors/chosen/chosen.css' ),
				'deps'    => '',
				'version' => '1.0.0',
				'media'   => 'all'
			),
			'imageselect'             => array(
				'used_in' => array( 'builder' ),
				'src'     => self::get_asset_url( '/includes/vendors/chosen/ImageSelect.css' ),
				'deps'    => '',
				'version' => '1.8.0',
				'media'   => 'all'
			),
			'jquery-jscrollpane'      => array(
				'used_in' => array( 'builder' ),
				'src'     => self::get_asset_url( '/includes/vendors/jscrollpane/jquery.jscrollpane.css' ),
				'deps'    => '',
				'version' => '2.0.16',
				'media'   => 'all'
			),
			'normalize'               => array(
				'used_in' => array( 'builder', 'frontend' ),
				'src'     => self::get_asset_url( '/includes/vendors/range-slider/normalize.css' ),
				'deps'    => '',
				'version' => '3.0.2',
				'media'   => 'all'
			),
			'range-slider'            => array(
				'used_in' => array( 'builder', 'frontend' ),
				'src'     => self::get_asset_url( '/includes/vendors/range-slider/ion.rangeSlider.css' ),
				'deps'    => '',
				'version' => '2.0.3',
				'media'   => 'all'
			),
			'query-builder'           => array(
				'used_in' => array( 'builder' ),
				'src'     => self::get_asset_url( '/includes/vendors/query-builder/query-builder.default.min.css' ),
				'deps'    => '',
				'version' => '2.5.2',
				'media'   => 'all'
			),
			'jquery-ui-structure'     => array(
				'used_in' => array( 'builder', 'frontend' ),
				'src'     => self::get_asset_url( '/includes/vendors/jquery-ui/jquery-ui.structure.min.css' ),
				'deps'    => '',
				'version' => '1.11.4',
				'media'   => 'all'
			),
			'uni-cpo-styles-builder'  => array(
				'used_in' => array( 'builder' ),
				'src'     => self::get_asset_url( '/assets/css/builder.css' ),
				'deps'    => '',
				'version' => UNI_CPO_VERSION,
				'media'   => 'all'
			),
			'flatpickr'               => array(
				'used_in' => array( 'builder', 'frontend', 'cart' ),
				'src'     => self::get_asset_url( '/includes/vendors/flatpickr/flatpickr.css' ),
				'deps'    => '',
				'version' => '4.3.2',
				'media'   => 'all'
			),
			'uni-cpo-styles-frontend' => array(
				'used_in' => array( 'frontend' ),
				'src'     => self::get_asset_url( '/assets/css/frontend.css' ),
				'deps'    => '',
				'version' => UNI_CPO_VERSION,
				'media'   => 'all'
			),
			'uni-cpo-styles-cart'     => array(
				'used_in' => array( 'cart' ),
				'src'     => self::get_asset_url( '/assets/css/cart.css' ),
				'deps'    => '',
				'version' => UNI_CPO_VERSION,
				'media'   => 'all'
			)
		) );
	}

	/**
	 * Get styles other
	 * @return array
	 */
	public static function get_styles_conditional() {
		return apply_filters( 'uni_cpo_enqueue_styles_conditional', array(
			'ion-rangeSlider-skinFlat'   => array(
				'used_in' => array( 'range_slider_style', 'flat' ),
				'src'     => self::get_asset_url( '/includes/vendors/range-slider/ion.rangeSlider.skinFlat.css' ),
				'deps'    => '',
				'version' => '2.0.3',
				'media'   => 'all'
			),
			'ion-rangeSlider-skinModern' => array(
				'used_in' => array( 'range_slider_style', 'modern' ),
				'src'     => self::get_asset_url( '/includes/vendors/range-slider/ion.rangeSlider.skinModern.css' ),
				'deps'    => '',
				'version' => '2.0.3',
				'media'   => 'all'
			),
			'ion-rangeSlider-skinHTML5'  => array(
				'used_in' => array( 'range_slider_style', 'html5' ),
				'src'     => self::get_asset_url( '/includes/vendors/range-slider/ion.rangeSlider.skinHTML5.css' ),
				'deps'    => '',
				'version' => '2.0.3',
				'media'   => 'all'
			),
			'ion-rangeSlider-skinNice'   => array(
				'used_in' => array( 'range_slider_style', 'nice' ),
				'src'     => self::get_asset_url( '/includes/vendors/range-slider/ion.rangeSlider.skinNice.css' ),
				'deps'    => '',
				'version' => '2.0.3',
				'media'   => 'all'
			),
			'ion-rangeSlider-skinSimple' => array(
				'used_in' => array( 'range_slider_style', 'simple' ),
				'src'     => self::get_asset_url( '/includes/vendors/range-slider/ion.rangeSlider.skinSimple.css' ),
				'deps'    => '',
				'version' => '2.0.3',
				'media'   => 'all'
			)
		) );
	}

	/**
	 * Get localizations
	 * @return array
	 */
	public static function get_localizations() {
		return apply_filters( 'uni_cpo_scripts_localizations', array(
				'parsleyjs' => array(
					'defaultMessage' => __( "This value seems to be invalid.", 'uni-cpo' ),
					'type_email'     => __( "This value should be a valid email.", 'uni-cpo' ),
					'type_url'       => __( "This value should be a valid url.", 'uni-cpo' ),
					'type_number'    => __( "This value should be a valid number.", 'uni-cpo' ),
					'type_digits'    => __( "This value should be digits.", 'uni-cpo' ),
					'type_alphanum'  => __( "This value should be alphanumeric.", 'uni-cpo' ),
					'type_integer'   => __( "This value should be a valid integer.", 'uni-cpo' ),
					'notblank'       => __( "This value should not be blank.", 'uni-cpo' ),
					'required'       => __( "This value is required.", 'uni-cpo' ),
					'pattern'        => __( "This value seems to be invalid.", 'uni-cpo' ),
					'min'            => __( "This value should be greater than or equal to %s.", 'uni-cpo' ),
					'max'            => __( "This value should be lower than or equal to %s.", 'uni-cpo' ),
					'range'          => __( "This value should be between %s and %s.", 'uni-cpo' ),
					'minlength'      => __( "This value is too short. It should have %s characters or more.", 'uni-cpo' ),
					'maxlength'      => __( "This value is too long. It should have %s characters or fewer.", 'uni-cpo' ),
					'length'         => __( "This value length is invalid. It should be between %s and %s characters long.", 'uni-cpo' ),
					'mincheck'       => __( "You must select at least %s choices.", 'uni-cpo' ),
					'maxcheck'       => __( "You must select %s choices or fewer.", 'uni-cpo' ),
					'check'          => __( "You must select between %s and %s choices.", 'uni-cpo' ),
					'equalto'        => __( "This value should be the same.", 'uni-cpo' ),
					'dateiso'        => __( "This value should be a valid date (YYYY-MM-DD).", 'uni-cpo' ),
					'minwords'       => __( "This value is too short. It should have %s words or more.", 'uni-cpo' ),
					'maxwords'       => __( "This value is too long. It should have %s words or fewer.", 'uni-cpo' ),
					'words'          => __( "This value length is invalid. It should be between %s and %s words long.", 'uni-cpo' ),
					'gt'             => __( "This value should be greater.", 'uni-cpo' ),
					'gte'            => __( "This value should be greater or equal.", 'uni-cpo' ),
					'lt'             => __( "This value should be less.", 'uni-cpo' ),
					'lte'            => __( "This value should be less or equal.", 'uni-cpo' ),
					'notequalto'     => __( "Must be unique!", 'uni-cpo' )
				),
				'flatpickr' => array(
					'weekdays'    => array(
						'shorthand' => array(
							__( 'Sun', 'uni-cpo' ),
							__( 'Mon', 'uni-cpo' ),
							__( 'Tue', 'uni-cpo' ),
							__( 'Wed', 'uni-cpo' ),
							__( 'Thu', 'uni-cpo' ),
							__( 'Fri', 'uni-cpo' ),
							__( 'Sat', 'uni-cpo' )
						),
						'longhand'  => array(
							__( 'Sunday', 'uni-cpo' ),
							__( 'Monday', 'uni-cpo' ),
							__( 'Tuesday', 'uni-cpo' ),
							__( 'Wednesday', 'uni-cpo' ),
							__( 'Thursday', 'uni-cpo' ),
							__( 'Friday', 'uni-cpo' ),
							__( 'Saturday', 'uni-cpo' )
						),
					),
					'months'      => array(
						'shorthand' => array(
							__( 'Jan', 'uni-cpo' ),
							__( 'Feb', 'uni-cpo' ),
							__( 'Mar', 'uni-cpo' ),
							__( 'Apr', 'uni-cpo' ),
							__( 'May', 'uni-cpo' ),
							__( 'Jun', 'uni-cpo' ),
							__( 'Jul', 'uni-cpo' ),
							__( 'Aug', 'uni-cpo' ),
							__( 'Sep', 'uni-cpo' ),
							__( 'Oct', 'uni-cpo' ),
							__( 'Nov', 'uni-cpo' ),
							__( 'Dec', 'uni-cpo' )
						),
						'longhand'  => array(
							__( 'January', 'uni-cpo' ),
							__( 'February', 'uni-cpo' ),
							__( 'March', 'uni-cpo' ),
							__( 'April', 'uni-cpo' ),
							__( 'May', 'uni-cpo' ),
							__( 'June', 'uni-cpo' ),
							__( 'July', 'uni-cpo' ),
							__( 'August', 'uni-cpo' ),
							__( 'September', 'uni-cpo' ),
							__( 'October', 'uni-cpo' ),
							__( 'November', 'uni-cpo' ),
							__( 'December', 'uni-cpo' )
						),
					),
					'scrollTitle' => __( 'Scroll to increment', 'uni-cpo' ),
					'toggleTitle' => __( 'Click to toggle', 'uni-cpo' ),
				)
			)
		);
	}

	/**
	 * Return asset URL.
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	private static function get_asset_url( $path ) {
		return apply_filters( 'uni_cpo_get_asset_url', plugins_url( $path, UNI_CPO_PLUGIN_FILE ), $path );
	}

	/**
	 * Register a script for use.
	 *
	 * @uses   wp_register_script()
	 * @access private
	 *
	 * @param  string $handle
	 * @param  string $path
	 * @param  string[] $deps
	 * @param  string $version
	 * @param  boolean $in_footer
	 */
	private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = UNI_CPO_VERSION, $in_footer = true ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $path, $deps, $version, $in_footer );
	}

	/**
	 * Register and enqueue a script for use.
	 *
	 * @uses   wp_enqueue_script()
	 * @access private
	 *
	 * @param  string $handle
	 * @param  string $path
	 * @param  string[] $deps
	 * @param  string $version
	 * @param  boolean $in_footer
	 */
	private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = UNI_CPO_VERSION, $in_footer = true ) {
		if ( ! in_array( $handle, self::$scripts ) && $path ) {
			self::register_script( $handle, $path, $deps, $version, $in_footer );
		}
		wp_enqueue_script( $handle );
	}

	/**
	 * Register a style for use.
	 *
	 * @uses   wp_register_style()
	 * @access private
	 *
	 * @param  string $handle
	 * @param  string $path
	 * @param  string[] $deps
	 * @param  string $version
	 * @param  string $media
	 */
	private static function register_style( $handle, $path, $deps = array(), $version = UNI_CPO_VERSION, $media = 'all' ) {
		self::$styles[] = $handle;
		wp_register_style( $handle, $path, $deps, $version, $media );
	}

	/**
	 * Register and enqueue a styles for use.
	 *
	 * @uses   wp_enqueue_style()
	 * @access private
	 *
	 * @param  string $handle
	 * @param  string $path
	 * @param  string[] $deps
	 * @param  string $version
	 * @param  string $media
	 */
	private static function enqueue_style( $handle, $path = '', $deps = array(), $version = UNI_CPO_VERSION, $media = 'all' ) {
		if ( ! in_array( $handle, self::$styles ) && $path ) {
			self::register_style( $handle, $path, $deps, $version, $media );
		}
		wp_enqueue_style( $handle );
	}

	/**
	 * Register/queue frontend scripts.
	 */
	public static function load_scripts() {

		if ( ! did_action( 'before_uni_cpo_init' ) ) {
			return;
		}

		$plugin_settings = UniCpo()->get_settings();
		$localizations   = self::get_localizations();
		$assets_path     = self::get_asset_url( '/assets/' );
		$vendors_path    = self::get_asset_url( '/includes/vendors/' );

		// Register any scripts for later use, or used as dependencies
		self::register_script( 'uni-cpo-lity', $vendors_path . 'lity/lity.min.js', array(), '2.3.1' );
		self::register_script( 'editablegrid', $vendors_path . 'editablegrid/editablegrid.js', array(), '2.0.1' );
		self::register_script( 'editablegrid_charts', $vendors_path . 'editablegrid/editablegrid_charts.js', array(), '2.0.1' );
		self::register_script( 'editablegrid_editors', $vendors_path . 'editablegrid/editablegrid_editors.js', array(), '2.0.1' );
		self::register_script( 'editablegrid_renderers', $vendors_path . 'editablegrid/editablegrid_renderers.js', array(), '2.0.1' );
		self::register_script( 'editablegrid_utils', $vendors_path . 'editablegrid/editablegrid_utils.js', array(), '2.0.1' );
		self::register_script( 'editablegrid_validators', $vendors_path . 'editablegrid/editablegrid_validators.js', array(), '2.0.1' );
		self::register_script( 'repeatable-fields', $vendors_path . 'repeatable-fields/repeatable-fields.js', array( 'jquery' ), '1.4.8' );
		self::register_script( 'query-builder', $vendors_path . 'query-builder/query-builder.standalone.min.js', array( 'jquery' ), '2.5.2' );
		self::register_script( 'chosen', $vendors_path . 'chosen/chosen.jquery.js', array( 'jquery' ), '1.0.0' );
		self::register_script( 'imageselect', $vendors_path . 'chosen/ImageSelect.jquery.js', array( 'jquery' ), '1.8.0' );
		self::register_script( 'jscrollpane', $vendors_path . 'jscrollpane/jquery.jscrollpane.min.js', array( 'jquery' ), '2.0.16' );
		self::register_script( 'mousewheel', $vendors_path . 'mousewheel/jquery.mousewheel.js', array( 'jquery' ), '3.1.3' );
		self::register_script( 'parsleyjs', $vendors_path . 'parsleyjs/parsley.min.js', array( 'jquery' ), '2.8.0' );
		self::register_script( 'parsley-localization', $vendors_path . 'parsleyjs/i18n/en.js', array( 'parsleyjs' ), '2.8.0' );
		self::register_script( 'moment', $vendors_path . 'moment/moment.min.js', array( 'jquery' ), '2.19.1' );
		self::register_script( 'flatpickr', $vendors_path . 'flatpickr/flatpickr.js', array( 'jquery' ), '4.3.2' );
		self::register_script( 'rangeSlider', $vendors_path . 'range-slider/ion.rangeSlider.min.js', array( 'jquery' ), '2.2.0' );
		self::register_script( 'uni-cpo-utils', $assets_path . 'js/utils.js', array( 'jquery' ), UNI_CPO_VERSION );

		self::register_script( 'uni-cpo-builder', $assets_path . 'js/builder.js',
			array(
				'jquery',
				'backbone',
				'underscore',
				'jquery-ui-core',
				'jquery-ui-widget',
				'jquery-ui-position',
				'jquery-ui-mouse',
				'jquery-ui-tabs',
				'jquery-ui-tooltip',
				'jquery-touch-punch',
				'jquery-ui-sortable',
				'mousewheel',
				'jscrollpane',
				'chosen',
				'imageselect',
				'query-builder',
				'repeatable-fields',
				'editablegrid',
				'editablegrid_charts',
				'editablegrid_editors',
				'editablegrid_renderers',
				'editablegrid_utils',
				'editablegrid_validators',
				'parsleyjs',
				'parsley-localization',
				'moment',
				'flatpickr',
				'rangeSlider',
				'uni-cpo-utils'
			),
			UNI_CPO_VERSION
		);

		self::register_script( 'uni-cpo-frontend', $assets_path . 'js/frontend.js',
			array(
				'jquery',
				'underscore',
				'parsleyjs',
				'parsley-localization',
				'jquery-blockui',
				'jquery-ui-core',
				'jquery-ui-widget',
				'jquery-ui-position',
				'jquery-ui-tooltip',
				'uni-cpo-lity',
				'plupload',
				'moment',
				'flatpickr',
				'rangeSlider',
				'woocommerce',
				'wc-add-to-cart'
			),
			UNI_CPO_VERSION
		);

		self::register_script( 'uni-cpo-cart', $assets_path . 'js/cart.js',
			array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-widget',
				'jquery-ui-position',
				'parsleyjs',
				'parsley-localization',
				'jquery-blockui',
				'moment',
				'flatpickr',
			),
			UNI_CPO_VERSION
		);

		// JS scripts and styles used in builder mode
		if ( Uni_Cpo_Product::is_builder_active() ) {

			//
			if ( ! UniCpo()->is_debug() ) {
				show_admin_bar( false );
				remove_action( 'wp_head', '_admin_bar_bump_cb' );
			}

			$product_data = Uni_Cpo_Product::get_product_data();
			$product      = wc_get_product( $product_data['id'] );

			$css_array_price_tag       = explode( ',', $plugin_settings['product_price_container'] );
			$css_array_price_tag_final = array_filter(
				apply_filters( 'uni_cpo_price_selector', $css_array_price_tag, $product_data ),
				function ($el) {
					return ! empty( $el );
				}
			);

			$css_array_image_tag       = explode( ',', $plugin_settings['product_image_container'] );
			$css_array_image_tag_final = array_filter(
				apply_filters( 'uni_cpo_image_selector', $css_array_image_tag, $product_data ),
				function ($el) {
					return ! empty( $el );
				}
			);

			$css_array_thumbs_tag       = explode( ',', $plugin_settings['product_thumbnails_container'] );
			$css_array_thumbs_tag_final = array_filter(
				apply_filters( 'uni_cpo_thumbs_selector', $css_array_thumbs_tag, $product_data ),
				function ($el) {
					return ! empty( $el );
				}
			);

			// main config
			$uni_cpo_cfg = array(
				'ajax_url'           => UniCpo()->ajax_url(),
				'security'           => wp_create_nonce( 'uni_cpo_builder' ),
				'builderId'          => UniCpo()->get_builder_id(),
				'product'            => $product_data,
				'range_slider_style' => $plugin_settings['range_slider_style'],
				'cpo_data'           => array(
					'var_slug'      => UniCpo()->get_var_slug(),
					'nov_slug'      => UniCpo()->get_nov_slug(),
					'border_images' => array(
						'solid'  => $assets_path . 'images/border-solid.png',
						'dashed' => $assets_path . 'images/border-dashed.png',
						'dotted' => $assets_path . 'images/border-dotted.png',
						'double' => $assets_path . 'images/border-double.png'
					),
					'other_vars'    => array(
						'quantity',
						'currency',
						'currency_code',
						'raw_price',
						'raw_price_tax_rev',
						'price',
						'price_prefix',
						'price_suffix',
						'raw_price_discounted',
						'price_discounted',
						'raw_total',
						'raw_total_discounted',
						'raw_total_tax_rev',
						'total',
						'total_discounted',
						'total_tax_rev',
						'total_suffix',
					)
				),
				'wholesale'          => uni_cpo_get_all_roles(),
				'regular_price'      => $product->get_regular_price(),
				'price_selector'     => implode( ',', $css_array_price_tag_final ),
				'image_selector'     => implode( ',', $css_array_image_tag_final ),
				'thumbs_selector'    => implode( ',', $css_array_thumbs_tag_final ),
			);
			wp_localize_script( 'uni-cpo-builder', 'builderiusCfg', $uni_cpo_cfg );

			wp_localize_script( 'parsleyjs', 'uni_parsley_loc', $localizations['parsleyjs'] );

			$uni_cpo_modules = [];
			// build an array of modules based on registered modules
			if ( ! empty( self::$modules ) && is_array( self::$modules ) ) {
				foreach ( self::$modules as $class_name ) {
					$type                               = call_user_func( array( $class_name, 'get_type' ) );
					$cfg_name                           = 'builderius_mod_' . $type;
					$module_cfg                         = call_user_func( array( $class_name, 'get_settings' ) );
					$uni_cpo_modules['module'][ $type ] = array(
						'title' => call_user_func( array( $class_name, 'get_title' ) ),
						'cfg'   => $cfg_name
					);
					wp_localize_script( 'uni-cpo-builder', $cfg_name, $module_cfg );
				}
			}
			// build an array of modules based on registered options
			if ( ! empty( self::$options ) && is_array( self::$options ) ) {
				foreach ( self::$options as $class_name ) {
					$type                               = call_user_func( array( $class_name, 'get_type' ) );
					$cfg_name                           = 'builderius_mod_' . $type;
					$module_cfg                         = call_user_func( array( $class_name, 'get_settings' ) );
					$uni_cpo_modules['option'][ $type ] = array(
						'title'        => call_user_func( array( $class_name, 'get_title' ) ),
						'cfg'          => $cfg_name,
						'special_vars' => call_user_func( array( $class_name, 'get_special_vars' ) ),
						'filter_data'  => call_user_func( array( $class_name, 'get_filter_data' ) )
					);
					wp_localize_script( 'uni-cpo-builder', $cfg_name, $module_cfg );
				}
			}
			wp_localize_script( 'uni-cpo-builder', 'builderiusModules', $uni_cpo_modules );

			$setting_types = uni_cpo_get_setting_types();
			wp_localize_script( 'uni-cpo-builder', 'builderiusSettings', $setting_types );

			// strings for translation
			$uni_cpo_i18n = apply_filters( 'uni_cpo_i18n_builder_strings',
				array(
					'panel'           => array(
						'smart_search' => __( 'Smart Search', 'uni-cpo' ),
						'groups'       => array(
							//'rows'    => __( 'Row Layouts', 'uni-cpo' ),
							'module' => __( 'Basic Modules', 'uni-cpo' ),
							'option' => __( 'Options', 'uni-cpo' )
						)
					),
					'settings_groups' => array(
						'general'         => array(
							'title' => __( 'General', 'uni-cpo' ),
							'icon'  => 'general',
						),
						'style'           => array(
							'title' => __( 'Style', 'uni-cpo' ),
							'icon'  => 'style',
						),
						'advanced'        => array(
							'title' => __( 'Advanced', 'uni-cpo' ),
							'icon'  => 'advanced',
						),
						'cpo_general'     => array(
							'title' => __( 'Main', 'uni-cpo' ),
							'icon'  => 'cpo-main',
						),
						'cpo_suboptions'  => array(
							'title' => __( 'Suboptions', 'uni-cpo' ),
							'icon'  => 'cpo-suboptions',
						),
						'cpo_matrix'      => array(
							'title' => __( 'Matrix', 'uni-cpo' ),
							'icon'  => 'cpo-suboptions',
						),
						'cpo_rules'       => array(
							'title' => __( 'Rules', 'uni-cpo' ),
							'icon'  => 'cpo-suboptions',
						),
						'cpo_conditional' => array(
							'title' => __( 'Conditional', 'uni-cpo' ),
							'icon'  => 'cpo-conditional',
						),
						'cpo_validation'  => array(
							'title' => __( 'Validation', 'uni-cpo' ),
							'icon'  => 'cpo-main',
						),
					),
					'modal'           => array(
						'delete'     => __( 'Delete', 'uni-cpo' ),
						'save'       => __( 'Save', 'uni-cpo' ),
						'sync'       => __( 'Sync', 'uni-cpo' ),
						'cancel'     => __( 'Cancel', 'uni-cpo' ),
						'suggestion' => __( 'Suggestion', 'uni-cpo' )
					),
					'overlay'         => array(
						'row'       => __( 'Row', 'uni-cpo' ),
						'column'    => __( 'Column', 'uni-cpo' ),
						'module'    => __( 'Module', 'uni-cpo' ),
						'move'      => __( 'Move Row', 'uni-cpo' ),
						'settings'  => __( 'Settings', 'uni-cpo' ),
						'duplicate' => __( 'Duplicate', 'uni-cpo' ),
						'remove'    => __( 'Remove', 'uni-cpo' )
					),
					'upload_btn'      => __( 'Upload', 'uni-cpo' ),
					'select_file'     => __( 'Select file', 'uni-cpo' ),
					'radio_empty'     => __( 'Please add suboptions', 'uni-cpo' ),
					'matrix_head'     => __( '2nd var/1st var', 'uni-cpo' ),
					'none'            => __( '- None -', 'uni-cpo' ),
					'flatpickr'       => $localizations['flatpickr'],
					'pro'             => __( 'Pro Version Feature', 'uni-cpo' ),
					'misconfig'       => array(
						'no_form'      => __( 'No cart form found! Check WooCommerce hooks integration with your theme', 'uni-cpo' ),
						'many_forms'   => __( 'Too many cart forms found! Uni CPO works on single product page only!', 'uni-cpo' ),
						'free'         => __( 'Regular price is not set. Price is required (even "1" works)!', 'uni-cpo' ),
						'no_price_tag' => __( 'It seems that price tag selector is wrong, please update!', 'uni-cpo' ),
						'no_image_tag' => __( 'It seems that image tag selector is wrong, please update!', 'uni-cpo' )
					)
				)
			);
			wp_localize_script( 'uni-cpo-builder', 'builderius_i18n', $uni_cpo_i18n );

			if ( ! empty( $plugin_settings['gmap_api_key'] ) ) {
				wp_enqueue_script(
					'google-map',
					'https://maps.googleapis.com/maps/api/js?key=' . trim( $plugin_settings['gmap_api_key'] ),
					array(),
					false,
					1
				);
			}

			// media uploader
			wp_enqueue_media();

			wp_enqueue_script(
				'iris',
				admin_url( 'js/iris.min.js' ),
				array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
				false,
				1
			);

			wp_enqueue_script(
				'wp-color-picker',
				admin_url( 'js/color-picker.min.js' ),
				array( 'iris' ),
				false,
				1
			);
			$colorpicker_l10n = array(
				'clear'         => __( 'Clear', 'uni-cpo' ),
				'defaultString' => __( 'Default', 'uni-cpo' ),
				'pick'          => __( 'Select Color', 'uni-cpo' ),
				'current'       => __( 'Current Color', 'uni-cpo' ),
			);
			wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );

			self::enqueue_script( 'uni-cpo-builder' );

			// common js templates
			Uni_Cpo_Templates::init();

			// outputs modules' js templates
			if ( ! empty( self::$modules ) && is_array( self::$modules ) ) {
				foreach ( self::$modules as $class_name ) {
					add_action( 'wp_footer', array( $class_name, 'js_template' ), 10 );
				}
			}

			// outputs options' js templates
			if ( ! empty( self::$options ) && is_array( self::$options ) ) {
				foreach ( self::$options as $class_name ) {
					add_action( 'wp_footer', array( $class_name, 'js_template' ), 10 );
				}
			}

			// settings templates
			if ( ! empty( self::$settings ) && is_array( self::$settings ) ) {
				foreach ( self::$settings as $class_name ) {
					new $class_name();
				}
			}

			wp_enqueue_style( 'wp-color-picker' );

			// CSS Styles to be used in builder
			if ( $enqueue_styles = self::get_styles() ) {
				foreach ( $enqueue_styles as $handle => $args ) {
					if ( count( array_intersect( $args['used_in'], array( 'builder' ) ) ) > 0 ) {
						self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
					}
				}
			}

		} // end scripts for the builder


		// JS scripts and styles used in frontend on the product page
		if ( ! Uni_Cpo_Product::is_builder_active() && Uni_Cpo_Product::is_single_product() ) {

			$product_data = Uni_Cpo_Product::get_product_data();
			$product      = wc_get_product( $product_data['id'] );
			$zero_price   = uni_cpo_price( '0.00' );
			if ( UniCpo()->is_pro() ) {
				$price_suffix       = ( ! empty( $product_data['settings_data']['price_suffix'] ) ) ? $product_data['settings_data']['price_suffix'] : '';
				$price_postfix      = ( ! empty( $product_data['settings_data']['price_postfix'] ) ) ? $product_data['settings_data']['price_postfix'] : '';
				$raw_starting_price = ( ! empty( $product_data['settings_data']['starting_price'] ) )
					? $product_data['settings_data']['starting_price']
					: 0;
			} else {
				$price_suffix       = '';
				$price_postfix      = '';
				$raw_starting_price = 0;
			}
			$starting_price = uni_cpo_price( $raw_starting_price );
			$currency       = get_woocommerce_currency();

			$css_array_price_tag       = explode( ',', $plugin_settings['product_price_container'] );
			$css_array_price_tag_final = array_filter(
				apply_filters( 'uni_cpo_price_selector', $css_array_price_tag, $product_data ),
				function ($el) {
					return ! empty( $el );
				}
			);

			$css_array_image_tag       = explode( ',', $plugin_settings['product_image_container'] );
			$css_array_image_tag_final = array_filter(
				apply_filters( 'uni_cpo_image_selector', $css_array_image_tag, $product_data ),
				function ($el) {
					return ! empty( $el );
				}
			);

			$css_array_thumbs_tag       = explode( ',', $plugin_settings['product_thumbnails_container'] );
			$css_array_thumbs_tag_final = array_filter(
				apply_filters( 'uni_cpo_thumbs_selector', $css_array_thumbs_tag, $product_data ),
				function ($el) {
					return ! empty( $el );
				}
			);

			$price_vars = array(
				'currency'             => get_woocommerce_currency_symbol( $currency ),
				'currency_code'        => $currency,
				'raw_price'            => 0,
				'raw_price_tax_rev'    => 0,
				'price'                => $zero_price,
				'price_tax_suffix'     => $product->get_price_suffix( $raw_starting_price ),
				'raw_price_discounted' => 0,
				'price_discounted'     => 0,
				'price_suffix'         => $price_suffix,
				'price_postfix'        => $price_postfix,
				'starting_price'       => $starting_price,
				'raw_total'            => 0,
				'raw_total_discounted' => 0,
				'raw_total_tax_rev'    => 0,
				'total'                => $zero_price,
				'total_discounted'     => $zero_price,
				'total_tax_rev'        => $zero_price,
				'total_tax_suffix'     => $product->get_price_suffix( 0 ),
			);

			// main config
			$uni_cpo_cfg = array(
				'ajax_url'                => UniCpo()->ajax_url(),
				'security'                => wp_create_nonce( 'uni_cpo_frontend' ),
				'version'                 => UNI_CPO_VERSION,
				'cpo_on'                  => ( 'off' !== $product_data['settings_data']['cpo_enable'] ) ? true : false,
				'calc_on'                 => ( 'off' !== $product_data['settings_data']['calc_enable'] ) ? true : false,
				'calc_btn_on'             => ( 'off' !== $product_data['settings_data']['calc_btn_enable'] ) ? true : false,
				'reset_form_btn_on'       => ( 'off' !== $product_data['settings_data']['reset_form_btn'] ) ? true : false,
				'layered_on'              => ( 'off' !== $product_data['settings_data']['layered_image_enable'] ) ? true : false,
				'imagify_on'              => ( 'off' !== $product_data['settings_data']['imagify_enable'] ) ? true : false,
				'silent_validation_on'    => ( 'off' !== $product_data['settings_data']['silent_validation_on'] ) ? true : false,
				'taxable'                 => $product->is_taxable(),
				'pid'                     => $product_data['id'],
				'product_image_id'        => $product_data['post_thumb_id'],
				'ajax_add_to_cart'        => Uni_Cpo_Product::is_ajax_add_to_cart( $product_data['id'] ),
				'price_selector'          => implode( ', ', $css_array_price_tag_final ),
				'image_selector'          => implode( ', ', $css_array_image_tag_final ),
				'thumbs_selector'         => implode( ', ', $css_array_thumbs_tag_final ),
				'max_file_size'           => apply_filters( 'uni_cpo_max_file_size', $plugin_settings['max_file_size'], $product_data ),
				'mime_types'              => apply_filters( 'uni_cpo_mime_types', str_replace( ' ', '', $plugin_settings['mime_type'] ), $product_data ),
				'options_selector_change' => apply_filters( 'uni_cpo_options_selector_change', '.cart .input-text.qty, .js-uni-cpo-field:not(.js-uni-cpo-field-datepicker, .js-uni-cpo-field-range_slider, .js-uni-cpo-field-matrix)', $product_data ),
				'options_selector'        => apply_filters( 'uni_cpo_options_selector', '.js-uni-cpo-field', $product_data ),
				'formatted_vars'          => array(),
				'nice_names_vars'         => array(),
				'price_vars'              => $price_vars,
				'extra_data'              => apply_filters( 'uni_cpo_extra_data', array( 'order_product' => 'enabled' ), $product_data )
			);
			wp_localize_script( 'uni-cpo-frontend', 'unicpo', $uni_cpo_cfg );

			$all_option_data = uni_cpo_get_options_data_for_frontend( $product_data['id'] );

			wp_localize_script( 'uni-cpo-frontend', 'unicpoAllOptions', $all_option_data );

			$uni_cpo_i18n = apply_filters( 'uni_cpo_i18n_frontend_strings',
				array(
					'calc_text'     => __( 'Calculating', 'uni-cpo' ),
					'max_file_size' => __( 'This file should not be larger than %s Kb', 'uni-cpo' ),
					'mime_type'     => __( 'File of this type is not allowed', 'uni-cpo' ),
					'added_to_cart' => __( 'The product has been added successfully!', 'uni-cpo' ),
					'flatpickr'     => $localizations['flatpickr']
				)
			);
			wp_localize_script( 'uni-cpo-frontend', 'unicpo_i18n', $uni_cpo_i18n );

			wp_localize_script( 'parsleyjs', 'uni_parsley_loc', $localizations['parsleyjs'] );

			if ( ! empty( $plugin_settings['gmap_api_key'] ) ) {
				wp_enqueue_script(
					'google-map',
					'https://maps.googleapis.com/maps/api/js?key=' . trim( $plugin_settings['gmap_api_key'] ),
					array(),
					false,
					1
				);
			}

			wp_enqueue_script(
				'iris',
				admin_url( 'js/iris.min.js' ),
				array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
				false,
				1
			);

			wp_enqueue_script(
				'wp-color-picker',
				admin_url( 'js/color-picker.min.js' ),
				array( 'iris' ),
				false,
				1
			);
			$colorpicker_l10n = array(
				'clear'         => __( 'Clear', 'uni-cpo' ),
				'defaultString' => __( 'Default', 'uni-cpo' ),
				'pick'          => __( 'Select Color', 'uni-cpo' ),
				'current'       => __( 'Current Color', 'uni-cpo' ),
			);
			wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );

			self::enqueue_script( 'uni-cpo-frontend' );

			// generated file with css styles
			self::add_generated_styles();

			// static permanent file with css styles
			if ( $enqueue_styles = self::get_styles() ) {
				foreach ( $enqueue_styles as $handle => $args ) {
					if ( count( array_intersect( $args['used_in'], array( 'frontend' ) ) ) > 0 ) {
						self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
					}
				}
			}

			if ( $uni_cpo_cfg['cpo_on'] && $uni_cpo_cfg['calc_on'] ) {
				$price_selector = $uni_cpo_cfg['price_selector'];
				$custom_css     = "$price_selector {
                    display:none;
                }
                .woocommerce-price-suffix {
                    display:none;
                }";
				wp_add_inline_style( 'uni-cpo-styles-frontend', $custom_css );
			}
		}

		// JS scripts and styles to be used in Cart page
		if ( function_exists( 'is_cart' ) && is_cart() ) {
			wp_localize_script( 'parsleyjs', 'uni_parsley_loc', $localizations['parsleyjs'] );

			$uni_cpo_cart = apply_filters( 'uni_cpo_cart_frontend_strings',
				array(
					'cart_url' => apply_filters( 'woocommerce_add_to_cart_redirect', wc_get_cart_url() )
				)
			);
			wp_localize_script( 'uni-cpo-cart', 'unicpo_cart', $uni_cpo_cart );

			$uni_cpo_i18n = apply_filters( 'uni_cpo_cart_i18n_frontend_strings',
				array(
					'flatpickr' => $localizations['flatpickr']
				)
			);
			wp_localize_script( 'uni-cpo-cart', 'unicpo_cart_i18n', $uni_cpo_i18n );

			self::enqueue_script( 'uni-cpo-cart' );

			if ( $enqueue_styles = self::get_styles() ) {
				foreach ( $enqueue_styles as $handle => $args ) {
					if ( count( array_intersect( $args['used_in'], array( 'cart' ) ) ) > 0 ) {
						self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
					}
				}
			}
		}

		// Conditionally loaded styles based on global plugin's settings
		if ( $enqueue_styles_conditional = self::get_styles_conditional() ) {
			$plugin_settings_used = array( 'range_slider_style' );
			foreach ( $plugin_settings_used as $setting_name ) {
				if ( ! empty( $plugin_settings[ $setting_name ] ) ) {
					foreach ( $enqueue_styles_conditional as $handle => $args ) {
						if ( count( array_intersect( $args['used_in'], array(
								$setting_name,
								$plugin_settings[ $setting_name ]
							) ) ) > 1 ) {
							self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
						}
					}
				}
			}
		}

	}

	private static function add_generated_styles() {

		if ( ! is_dir( UNI_CPO_CSS_DIR ) ) {
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			WP_Filesystem();
			global $wp_filesystem;
			$wp_filesystem->mkdir( UNI_CPO_CSS_DIR );
		}

		$product_data = Uni_Cpo_Product::get_product_data();

		if ( 'on' !== $product_data['settings_data']['cpo_enable'] ) {
			return;
		}

		if ( is_writable( UNI_CPO_CSS_DIR ) ) {
			$css_file_uri = get_option( 'builder_css_cached_for' . $product_data['id'] );

			if ( ! $css_file_uri ) {
				$css_file_uri = self::cache_css_file( $product_data );
			}

			if ( is_ssl() ) {
				$css_file_uri = str_replace( array( 'http:', 'https:' ), 'https:', $css_file_uri );
			}

			wp_enqueue_style(
				'uni-cpo-generated-css-' . $product_data['id'],
				$css_file_uri,
				array(),
				null
			);

		} else {
			add_action(
				'wp_head',
				function () use ( $product_data ) {
					echo '<style>' . self::cache_css_file( $product_data, true ) . '</style>';
				},
				99
			);
		}

	}

	private static function cache_css_file( $product_data, $return_raw_styles = false ) {
		global $blog_id;
		$multisite_suffix = ( is_multisite() ) ? '-blog-' . $blog_id : '';
		$css_file         = UNI_CPO_CSS_DIR . '/cpo-css-product-' . $product_data['id'] . $multisite_suffix . '.css';
		$css_file_uri     = UNI_CPO_CSS_URI . '/cpo-css-product-' . $product_data['id'] . $multisite_suffix . '.css';

		$css = "/**\n";
		$css .= " * This file has been created automatically by Uni CPO plugin\n";
		$css .= " * Last modified time: " . date( 'M d Y, h:s:i' ) . "\n";
		$css .= " */\n\n\n";
		if ( ! empty( $product_data['content'] ) && is_array( $product_data['content'] ) ) {
			foreach ( $product_data['content'] as $row_key => $row_data ) {
				$row_class = call_user_func( array(
					UniCpo()->module_factory,
					'get_classname_from_module_type'
				), $row_data['type'] );
				$css       .= call_user_func( array( $row_class, 'get_css' ), $row_data );
				$css       .= "\n\n";

				// columns
				if ( ! empty( $row_data['columns'] ) ) {
					foreach ( $row_data['columns'] as $column_key => $column_data ) {
						$column_class = call_user_func( array(
							UniCpo()->module_factory,
							'get_classname_from_module_type'
						), $column_data['type'] );
						$css          .= call_user_func( array( $column_class, 'get_css' ), $column_data );
						$css          .= "\n\n";

						// modules
						if ( ! empty( $column_data['modules'] ) ) {
							foreach ( $column_data['modules'] as $module_key => $module_data ) {
								if ( 'option' === $module_data['obj_type'] ) {
									$module_class = call_user_func( array(
										UniCpo()->option_factory,
										'get_classname_from_option_type'
									), $module_data['type'] );
								} else {
									$module_class = call_user_func( array(
										UniCpo()->module_factory,
										'get_classname_from_module_type'
									), $module_data['type'] );
								}
								if ( class_exists( $module_class ) ) {
									$css .= call_user_func( array( $module_class, 'get_css' ), $module_data );
								}
								$css .= "\n\n";
							}
						}
					}
				}
			}
		}

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		global $wp_filesystem;
		WP_Filesystem();

		$chmod_file = ( 0644 & ~umask() );
		if ( defined( 'FS_CHMOD_FILE' ) ) {
			$chmod_file = FS_CHMOD_FILE;
		}
		if ( ! $wp_filesystem->put_contents( $css_file, $css, $chmod_file ) ) {
			update_option( 'builder_css_cached_for' . $product_data['id'], '', false );
		} else {
			update_option( 'builder_css_cached_for' . $product_data['id'], $css_file_uri, false );
		}

		if ( $return_raw_styles ) {
			return $css;
		} else {
			return $css_file_uri;
		}
	}

	/**
	 * Load registered types of modules
	 *
	 * @since 4.0.0
	 */
	private static function load_modules() {
		$module_types = uni_cpo_get_module_types();
		// an array of names of classes of the all available modules
		foreach ( $module_types as $module_type ) {

			$class_name = self::get_classname_from_object_type( 'module', $module_type );

			if ( class_exists( $class_name ) && is_subclass_of( $class_name, 'Uni_Cpo_Module_Interface' ) ) {
				self::$modules[] = $class_name;
			}
		}
	}

	/**
	 * Loads names of classes for options
	 *
	 * @since 4.0.0
	 */
	private static function load_options() {
		$option_types = uni_cpo_get_option_types();
		// an array of names of classes of the all available options
		foreach ( $option_types as $option_type ) {

			$class_name = self::get_classname_from_object_type( 'option', $option_type );

			if ( class_exists( $class_name ) && is_subclass_of( $class_name, 'Uni_Cpo_Option_Interface' ) ) {
				self::$options[] = $class_name;
			}
		}
	}

	/**
	 * Loads names of classes for settings
	 *
	 * @since 4.0.0
	 */
	private static function load_settings() {
		$setting_types = uni_cpo_get_setting_types();
		// an array of names of classes of the all available settings
		foreach ( $setting_types as $setting_type ) {

			$class_name = self::get_classname_from_object_type( 'setting', $setting_type );

			if ( class_exists( $class_name ) && is_subclass_of( $class_name, 'Uni_Cpo_Setting_Interface' ) ) {
				self::$settings[] = $class_name;
			}
		}
	}

	/**
	 * Create a Uni Cpo coding standards compliant class name.
	 *
	 * @param  string $option_type
	 *
	 * @return string|false
	 */
	public static function get_classname_from_object_type( $object_type, $type ) {
		return $type ? 'Uni_Cpo_' . ucfirst( $object_type ) . '_'
		               . implode( '_', array_map( 'ucfirst', explode( '_', $type ) ) ) : false;
	}

}

Uni_Cpo_Frontend_Scripts::init();
