<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Uni_Cpo_Plugin_Settings {
	private $file;
	private $settings_base;
	private $settings;

	public function __construct( $file ) {
		$this->file              = $file;
		$this->settings_base     = 'uni_cpo_settings_general';
		$this->exclude_from_free = apply_filters( 'uni_cpo_exclude_from_free_plugin_settings', array(
			'ajax_add_to_cart',
			'product_image_size',
            'product_thumbnails_container',
			'display_weight_in_cart',
            'display_dimensions_in_cart',
			'range_slider_style',
			'file_upload',
			'sample_feature',
			'gmap_api_key'
		) );

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
					'id'          => 'ajax_add_to_cart',
					'label'       => __( 'Add product to the cart via AJAX', 'uni-cpo' ),
					'description' => __( 'This option enables adding to cart via AJAX for all the products which use custom options and price calculation.', 'uni-cpo' ),
					'type'        => 'checkbox',
					'default'     => ''
				),
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
				),
				array(
					'id'          => 'product_image_size',
					'label'       => __( 'Image size that is used for single product main image', 'uni-cpo' ),
					'description' => __( 'By default, this is "shop_single". However the actual thumbnail size used depends on the theme and you may need to choose the correct one. This setting works in conjuction with the previous one and it is important to choose proper image size, so it will be used whenever a customer selects new option in a dropdown option or image select option with an image added to this chosen option.', 'uni-cpo' ),
					'type'        => 'select',
					'options'     => uni_cpo_get_image_sizes_list(),
					'default'     => 'shop_single'
				),
				array(
					'id'          => 'product_thumbnails_container',
					'label'       => __( 'Custom selector (id/class) for a product thumbnails wrapper html tag', 'uni-cpo' ),
					'description' => __( 'By default, the selector for a product thumbnails wrapper html tag on a single product page is "ol.flex-control-thumbs". However, the actual html markup of the thumbnails block depends on the theme and you may need to define yours custom selector.', 'uni-cpo' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( 'CSS selector', 'uni-cpo' )
				),
				array(
					'id'          => 'display_weight_in_cart',
					'label'       => __( 'Display weight in the cart', 'uni-cpo' ),
					'description' => __( 'This option enables displaying product weight in the cart.', 'uni-cpo' ),
					'type'        => 'checkbox',
					'default'     => ''
				),
                array(
                    'id'          => 'display_dimensions_in_cart',
                    'label'       => __( 'Display dimensions in the cart', 'uni-cpo' ),
                    'description' => __( 'This option enables displaying product dimensions in the cart.', 'uni-cpo' ),
                    'type'        => 'checkbox',
                    'default'     => ''
                ),
				array(
					'id'          => 'range_slider_style',
					'label'       => __( 'Style for range sliders', 'uni-cpo' ),
					'description' => __( 'Default style is "HTML 5". This style will be applied for all range slider option instances in the store.', 'uni-cpo' ),
					'type'        => 'select',
					'options'     => array(
						'flat'   => __( 'Flat', 'uni-cpo' ),
						'modern' => __( 'Modern', 'uni-cpo' ),
						'html5'  => __( 'HTML5', 'uni-cpo' ),
						'nice'   => __( 'Nice', 'uni-cpo' ),
						'simple' => __( 'Simple', 'uni-cpo' ),
					),
					'default'     => 'html5'
				),
				array(
					'id'          => 'gmap_api_key',
					'label'       => __( 'Google Map API Key', 'uni-cpo' ),
					'description' => __( 'Add Google Map API key in order to use Google Map option', 'uni-cpo' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => ''
				)
			)
		);

		$settings['file_upload'] = array(
			'title'       => __( 'File upload settings', 'uni-cpo' ),
			'description' => '',
			'fields'      => array(
				array(
					'id'          => 'max_file_size',
					'label'       => __( 'Upload max file size (Mb)', 'uni-cpo' ),
					'description' => __( 'Global option: max file size that is allowed to be uploaded through File Upload option. Default is 2Mb.', 'uni-cpo' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( '2', 'uni-cpo' )
				),
				array(
					'id'          => 'mime_type',
					'label'       => __( 'Allowed mime types', 'uni-cpo' ),
					'description' => __( 'Global option: a comma separated list of allowed mime types as extension names. Default is: "jpg,zip". Important: file types defined here still must comply with allowed MIME types by WP itself. More info here: https://codex.wordpress.org/Function_Reference/get_allowed_mime_types', 'uni-cpo' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( 'jpg,zip', 'uni-cpo' )
				),
				array(
					'id'          => 'file_storage',
					'label'       => __( 'Files storage', 'uni-cpo' ),
					'description' => __( '"Local" is set by default', 'uni-cpo' ),
					'type'        => 'select',
					'options'     => array(
						'local' => __( 'Local', 'uni-cpo' ),
                        'dropbox' => __( 'Dropbox', 'uni-cpo' )
					),
					'default'     => 'local'
				),
				array(
					'id'          => 'custom_path_enable',
					'label'       => __( 'Enables custom local folder for file uploads', 'uni-cpo' ),
					'description' => __( 'By default, all the files are handled by the standard WP functions and are stored in the same as any regular attachments. This setting is called to separate file uploads via the plugin\'s File Upload option from regular attachments and store them in different folder with custom folders structure. This custom folder is still in "Uploads" folder.', 'uni-cpo' ),
					'type'        => 'checkbox',
					'default'     => '',
					'dependency'  => '#file_storage:is(local)'
				),
				array(
					'id'          => 'custom_path',
					'label'       => __( 'Define custom folders structure for file uploads', 'uni-cpo' ),
					'description' => __( 'This setting works only if a custom local folder is enabled. The path always starts in the standard "uploads" folder. {{{POST_ID}}} and {{{DATE}}} variables may be used folders structure scheme.', 'uni-cpo' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( 'cpo-uploads/{{{POST_ID}}}/{{{DATE}}}', 'uni-cpo' ),
                    'dependency'  => '#file_storage:is(local)'
				),
                array(
                    'id'          => 'dropbox_token',
                    'label'       => __( 'Dropbox Access Token', 'uni-cpo' ),
                    'description' => __( 'This access token makes it possible to upload files uploaded by customers to a separate folder in your Dropbox account. Attention: current limitation is 150 mb file! So, if you choose "dropbox" as storage option, make sure that your file uploads are limited to 150 mb!', 'uni-cpo' ),
                    'type'        => 'text',
                    'default'     => '',
                    'placeholder' => '',
                    'dependency'  => '#file_storage:is(dropbox)'
                )
			)
		);

		$settings['sample_feature'] = array(
			'title'       => __( 'Free sample functionality', 'uni-cpo' ),
			'description' => '',
			'fields'      => array(
				array(
					'id'          => 'free_sample_enable',
					'label'       => __( 'Enable "Free sample" functionality', 'uni-cpo' ),
					'description' => __( 'Enables so called "Free Sample" functionality (adding to cart free products (zero price)) and limits the total number of free products (if set).', 'uni-cpo' ),
					'type'        => 'checkbox',
					'default'     => ''
				),
				array(
					'id'          => 'free_samples_limit',
					'label'       => __( '"Free sample" products limit', 'uni-cpo' ),
					'description' => __( 'Sets the maximum total number of free products which can be added to a single order. Set to "0" or leave it empty to allow unlimited samples.', 'uni-cpo' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __( '5', 'uni-cpo' )
				)
			)
		);

		$settings = apply_filters( 'uni_cpo_plugin_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings() {
		if ( is_array( $this->settings ) ) {

			foreach ( $this->settings as $section => $data ) {

				if ( unicpo_fs()->is_not_paying() ) {
					if ( in_array( $section, $this->exclude_from_free ) ) {
						continue;
					}
				}

				// Add section to page
				add_settings_section(
					$section,
					$data['title'],
					array( $this, 'settings_section' ),
					'general_settings'
				);

				foreach ( $data['fields'] as $field ) {

					if ( unicpo_fs()->is_not_paying() ) {
						if ( in_array( $field['id'], $this->exclude_from_free ) ) {
							continue;
						}
					}

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

		$option = UniCpo()->get_settings();

		$option_name = $this->settings_base . '[' . $field['id'] . ']';
		$data        = '';
		if ( isset( $field['default'] ) ) {
			$data = $field['default'];
			if ( $option ) {
				$data = isset( $option[ $field['id'] ] ) ? $option[ $field['id'] ] : $field['default'];
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
				if ( $data && 'on' == $data ) {
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
                    <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;"
                         class="colorpicker"></div>
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
		$html .= '<p class="uni-cpo-setup-actions">
						<a class="button button-primary button-large" href="' . esc_url( admin_url( 'post-new.php?post_type=product&cpo-tutorial=true' ) ) . '">'
							. esc_html__( 'Uni CPO product basic setup tutorial', 'uni-cpo' ) .
						'</a>
					</p>';
		$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

		// Setup navigation
		$html .= '<ul id="settings-sections" class="subsubsub hide-if-no-js">' . "\n";
		$html .= '<li><a class="tab all current" href="#all">' . __( 'All', 'uni-cpo' ) . '</a></li>' . "\n";

		foreach ( $this->settings as $section => $data ) {
			if ( unicpo_fs()->is_not_paying() ) {
				if ( in_array( $section, $this->exclude_from_free ) ) {
					continue;
				}
			}
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
