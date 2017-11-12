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
			'editablegrid'            => array(
				'used_in' => 'builder',
				'src'     => str_replace( array( 'http:', 'https:' ), '', UniCpo()->plugin_url() )
				             . '/assets/css/vendors/editablegrid.css',
				'deps'    => '',
				'version' => '2.0.1',
				'media'   => 'all'
			),
			'font-awesome'            => array(
				'used_in' => 'builder',
				'src'     => str_replace( array( 'http:', 'https:' ), '', UniCpo()->plugin_url() )
				             . '/assets/css/vendors/font-awesome.min.css',
				'deps'    => '',
				'version' => '4.7.0',
				'media'   => 'all'
			),
			'chosen'                  => array(
				'used_in' => 'builder',
				'src'     => str_replace( array( 'http:', 'https:' ), '', UniCpo()->plugin_url() )
				             . '/assets/css/vendors/chosen.css',
				'deps'    => '',
				'version' => '1.0.0',
				'media'   => 'all'
			),
			'imageselect'             => array(
				'used_in' => 'builder',
				'src'     => str_replace( array( 'http:', 'https:' ), '', UniCpo()->plugin_url() )
				             . '/assets/css/vendors/ImageSelect.css',
				'deps'    => '',
				'version' => '1.8.0',
				'media'   => 'all'
			),
			'jquery-jscrollpane'      => array(
				'used_in' => 'builder',
				'src'     => str_replace( array( 'http:', 'https:' ), '', UniCpo()->plugin_url() )
				             . '/assets/css/vendors/jquery.jscrollpane.css',
				'deps'    => '',
				'version' => '2.0.16',
				'media'   => 'all'
			),
			'query-builder'           => array(
				'used_in' => 'builder',
				'src'     => str_replace( array( 'http:', 'https:' ), '', UniCpo()->plugin_url() )
				             . '/assets/css/vendors/query-builder.default.min.css',
				'deps'    => '',
				'version' => '2.4.3',
				'media'   => 'all'
			),
			'jquery-ui-structure'     => array(
				'used_in' => 'builder',
				'src'     => str_replace( array( 'http:', 'https:' ), '', UniCpo()->plugin_url() )
				             . '/assets/css/vendors/jquery-ui.structure.min.css',
				'deps'    => '',
				'version' => '1.11.4',
				'media'   => 'all'
			),
			'uni-cpo-styles-builder'  => array(
				'used_in' => 'builder',
				'src'     => str_replace( array( 'http:', 'https:' ), '', UniCpo()->plugin_url() )
				             . '/assets/css/uni-cpo-styles-builder.css',
				'deps'    => '',
				'version' => UNI_CPO_VERSION,
				'media'   => 'all'
			),
			'jquery-ui-structure-frontend'     => array(
				'used_in' => 'frontend',
				'src'     => str_replace( array( 'http:', 'https:' ), '', UniCpo()->plugin_url() )
				             . '/assets/css/vendors/jquery-ui.structure.min.css',
				'deps'    => '',
				'version' => '1.11.4',
				'media'   => 'all'
			),
			'uni-cpo-styles-frontend' => array(
				'used_in' => 'frontend',
				'src'     => str_replace( array( 'http:', 'https:' ), '', UniCpo()->plugin_url() )
				             . '/assets/css/uni-cpo-styles-frontend.css',
				'deps'    => '',
				'version' => UNI_CPO_VERSION,
				'media'   => 'all'
			)
		) );
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

		$locale       = get_locale();
		$locale_array = explode( '_', $locale );
		$lang_code    = $locale_array[0];

		$assets_path = str_replace( array( 'http:', 'https:' ), '', UniCpo()->plugin_url() ) . '/assets/';

		// Register any scripts for later use, or used as dependencies
		self::register_script( 'sortable', $assets_path . 'js/vendors/Sortable.min.js', array( 'jquery' ), '1.6.1' );
		self::register_script( 'repeatable-fields', $assets_path . 'js/vendors/repeatable-fields.js', array( 'jquery' ), '1.4.8' );
		self::register_script( 'query-builder', $assets_path . 'js/vendors/query-builder.standalone.min.js', array( 'jquery' ), '2.4.3' );
		self::register_script( 'chosen', $assets_path . 'js/vendors/chosen.jquery.js', array( 'jquery' ), '1.0.0' );
		self::register_script( 'imageselect', $assets_path . 'js/vendors/ImageSelect.jquery.js', array( 'jquery' ), '1.8.0' );
		self::register_script( 'jscrollpane', $assets_path . 'js/vendors/jquery.jscrollpane.min.js', array( 'jquery' ), '2.0.16' );
		self::register_script( 'mousewheel', $assets_path . 'js/vendors/jquery.mousewheel.js', array( 'jquery' ), '3.1.3' );
		self::register_script( 'parsley', $assets_path . 'js/vendors/parsley.min.js', array( 'jquery' ), '2.8.0' );
		self::register_script( 'moment', $assets_path . 'js/vendors/moment.min.js', array( 'jquery' ), '2.19.1' );
		self::register_script( 'uni-cpo-utils', $assets_path . 'js/unicpo-utils.js', array( 'jquery' ) );

		self::register_script( 'uni-cpo-builder', $assets_path . 'js/builder.js',
			array(
				'jquery',
				'backbone',
				'underscore',
				'jquery-ui-core',
				'jquery-ui-widget',
				'jquery-ui-position',
				'jquery-ui-tabs',
				'jquery-ui-tooltip',
				'jquery-touch-punch',
				'sortable',
				'mousewheel',
				'jscrollpane',
				'chosen',
				'imageselect',
				'query-builder',
				'repeatable-fields',
				'parsley',
				'moment',
				'uni-cpo-utils'
			)
		);

		self::register_script( 'uni-cpo-frontend', $assets_path . 'js/unicpo-frontend.js',
			array(
				'jquery',
				'underscore',
				'parsley',
				'jquery-blockui',
				'jquery-ui-core',
				'jquery-ui-widget',
				'jquery-ui-position',
				'jquery-ui-tooltip',
			)
		);

		// for builder
		if ( Uni_Cpo_Product::is_builder_active() ) {

			//
			if ( ! UniCpo()->is_debug() ) {
				show_admin_bar( false );
				remove_action( 'wp_head', '_admin_bar_bump_cb' );
			}

			// main config
			$uni_cpo_cfg = array(
				'ajax_url'  => UniCpo()->ajax_url(),
				'security'  => wp_create_nonce( 'uni_cpo_builder' ),
				'builderId' => UniCpo()->get_builder_id(),
				'product'   => Uni_Cpo_Product::get_product_data(),
				'cpo_data'  => array(
					'var_slug' => UniCpo()->get_var_slug(),
					'nov_slug' => UniCpo()->get_nov_slug(),
					'border_images' => array(
						'solid' => $assets_path . 'images/border-solid.png',
						'dashed' => $assets_path . 'images/border-dashed.png',
						'dotted' => $assets_path . 'images/border-dotted.png',
						'double' => $assets_path . 'images/border-double.png'
					)
				),
				'wholesale' => $all_role_names = uni_cpo_get_all_roles()
			);
			wp_localize_script( 'uni-cpo-builder', 'builderiusCfg', $uni_cpo_cfg );

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
						'cpo_conditional' => array(
							'title' => __( 'Conditional', 'uni-cpo' ),
							'icon'  => 'cpo-conditional',
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
					)
				)
			);
			wp_localize_script( 'uni-cpo-builder', 'builderius_i18n', $uni_cpo_i18n );

			// scripts
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
					if ( in_array( $args['used_in'], array( 'builder', 'both' ) ) ) {
						self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
					}
				}
			}

		} // end scripts for the builder

		// for frontend
		if ( ! Uni_Cpo_Product::is_builder_active() && Uni_Cpo_Product::is_single_product() ) {

			$plugin_settings = get_option( 'uni_cpo_settings_general', UniCpo()->default_settings() );
			$product_data    = Uni_Cpo_Product::get_product_data();
			$zero_price      = uni_cpo_price( '0.00' );

			// main config
			$uni_cpo_cfg = array(
				'ajax_url'         => UniCpo()->ajax_url(),
				'security'         => wp_create_nonce( 'uni_cpo_frontend' ),
				'version'          => UNI_CPO_VERSION,
				'cpo_on'           => ( isset( $product_data['settings_data']['cpo_enable'] )
				                        && 'off' !== $product_data['settings_data']['cpo_enable'] )
					? true : false,
				'calc_on'          => ( isset( $product_data['settings_data']['calc_enable'] )
				                        && 'off' !== $product_data['settings_data']['calc_enable'] )
					? true : false,
				'calc_btn_on'      => ( isset( $product_data['settings_data']['calc_btn_enable'] )
				                        && 'off' !== $product_data['settings_data']['calc_btn_enable'] )
					? true : false,
				'price_selector'   => $plugin_settings['product_price_container'],
				'image_selector'   => $plugin_settings['product_image_container'],
				'options_selector' => '.js-uni-cpo-field',
				'formatted_vars'   => array(),
				'price_vars'       => array(
					'raw_price'         => 0,
					'raw_price_tax_rev' => 0,
					'price'             => $zero_price,
					'price_suffix'      => '',
					'price_discounted'  => 0,
					'raw_total'         => 0,
					'raw_total_tax_rev' => 0,
					'total'             => $zero_price,
					'total_tax_rev'     => $zero_price,
					'total_suffix'      => '',
				),
				'extra_data'       => array( 'order_product' => 'enabled' ),
			);
			wp_localize_script( 'uni-cpo-frontend', 'unicpo', $uni_cpo_cfg );

			$uni_cpo_i18n = apply_filters( 'uni_cpo_i18n_frontend_strings',
				array(
					'calc_text' => esc_html__('Calculating', 'uni-cpo')
				)
			);
			wp_localize_script( 'uni-cpo-frontend', 'unicpo_i18n', $uni_cpo_i18n );

			self::enqueue_script( 'uni-cpo-frontend' );

			// generated file with css styles
			self::add_generated_styles();
			// static permanent file with css styles
			if ( $enqueue_styles = self::get_styles() ) {
				foreach ( $enqueue_styles as $handle => $args ) {
					if ( in_array( $args['used_in'], array( 'frontend', 'both' ) ) ) {
						self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
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

		if ( is_writable( UNI_CPO_CSS_DIR ) ) {
			$css_file_uri = get_option( 'builder_css_cached_for' . $product_data['id'] );

			if ( ! $css_file_uri ) {
				$css_file_uri = self::cache_css_file( $product_data );
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

		$chmod_file = ( 0644 & ~ umask() );
		if ( defined( 'FS_CHMOD_FILE' ) ) {
			$chmod_file = FS_CHMOD_FILE;
		}
		if ( ! $wp_filesystem->put_contents( $css_file, $css, $chmod_file ) ) {
			update_option( 'builder_css_cached_for' . $product_data['id'], '' );
		} else {
			update_option( 'builder_css_cached_for' . $product_data['id'], $css_file_uri );
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
