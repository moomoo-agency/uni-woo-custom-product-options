<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Uni_Cpo_Plugin_Settings {
	private $dir;
	private $file;
	private $assets_dir;
	private $assets_url;
	private $settings_base;
	private $settings;

	public function __construct( $file ) {
		$this->file          = $file;
		$this->dir           = dirname( $this->file );
		$this->assets_dir    = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url    = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
		$this->settings_base = 'uni_cpo_settings_general';

		// Initialise settings
		add_action( 'admin_init', array( $this, 'init' ) );

		// Register plugin settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->file ), array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item() {
		$page = add_submenu_page(
			'woocommerce',
			__( 'Uni CPO Settings', 'uni-cpo' ),
			__( 'Uni CPO Settings', 'uni-cpo' ),
			'manage_woocommerce',
			'uni-cpo-settings',
			array( $this, 'settings_page' )
		);
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );

	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets() {
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );
		wp_enqueue_media();
		wp_register_script(
			'uni-cpo-admin-js',
			$this->assets_url . 'js/uni-cpo-admin.js',
			array( 'farbtastic', 'jquery' ),
			'1.0.0'
		);
		wp_enqueue_script( 'uni-cpo-admin-js' );
	}

	/**
	 * Add settings link to plugin list table
	 *
	 * @param  array $links Existing links
	 *
	 * @return array        Modified links
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=uni-cpo-settings">' . __( 'Settings', 'uni-cpo' ) . '</a>';
		array_push( $links, $settings_link );

		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {

		$settings['standard'] = array(
			'title'       => __( 'Standard', 'uni-cpo' ),
			'description' => '',
			'fields'      => array(
				array(
					'id'          => 'product_price_container',
					'label'       => __( 'Custom selector (id/class) for a product price html tag', 'uni-cpo' ),
					'description' => __( 'By default, the selector for a product price html tag is ".summary.entry-summary .price > .amount, .summary.entry-summary .price ins .amount". However, the actual html markup of this block depends on the theme and you may need to define yours custom selector.', 'uni-cpo' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( 'CSS selector', 'uni-cpo' )
				),
				array(
					'id'          => 'product_image_container',
					'label'       => __( 'Custom selector (id/class) for a product image wrapper html tag', 'uni-cpo' ),
					'description' => __( 'By default, the selector for a product image wrapper html tag on a single product page is "figure.woocommerce-product-gallery__wrapper". However, the actual html markup of the image block depends on the theme and you may need to define yours custom selector. Reminder: this selector is for element that wraps the main image, not the image ("img" tag) itself!', 'uni-cpo' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( 'CSS selector', 'uni-cpo' )
				)
			)
		);

		$settings = apply_filters( 'cpo_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings() {
		if ( is_array( $this->settings ) ) {
			foreach ( $this->settings as $section => $data ) {

				// Add section to page
				add_settings_section(
					$section,
					$data['title'],
					array( $this, 'settings_section' ),
					'general_settings'
				);

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					register_setting( 'general_settings', $this->settings_base, $validation );

					// Add field to page
					add_settings_field(
						$field['id'],
						$field['label'],
						array( $this, 'display_field' ),
						'general_settings',
						$section,
						array( 'field' => $field )
					);
				}
			}
		}
	}

	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Generate HTML for displaying fields
	 *
	 * @param  array $args Field data
	 *
	 * @return void
	 */
	public function display_field( $args ) {

		$field = $args['field'];

		$html = '';

		$option = get_option( 'uni_cpo_settings_general', UniCpo()->default_settings() );

		$option_name = $this->settings_base . '[' . $field['id'] . ']';
		$data        = '';
		if ( isset( $field['default'] ) ) {
			$data = $field['default'];
			if ( $option ) {
				$data = $option[ $field['id'] ];
			}
		}

		switch ( $field['type'] ) {

			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
				break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value=""/>' . "\n";
				break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>' . "\n";
				break;

			case 'checkbox':
				$checked = '';
				if ( $option && 'on' == $option ) {
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
				break;

			case 'checkbox_multi':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
				break;

			case 'radio':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( $k == $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
				break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
				break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( in_array( $k, $data ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
				}
				$html .= '</select> ';
				break;

			case 'image':
				$image_thumb = '';
				if ( $data ) {
					$image_thumb = wp_get_attachment_thumb_url( $data );
				}
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __( 'Upload an image', 'uni-cpo' ) . '" data-uploader_button_text="' . __( 'Use image', 'uni-cpo' ) . '" class="image_upload_button button" value="' . __( 'Upload new image', 'uni-cpo' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="' . __( 'Remove image', 'uni-cpo' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
				break;

			case 'color':
				?>
                <div class="color-picker" style="position:relative;">
                <input type="text" name="<?php esc_attr_e( $option_name ); ?>" class="color"
                       value="<?php esc_attr_e( $data ); ?>"/>
                <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
                </div>
				<?php
				break;

		}

		switch ( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
				break;

			default:
				$html .= '<label for="' . esc_attr( $field['id'] ) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
				break;
		}

		echo $html;
	}

	/**
	 * Validate individual settings field
	 *
	 * @param  string $data Inputted value
	 *
	 * @return string       Validated value
	 */
	public function validate_field( $data ) {
		if ( $data && strlen( $data ) > 0 && $data != '' ) {
			$data = urlencode( strtolower( str_replace( ' ', '-', $data ) ) );
		}

		return $data;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page() {

		// Build page HTML
		$html = '<div class="wrap" id="plugin_settings">' . "\n";
		$html .= '<h1>' . esc_html__( 'Uni CPO Plugin Settings', 'uni-cpo' ) . '</h1>' . "\n";
		$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

		// Setup navigation
		$html .= '<ul id="settings-sections" class="subsubsub hide-if-no-js">' . "\n";
		$html .= '<li><a class="tab all current" href="#all">' . __( 'All', 'uni-cpo' ) . '</a></li>' . "\n";

		foreach ( $this->settings as $section => $data ) {
			$html .= '<li>| <a class="tab" href="#' . $section . '">' . $data['title'] . '</a></li>' . "\n";
		}

		$html .= '</ul>' . "\n";

		$html .= '<div class="clear"></div>' . "\n";

		// Get settings fields
		ob_start();
		settings_fields( 'general_settings' );
		do_settings_sections( 'general_settings' );
		$html .= ob_get_clean();

		$html .= '<p class="submit">' . "\n";
		$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings', 'uni-cpo' ) ) . '" />' . "\n";
		$html .= '</p>' . "\n";
		$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;
	}

}
