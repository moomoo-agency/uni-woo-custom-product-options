<?php
/**
 * Post
 *
 * @class       Builderius_Post
 * @version     1.0.0
 * @package     Builderius/Classes/
 * @category    Class
 * @author      MooMoo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Uni_Cpo_Product Class.
 */
final class Uni_Cpo_Product {

	/**
	 * Hooks.
	 *
	 * @since 4.0.0
	 * @return void
	 */
	static public function init() {
		add_action( 'admin_bar_menu', array( __CLASS__, 'admin_bar_menu_item' ), 99 );
		add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'display_options' ), 10 );
		add_filter( 'post_row_actions', array( __CLASS__, 'builder_link' ), 10, 2 );
		if ( UniCpo()->is_pro() ) {
			add_action( 'woocommerce_product_duplicate', array( __CLASS__, 'duplicate_product_settings' ), 10, 2 );
			add_action( 'woocommerce_after_add_to_cart_form', array( __CLASS__, 'image_change_rules' ), 10 );
		}
	}

	/**
	 * Adds the page builder button to the WordPress admin bar.
	 *
	 * @since 4.0.0
	 * @return void
	 */
	static public function admin_bar_menu_item( $wp_admin_bar ) {
		global $post;

		if ( self::is_post_editable() ) {

			$wp_admin_bar->add_node( array(
				'id'    => 'cpo-admin-bar-edit-link',
				'title' => __( 'CPO builder', 'uni-cpo' ),
				'href'  => self::get_edit_url( $post->ID )
			) );
		}
	}

	/**
	 * Show the "To CPO builder" link in admin products list.
	 *
	 * @param  array $actions
	 * @param  WP_Post $post Post object
	 *
	 * @return array
	 */
	static public function builder_link( $actions, $post ) {
		if ( ! current_user_can( apply_filters( 'uni_cpo_cpo_builder_capability', 'manage_woocommerce' ) ) ) {
			return $actions;
		}

		if ( 'product' !== $post->post_type ) {
			return $actions;
		}

		$product = wc_get_product( $post->ID );

		if ( false === $product ) {
			return $actions;
		}

		if ( 'simple' !== $product->get_type() ) {
			return $actions;
		}

		$actions['cpo-builder'] = '<a href="' . esc_url( self::get_edit_url() ) . '" aria-label="'
		                          . esc_attr__( 'Go to CPO builder', 'uni-cpo' )
		                          . '" rel="permalink">' . __( 'CPO Builder', 'uni-cpo' ) . '</a>';

		return $actions;
	}

	/**
	 * Deletes content
	 *
	 * @since 4.0.0
	 *
	 * @param integer
	 *
	 * @return bool|int
	 */
	static public function delete_content( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( $product ) {
			do_action( 'before_cpo_delete_content', $product_id );

			$id = delete_post_meta( $product_id, '_cpo_content' );

			// update generated CSS
			update_option( 'builder_css_cached_for' . $product_id, '' );

			do_action( 'after_cpo_delete_content', $product_id );

			return $id;
		}

		return false;
	}

	/**
	 * Display product options
	 *
	 * @since 4.0.0
	 *
	 * @param string $content
	 *
	 * @return void
	 */
	static public function display_options() {

		do_action( 'uni_cpo_before_render_content' );

		echo '<div id="' . esc_attr( UniCpo()->get_builder_id() ) . '" class="uni-builderius-container">';

		do_action( 'uni_cpo_before_render_form_fields' );

		if ( ! self::is_builder_active() && self::is_single_product() ) {
			$product_data     = self::get_product_data();
			$cpo_cart_item_id = current_time( 'timestamp' );

			if ( 'on' === $product_data['settings_data']['cpo_enable']
			     && ! empty( $product_data['content'] )
			) {

				$post_data = apply_filters(
					'uni_cpo_post_data_before_options',
					array(),
					$product_data['id']
				);

				if ( isset( $_POST['cpo_product_id'] ) ) {
					$product_data['post_thumb_id'] = $_POST['cpo_product_image'];
					unset( $_POST['cpo_product_id'] );
					unset( $_POST['cpo_cart_item_id'] );
					unset( $_POST['add-to-cart'] );
					unset( $_POST['quantity'] );
					$post_data = $_POST;
				} elseif ( self::is_cart_item_editing( $product_data['id'] ) ) {
					$transient_data   = get_transient( '_cpo_cart_item_edit_' . $_GET['cpo_cart_item_edit'] );
					$cart_item_key    = $transient_data['key'];
					$cart_content     = WC()->cart->get_cart_contents();
					$edited_item      = $cart_content[ $cart_item_key ];
					$post_data        = $edited_item['_cpo_data'];
					$cpo_cart_item_id = $cart_item_key;
				}

				$post_data = apply_filters( 'uni_cpo_product_post_data', $post_data, $product_data );

				if ( ! empty( $product_data['settings_data']['price_disabled_msg'] ) ) {
					echo '<div class="js-uni-cpo-ordering-disabled-notice" style="display:none;">';
					echo do_shortcode( $product_data['settings_data']['price_disabled_msg'] );
					echo '</div>';
				}

				echo '<input type="hidden" class="js-cpo-pid" name="cpo_product_id" value="' . esc_attr( $product_data['id'] ) . '" />';
				if ( ! self::is_ajax_add_to_cart( $product_data['id'] ) ) {
					echo '<input type="hidden" name="add-to-cart" value="' . esc_attr( $product_data['id'] ) . '" />';
				}
				echo '<input type="hidden" class="js-cpo-product-image" name="cpo_product_image" value="' . esc_attr( $product_data['post_thumb_id'] ) . '" />';
				echo '<input type="hidden" class="js-cpo-cart-item" name="cpo_cart_item_id" value="' . esc_attr( $cpo_cart_item_id ) . '" />';

				do_action( 'uni_cpo_before_render_builder_modules', $product_data );

				foreach ( $product_data['content'] as $row_key => $row_data ) {
					$row_class = UniCpo()->module_factory::get_classname_from_module_type( $row_data['type'] );
					call_user_func( array( $row_class, 'template' ), $row_data, $post_data );
				}

				do_action( 'uni_cpo_after_render_builder_modules', $product_data );
			}
		}

		do_action( 'uni_cpo_after_render_form_fields' );

		echo '</div>';

		do_action( 'uni_cpo_after_render_content' );

	}

	/**
	 * Function to duplicate one product's settings and save them for another product
	 *
	 * @param WC_Product $product
	 * @param WC_Product $product_from A product from which the settings will be duplicated
	 *
	 * @return bool|array
	 */
	static public function duplicate_product_settings( $product, $product_from ) {
		if ( UniCpo()->is_pro() ) {
			try {
				if ( 'simple' !== $product->get_type() ) {
					return false;
				}

				$new_data               = self::get_product_data_by_id( $product_from->get_id() );
				$new_data['product_id'] = $product->get_id();
				self::save_product_data( $new_data );
				$new_content = self::get_content( $product_from->get_id() );
				if ( $new_content ) {
					self::update_content( $product->get_id(), $new_content );
				}

				return true;
			} catch ( Exception $e ) {
				return array( 'error' => $e->getMessage() );
			}
		}
	}

	/**
	 * Enable the builder editor for the main post in the query.
	 *
	 * @since 4.0.0
	 * @return void
	 */
	static public function enable_editing() {
		global $wp_the_query;

		if ( self::is_post_editable() ) {

			$post = $wp_the_query->post;

			//  TODO Lock the builder
			/*if ( ! function_exists( 'wp_set_post_lock' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/post.php' );
			}
			wp_set_post_lock( $post->ID );*/
		}
	}

	/**
	 * Deletes content
	 *
	 * @since 4.0.0
	 *
	 * @param int $product_id
	 *
	 * @return bool|int
	 */
	static public function get_content( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( $product ) {
			do_action( 'before_cpo_get_content', $product_id );

			$content = get_post_meta( $product_id, '_cpo_content', true );

			do_action( 'after_cpo_get_content', $product_id, $content );

			return $content;
		}

		return false;
	}

	/**
	 * Returns a builder edit URL.
	 *
	 * @since 4.0.0
	 *
	 * @param int|bool $post_id
	 *
	 * @return string
	 */
	static public function get_edit_url( $post_id = false ) {
		if ( false === $post_id ) {
			global $post;
		} else {
			$post = get_post( $post_id );
		}

		$builder_edit_mode_uri = add_query_arg( 'cpo_options', '1', get_permalink( $post->ID ) );

		return set_url_scheme( $builder_edit_mode_uri );
	}

	/**
	 * Returns the currently viewing product data
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	static public function get_product_data() {
		global $wp_the_query;

		$data = array();
		if ( self::is_single_product() ) {
			$data = self::get_product_data_by_id( $wp_the_query->post->ID );
		}

		return $data;
	}

	static public function image_change_rules() {

		if ( UniCpo()->is_pro() && self::is_single_product() ) {
			$product_settings = self::get_product_data();
			$rules_data       = $product_settings['image_data']['image_scheme'];


			$is_enabled = ( 'on' === $product_settings['image_data']['image_enable'] ) ? true : false;

			if ( ! $is_enabled ) {
				return;
			}

			$rules           = $rules_data;
			$plugin_settings = UniCpo()->get_settings();
			$image_size      = $plugin_settings['product_image_size'];

			if ( is_array( $rules ) && ! empty( $rules ) ) {
				$i               = 1;
				$count           = count( $rules );
				$final_statement = '';
				foreach ( $rules as $rule ) {
					$scheme                = json_decode( $rule['rule'], true );
					$attach_id             = $rule['attach_id'];
					$replacement_attach_id = $attach_id;
					$image                 = wp_get_attachment_image_src( $replacement_attach_id, $image_size );
					$image_full            = wp_get_attachment_image_src( $replacement_attach_id, 'full' );
					$image_thumb           = wp_get_attachment_image_src( $replacement_attach_id, 'thumbnail' );
					$image_meta            = get_post_meta( $replacement_attach_id, '_wp_attachment_metadata', true );
					$src                   = $image[0];
					$width                 = $image[1];
					$height                = $image[2];
					$src_full              = $image_full[0];
					$width_full            = $image_full[1];
					$height_full           = $image_full[2];
					$size_array            = array( absint( $width ), absint( $height ) );
					$srcset                = wp_calculate_image_srcset( $size_array, $src, $image_meta, $replacement_attach_id );
					$sizes                 = wp_calculate_image_sizes( $size_array, $src, $image_meta, $replacement_attach_id );
					$for_image_replace     = array(
						'imgId'                 => $replacement_attach_id,
						'imgFullUri'            => $src_full,
						'imgTitle'              => $rule['attach_name'],
						'imgAlt'                => $rule['attach_name'],
						'imgUri'                => $src,
						'imgSrcset'             => $srcset,
						'imgSizes'              => $sizes,
						'imgSrc'                => $src,
						'imgLarge_image'        => $src_full,
						'imgLarge_image_width'  => $width_full,
						'imgLarge_image_height' => $height_full,
						'imgThumbUri'           => $image_thumb[0],
						'imgWidth'              => $width,
						'imgHeight'             => $height
					);

					$condition = uni_cpo_option_js_condition_prepare( $scheme );

					if ( $i === 1 ) {
						$final_statement = 'window.UniCpo.imageLogicOn = true;' . "\n";
						$final_statement .= 'if ' . $condition . ' {' . "\n";
						$final_statement .= 'window.UniCpo.replaceMainImageData(' . json_encode( $for_image_replace ) . ')';
						$final_statement .= '}';
					} elseif ( $i <= $count ) {
						$final_statement .= ' else if ' . $condition . ' {' . "\n";
						$final_statement .= 'window.UniCpo.replaceMainImageData(' . json_encode( $for_image_replace ) . ')';
						$final_statement .= '}';
					}

					$i ++;
				}

				$image             = wp_get_attachment_image_src( $product_settings['post_thumb_id'], $image_size );
				$image_full        = wp_get_attachment_image_src( $product_settings['post_thumb_id'], 'full' );
				$image_thumb       = wp_get_attachment_image_src( $product_settings['post_thumb_id'], 'thumbnail' );
				$image_meta        = get_post_meta( $product_settings['post_thumb_id'], '_wp_attachment_metadata', true );
				$src               = $image[0];
				$width             = $image[1];
				$height            = $image[2];
				$src_full          = $image_full[0];
				$width_full        = $image_full[1];
				$height_full       = $image_full[2];
				$size_array        = array( absint( $width ), absint( $height ) );
				$srcset            = wp_calculate_image_srcset( $size_array, $src, $image_meta, $product_settings['post_thumb_id'] );
				$sizes             = wp_calculate_image_sizes( $size_array, $src, $image_meta, $product_settings['post_thumb_id'] );
				$for_image_replace = array(
					'imgId'                 => $product_settings['post_thumb_id'],
					'imgFullUri'            => $src_full,
					'imgTitle'              => get_post_meta( $product_settings['post_thumb_id'], '_wp_attachment_image_alt', true ),
					'imgAlt'                => get_post_meta( $product_settings['post_thumb_id'], '_wp_attachment_image_alt', true ),
					'imgUri'                => $src,
					'imgSrcset'             => $srcset,
					'imgSizes'              => $sizes,
					'imgSrc'                => $src,
					'imgLarge_image'        => $src_full,
					'imgLarge_image_width'  => $width_full,
					'imgLarge_image_height' => $height_full,
					'imgThumbUri'           => $image_thumb[0],
					'imgWidth'              => $width,
					'imgHeight'             => $height
				);

				$final_statement .= ' else {' . "\n";
				$final_statement .= 'window.UniCpo.replaceMainImageData(' . json_encode( $for_image_replace ) . ')';
				$final_statement .= '}' . "\n";

				?>
				<script>
                    jQuery(document).ready(function($) {
                        'use strict';

						<?php
						$arrFields = [];
						foreach ( $rules as $rule ) {
							$scheme   = json_decode( $rule['rule'], true );
							$arrRules = json_encode( $scheme['rules'] );
							foreach ( json_decode( $arrRules, true ) as $key => $value ) {
								$arrFields[] = $value['id'];
							}
						}
						$uniqueFields = array_unique( $arrFields );

						foreach ( $uniqueFields as $field ) {
							$output = '$(document.body).on(\'change\', \'[id*="' . $field . '-field"]\', function (){' . "\n";
							$output .= 'let fields = window.UniCpo.collectData(false);' . "\n";
							$output .= 'const interval = setInterval(function () {' . "\n";
							$output .= 'change_main_image_func(fields);' . "\n";
							$output .= 'clearInterval(interval);' . "\n";
							$output .= '}, 200);' . "\n";
							$output .= '});' . "\n";
							echo $output;
						} ?>

                        $(document.body).on('uni_cpo_frontend_is_ready', function() {
                            let fields = window.UniCpo.collectData(false);
                            change_main_image_func(fields);
                        });

                        function change_main_image_func (formData) {
							<?php echo $final_statement; ?>
                        }
                    });
				</script>
				<?php
			}
		}

	}

	/**
	 * Checks whether the product should be added via ajax
	 *
	 * @since 4.0.10
	 *
	 * @return bool
	 */
	static public function is_ajax_add_to_cart( $product_id ) {
		$plugin_settings = UniCpo()->get_settings();

		return ( $plugin_settings['ajax_add_to_cart'] || self::is_cart_item_editing( $product_id ) ) ? true : false;
	}

	/**
	 * Checks whether this is cart item editing
	 *
	 * @since 4.0.10
	 *
	 * @return bool
	 */
	static public function is_cart_item_editing( $product_id ) {
		$product_data = self::get_product_data_by_id( $product_id );
		if ( isset( $_GET['cpo_cart_item_edit'] ) && get_transient( '_cpo_cart_item_edit_' . $_GET['cpo_cart_item_edit'] ) ) {
			$transient_data = get_transient( '_cpo_cart_item_edit_' . $_GET['cpo_cart_item_edit'] );

			if ( WC()->cart->get_cart_contents() && $transient_data['product_id'] === $product_data['id'] ) {
				return true;
			}

			return false;
		}

		return false;
	}

	/**
	 * Checks whether UniCpo's builder mode is active
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	static public function is_builder_active() {
		$product_data = self::get_product_data();
		$is_active    = false;

		if ( self::is_post_editable() && ! is_admin() && ! post_password_required() ) {
			if ( isset( $_GET['cpo_options'] ) ) {
				$is_active = true;
			}
			if ( '/?cpo_options' === $_SERVER['REQUEST_URI'] ) {
				$is_active = true;
			}
		}

		return apply_filters( 'uni_cpo_is_builder_active', $is_active, $product_data );
	}

	/**
	 * Checks whether the post can be edited
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	static public function is_post_editable() {
		global $wp_the_query;

		if ( is_singular( 'product' ) && isset( $wp_the_query->post ) ) {

			$product      = wc_get_product( $wp_the_query->post );
			$user_can     = current_user_can( 'edit_post', $product->get_id() );
			$product_type = $product->get_type();

			if ( $user_can && ( 'simple' === $product_type || 'subscription' === $product_type ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks whether we are on a single product page
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	static public function is_single_product() {
		global $wp_the_query;
		if ( is_singular( 'product' ) && is_main_query() && isset( $wp_the_query->post ) ) {
			$product      = wc_get_product( $wp_the_query->post );
			$product_type = $product->get_type();
			if ( 'simple' === $product_type || 'subscription' === $product_type ) {
				return true;
			}
		}

		return false;

	}

	/**
	 * Checks whether display or not a visual pointers in order
	 * to guide the user through key elements/features
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	static public function is_guide_needed() {

		if ( self::is_builder_active() ) {
			$current_user = wp_get_current_user();
			$guide_used   = get_user_meta( $current_user->ID, '_cpo_guide_used', true );

			if ( empty( $guide_used ) ) {
				//update_user_meta( $current_user->ID, '_cpo_guide_used', 1 );

				return true;
			}
		}

		return false;
	}

	/**
	 * Returns the product data by product id
	 *
	 * @since 4.0.0
	 *
	 * @param int $product_id
	 *
	 * @return array
	 */
	static public function get_product_data_by_id( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return array();
		}

		$data['id']            = $product->get_id();
		$data['post_thumb_id'] = get_post_thumbnail_id( $data['id'] );
		$data['uri']           = get_permalink( $data['id'] );
		$data['content']       = array();

		$cpo_content = get_post_meta( $data['id'], '_cpo_content', true );
		if ( $cpo_content ) {
			$cpo_content     = uni_cpo_decode( $cpo_content );
			$data['content'] = $cpo_content;
		}

		$cpo_enable            = ( get_post_meta( $data['id'], '_cpo_enable', true ) )
			? get_post_meta( $data['id'], '_cpo_enable', true )
			: 'off';
		$calc_enable           = ( get_post_meta( $data['id'], '_cpo_calc_enable', true ) )
			? get_post_meta( $data['id'], '_cpo_calc_enable', true )
			: 'off';
		$calc_btn_enable       = ( get_post_meta( $data['id'], '_cpo_calc_btn_enable', true ) )
			? get_post_meta( $data['id'], '_cpo_calc_btn_enable', true )
			: 'off';
        $reset_form_btn       = ( get_post_meta( $data['id'], '_cpo_reset_form_btn', true ) )
            ? get_post_meta( $data['id'], '_cpo_reset_form_btn', true )
            : 'off';
		$cart_duplicate_enable = ( get_post_meta( $data['id'], '_cart_duplicate_enable', true ) )
			? get_post_meta( $data['id'], '_cart_duplicate_enable', true )
			: 'off';
		$cart_edit_enable      = ( get_post_meta( $data['id'], '_cart_edit_enable', true ) )
			? get_post_meta( $data['id'], '_cart_edit_enable', true )
			: 'off';
		$cart_edit_full_enable = ( get_post_meta( $data['id'], '_cart_edit_full_enable', true ) )
			? get_post_meta( $data['id'], '_cart_edit_full_enable', true )
			: 'off';
		$silent_validation_on  = ( get_post_meta( $data['id'], '_cpo_silent_validation_on', true ) )
			? get_post_meta( $data['id'], '_cpo_silent_validation_on', true )
			: 'off';
		$qty_field             = ( get_post_meta( $data['id'], '_cpo_qty_field', true ) )
			? get_post_meta( $data['id'], '_cpo_qty_field', true )
			: 'wc';
		$sold_individually  = ( get_post_meta( $data['id'], '_cpo_sold_individually', true ) )
			? get_post_meta( $data['id'], '_cpo_sold_individually', true )
			: 'off';
		$price_min             = ( get_post_meta( $data['id'], '_cpo_min_price', true ) )
			? floatval( get_post_meta( $data['id'], '_cpo_min_price', true ) )
			: '';
		$price_max             = ( get_post_meta( $data['id'], '_cpo_max_price', true ) )
			? floatval( get_post_meta( $data['id'], '_cpo_max_price', true ) )
			: '';
		$price_disabled_msg    = get_post_meta( $data['id'], '_cpo_price_disabled_msg', true );
		$price_suffix          = get_post_meta( $data['id'], '_cpo_price_suffix', true );
		$price_postfix         = get_post_meta( $data['id'], '_cpo_price_postfix', true );
		$starting_price        = ( get_post_meta( $data['id'], '_cpo_starting_price', true ) )
			? floatval( get_post_meta( $data['id'], '_cpo_starting_price', true ) )
			: '';
		$price_archives        = get_post_meta( $data['id'], '_cpo_price_archives', true );
		$price_archives_sale   = get_post_meta( $data['id'], '_cpo_price_archives_sale', true );
		$layered_image_enable  = ( get_post_meta( $data['id'], '_cpo_layered_image_enable', true ) )
			? get_post_meta( $data['id'], '_cpo_layered_image_enable', true )
			: 'off';
		$imagify_enable  = ( get_post_meta( $data['id'], '_cpo_imagify_enable', true ) )
			? get_post_meta( $data['id'], '_cpo_imagify_enable', true )
			: 'off';
		$imagify_base_image  = ( get_post_meta( $data['id'], '_cpo_imagify_base_image', true ) )
			? get_post_meta( $data['id'], '_cpo_imagify_base_image', true )
			: '';

		$data['settings_data'] = array(
			'cpo_enable'            => $cpo_enable,
			'calc_enable'           => $calc_enable,
			'calc_btn_enable'       => $calc_btn_enable,
			'reset_form_btn'       => $reset_form_btn,
			'cart_duplicate_enable' => $cart_duplicate_enable,
			'cart_edit_enable'      => $cart_edit_enable,
			'cart_edit_full_enable' => $cart_edit_full_enable,
			'silent_validation_on'  => $silent_validation_on,
			'qty_field'             => $qty_field,
			'sold_individually'     => $sold_individually,
			'min_price'             => $price_min,
			'max_price'             => $price_max,
			'price_disabled_msg'    => $price_disabled_msg,
			'price_suffix'          => $price_suffix,
			'price_postfix'         => $price_postfix,
			'starting_price'        => $starting_price,
			'price_archives'        => $price_archives,
			'price_archives_sale'   => $price_archives_sale,
			'layered_image_enable'  => $layered_image_enable,
			'imagify_enable' => $imagify_enable,
			'imagify_base_image' => $imagify_base_image
		);

		$discounts_strategy         = ( get_post_meta( $data['id'], '_cpo_cart_discounts_strategy', true ) )
			? get_post_meta( $data['id'], '_cpo_cart_discounts_strategy', true )
			: 'highest';
		$role_cart_discounts_enable = ( get_post_meta( $data['id'], '_cpo_role_cart_discounts_enable', true ) )
			? get_post_meta( $data['id'], '_cpo_role_cart_discounts_enable', true )
			: 'off';
		$qty_cart_discounts_enable  = ( get_post_meta( $data['id'], '_cpo_qty_cart_discounts_enable', true ) )
			? get_post_meta( $data['id'], '_cpo_qty_cart_discounts_enable', true )
			: 'off';
		$qty_cart_discounts_field   = ( get_post_meta( $data['id'], '_cpo_qty_cart_discounts_field', true ) )
			? get_post_meta( $data['id'], '_cpo_qty_cart_discounts_field', true )
			: 'off';
		$data['discounts_data']     = array(
			'cart_discounts_strategy'    => $discounts_strategy,
			'role_cart_discounts_enable' => $role_cart_discounts_enable,
			'qty_cart_discounts_enable'  => $qty_cart_discounts_enable,
			'role_cart_discounts'        => get_post_meta( $data['id'], '_cpo_role_cart_discounts', true ),
			'qty_cart_discounts'         => get_post_meta( $data['id'], '_cpo_qty_cart_discounts', true ),
			'qty_cart_discounts_field'   => $qty_cart_discounts_field
		);

		$rules_enable         = ( get_post_meta( $data['id'], '_cpo_formula_rules_enable', true ) )
			? get_post_meta( $data['id'], '_cpo_formula_rules_enable', true )
			: 'off';
		$formula_scheme       = ( get_post_meta( $data['id'], '_cpo_formula_scheme', true ) )
			? get_post_meta( $data['id'], '_cpo_formula_scheme', true )
			: array();
		$data['formula_data'] = array(
			'rules_enable'   => $rules_enable,
			'formula_scheme' => $formula_scheme,
			'main_formula'   => get_post_meta( $data['id'], '_cpo_main_formula', true )
		);

		$weight_enable       = ( get_post_meta( $data['id'], '_cpo_weight_enable', true ) )
			? get_post_meta( $data['id'], '_cpo_weight_enable', true )
			: 'off';
		$weight_rules_enable = ( get_post_meta( $data['id'], '_cpo_weight_rules_enable', true ) )
			? get_post_meta( $data['id'], '_cpo_weight_rules_enable', true )
			: 'off';
		$weight_scheme       = ( get_post_meta( $data['id'], '_cpo_weight_scheme', true ) )
			? get_post_meta( $data['id'], '_cpo_weight_scheme', true )
			: array();
		$data['weight_data'] = array(
			'weight_enable'       => $weight_enable,
			'weight_rules_enable' => $weight_rules_enable,
			'weight_scheme'       => $weight_scheme,
			'main_weight_formula' => get_post_meta( $data['id'], '_cpo_main_weight_formula', true )
		);

		$image_enable       = ( get_post_meta( $data['id'], '_cpo_image_enable', true ) )
			? get_post_meta( $data['id'], '_cpo_image_enable', true )
			: 'off';
		$image_scheme       = ( get_post_meta( $data['id'], '_cpo_image_scheme', true ) )
			? get_post_meta( $data['id'], '_cpo_image_scheme', true )
			: array();
		$data['image_data'] = array(
			'image_enable' => $image_enable,
			'image_scheme' => $image_scheme
		);

		$dimensions_enable       = ( get_post_meta( $data['id'], '_cpo_dimensions_enable', true ) )
			? get_post_meta( $data['id'], '_cpo_dimensions_enable', true )
			: 'off';
		$d_unit_option           = ( get_post_meta( $data['id'], '_cpo_d_unit_option', true ) )
			? get_post_meta( $data['id'], '_cpo_d_unit_option', true )
			: '';
		$d_length_option         = ( get_post_meta( $data['id'], '_cpo_d_length_option', true ) )
			? get_post_meta( $data['id'], '_cpo_d_length_option', true )
			: '';
		$convert_length          = ( get_post_meta( $data['id'], '_cpo_convert_length', true ) )
			? get_post_meta( $data['id'], '_cpo_convert_length', true )
			: 'off';
		$d_width_option          = ( get_post_meta( $data['id'], '_cpo_d_width_option', true ) )
			? get_post_meta( $data['id'], '_cpo_d_width_option', true )
			: '';
		$convert_width           = ( get_post_meta( $data['id'], '_cpo_convert_width', true ) )
			? get_post_meta( $data['id'], '_cpo_convert_width', true )
			: 'off';
		$d_height_option         = ( get_post_meta( $data['id'], '_cpo_d_height_option', true ) )
			? get_post_meta( $data['id'], '_cpo_d_height_option', true )
			: '';
		$convert_height          = ( get_post_meta( $data['id'], '_cpo_convert_height', true ) )
			? get_post_meta( $data['id'], '_cpo_convert_height', true )
			: 'off';
		$data['dimensions_data'] = array(
			'dimensions_enable' => $dimensions_enable,
			'd_unit_option'     => $d_unit_option,
			'd_length_option'   => $d_length_option,
			'convert_length'    => $convert_length,
			'd_width_option'    => $d_width_option,
			'convert_width'     => $convert_width,
			'd_height_option'   => $d_height_option,
			'convert_height'    => $convert_height
		);

		$nov_enable       = ( get_post_meta( $data['id'], '_cpo_nov_enable', true ) )
			? get_post_meta( $data['id'], '_cpo_nov_enable', true )
			: 'off';
		$wholesale_enable = ( get_post_meta( $data['id'], '_cpo_wholesale_enable', true ) )
			? get_post_meta( $data['id'], '_cpo_wholesale_enable', true )
			: 'off';
		$data['nov_data'] = array(
			'nov_enable'       => $nov_enable,
			'wholesale_enable' => $wholesale_enable,
			'nov'              => get_post_meta( $data['id'], '_cpo_nov', true )
		);

		return $data;
	}

	/**
	 * Saves product data
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	static public function save_product_data( $data, $context = 'all' ) {
		$product_id = $data['product_id'];
		$product    = wc_get_product( $product_id );

		try {
			if ( ! $product ) {
				throw new Exception( __( 'Product does not exist or not chosen', 'uni-cpo' ) );
			}

			if ( 'all' === $context ) {
				update_post_meta( $product->get_id(), '_cpo_enable', $data['settings_data']['cpo_enable'] );
				update_post_meta( $product->get_id(), '_cpo_calc_enable', $data['settings_data']['calc_enable'] );
				update_post_meta( $product->get_id(), '_cpo_calc_btn_enable', $data['settings_data']['calc_btn_enable'] );
				update_post_meta( $product->get_id(), '_cpo_reset_form_btn', $data['settings_data']['reset_form_btn'] );
				update_post_meta( $product->get_id(), '_cart_duplicate_enable', $data['settings_data']['cart_duplicate_enable'] );
				update_post_meta( $product->get_id(), '_cart_edit_enable', $data['settings_data']['cart_edit_enable'] );
				update_post_meta( $product->get_id(), '_cart_edit_full_enable', $data['settings_data']['cart_edit_full_enable'] );
				update_post_meta( $product->get_id(), '_cpo_silent_validation_on', $data['settings_data']['silent_validation_on'] );
				update_post_meta( $product->get_id(), '_cpo_qty_field', $data['settings_data']['qty_field'] );
				update_post_meta( $product->get_id(), '_cpo_sold_individually', $data['settings_data']['sold_individually'] );
				update_post_meta( $product->get_id(), '_cpo_min_price', $data['settings_data']['min_price'] );
				update_post_meta( $product->get_id(), '_cpo_max_price', $data['settings_data']['max_price'] );
				update_post_meta( $product->get_id(), '_cpo_price_disabled_msg', $data['settings_data']['price_disabled_msg'] );
				update_post_meta( $product->get_id(), '_cpo_price_suffix', $data['settings_data']['price_suffix'] );
				update_post_meta( $product->get_id(), '_cpo_price_postfix', $data['settings_data']['price_postfix'] );
				update_post_meta( $product->get_id(), '_cpo_starting_price', $data['settings_data']['starting_price'] );
				update_post_meta( $product->get_id(), '_cpo_price_archives', $data['settings_data']['price_archives'] );
				update_post_meta( $product->get_id(), '_cpo_price_archives', $data['settings_data']['price_archives_sale'] );
				update_post_meta( $product->get_id(), '_cpo_layered_image_enable', $data['settings_data']['layered_image_enable'] );
				update_post_meta( $product->get_id(), '_cpo_imagify_enable', $data['settings_data']['imagify_enable'] );
				update_post_meta( $product->get_id(), '_cpo_imagify_base_image', $data['settings_data']['imagify_base_image'] );

				update_post_meta( $product->get_id(), '_cpo_cart_discounts_strategy', $data['discounts_data']['cart_discounts_strategy'] );
				update_post_meta( $product->get_id(), '_cpo_role_cart_discounts_enable', $data['discounts_data']['role_cart_discounts_enable'] );
				update_post_meta( $product->get_id(), '_cpo_qty_cart_discounts_enable', $data['discounts_data']['qty_cart_discounts_enable'] );
				update_post_meta( $product->get_id(), '_cpo_role_cart_discounts', $data['discounts_data']['role_cart_discounts'] );
				update_post_meta( $product->get_id(), '_cpo_qty_cart_discounts', $data['discounts_data']['qty_cart_discounts'] );
				update_post_meta( $product->get_id(), '_cpo_qty_cart_discounts_field', $data['discounts_data']['qty_cart_discounts_field'] );

				update_post_meta( $product->get_id(), '_cpo_formula_rules_enable', $data['formula_data']['rules_enable'] );
				update_post_meta( $product->get_id(), '_cpo_formula_scheme', $data['formula_data']['formula_scheme'] );
				update_post_meta( $product->get_id(), '_cpo_main_formula', $data['formula_data']['main_formula'] );

				update_post_meta( $product->get_id(), '_cpo_image_enable', $data['image_data']['image_enable'] );
				update_post_meta( $product->get_id(), '_cpo_image_scheme', $data['weight_data']['image_scheme'] );

				update_post_meta( $product->get_id(), '_cpo_weight_enable', $data['weight_data']['weight_enable'] );
				update_post_meta( $product->get_id(), '_cpo_weight_rules_enable', $data['weight_data']['weight_rules_enable'] );
				update_post_meta( $product->get_id(), '_cpo_weight_scheme', $data['weight_data']['weight_scheme'] );
				update_post_meta( $product->get_id(), '_cpo_main_weight_formula', $data['weight_data']['main_weight_formula'] );

				update_post_meta( $product->get_id(), '_cpo_dimensions_enable', $data['dimensions_data']['dimensions_enable'] );
				update_post_meta( $product->get_id(), '_cpo_d_unit_option', $data['dimensions_data']['d_unit_option'] );
				update_post_meta( $product->get_id(), '_cpo_d_length_option', $data['dimensions_data']['d_length_option'] );
				update_post_meta( $product->get_id(), '_cpo_convert_length', $data['dimensions_data']['convert_length'] );
				update_post_meta( $product->get_id(), '_cpo_d_width_option', $data['dimensions_data']['d_width_option'] );
				update_post_meta( $product->get_id(), '_cpo_convert_width', $data['dimensions_data']['convert_width'] );
				update_post_meta( $product->get_id(), '_cpo_d_height_option', $data['dimensions_data']['d_height_option'] );
				update_post_meta( $product->get_id(), '_cpo_convert_height', $data['dimensions_data']['convert_height'] );

				update_post_meta( $product->get_id(), '_cpo_nov_enable', $data['nov_data']['nov_enable'] );
				update_post_meta( $product->get_id(), '_cpo_wholesale_enable', $data['nov_data']['wholesale_enable'] );
				update_post_meta( $product->get_id(), '_cpo_nov', $data['nov_data']['nov'] );

				return array(
					'settings_data'   => $data['settings_data'],
					'discounts_data'  => $data['discounts_data'],
					'formula_data'    => $data['formula_data'],
					'weight_data'     => $data['weight_data'],
					'image_data'      => $data['image_data'],
					'dimensions_data' => $data['dimensions_data'],
					'nov_data'        => $data['nov_data'],
				);
			} elseif ( 'settings_data' === $context ) {
				update_post_meta( $product->get_id(), '_cpo_enable', $data['settings_data']['cpo_enable'] );
				update_post_meta( $product->get_id(), '_cpo_calc_enable', $data['settings_data']['calc_enable'] );
				update_post_meta( $product->get_id(), '_cpo_calc_btn_enable', $data['settings_data']['calc_btn_enable'] );
				update_post_meta( $product->get_id(), '_cpo_reset_form_btn', $data['settings_data']['reset_form_btn'] );
				update_post_meta( $product->get_id(), '_cart_duplicate_enable', $data['settings_data']['cart_duplicate_enable'] );
				update_post_meta( $product->get_id(), '_cart_edit_enable', $data['settings_data']['cart_edit_enable'] );
				update_post_meta( $product->get_id(), '_cart_edit_full_enable', $data['settings_data']['cart_edit_full_enable'] );
				update_post_meta( $product->get_id(), '_cpo_silent_validation_on', $data['settings_data']['silent_validation_on'] );
				update_post_meta( $product->get_id(), '_cpo_qty_field', $data['settings_data']['qty_field'] );
				update_post_meta( $product->get_id(), '_cpo_sold_individually', $data['settings_data']['sold_individually'] );
				update_post_meta( $product->get_id(), '_cpo_min_price', $data['settings_data']['min_price'] );
				update_post_meta( $product->get_id(), '_cpo_max_price', $data['settings_data']['max_price'] );
				update_post_meta( $product->get_id(), '_cpo_price_disabled_msg', $data['settings_data']['price_disabled_msg'] );
				update_post_meta( $product->get_id(), '_cpo_price_suffix', $data['settings_data']['price_suffix'] );
				update_post_meta( $product->get_id(), '_cpo_price_postfix', $data['settings_data']['price_postfix'] );
				update_post_meta( $product->get_id(), '_cpo_starting_price', $data['settings_data']['starting_price'] );
				update_post_meta( $product->get_id(), '_cpo_price_archives', $data['settings_data']['price_archives'] );
				update_post_meta( $product->get_id(), '_cpo_price_archives_sale', $data['settings_data']['price_archives_sale'] );
                update_post_meta( $product->get_id(), '_cpo_layered_image_enable', $data['settings_data']['layered_image_enable'] );
				update_post_meta( $product->get_id(), '_cpo_imagify_enable', $data['settings_data']['imagify_enable'] );
				update_post_meta( $product->get_id(), '_cpo_imagify_base_image', $data['settings_data']['imagify_base_image'] );

				return array( 'settings_data' => $data['settings_data'] );
			} elseif ( 'discounts_data' === $context ) {
				update_post_meta( $product->get_id(), '_cpo_cart_discounts_strategy', $data['discounts_data']['cart_discounts_strategy'] );
				update_post_meta( $product->get_id(), '_cpo_role_cart_discounts_enable', $data['discounts_data']['role_cart_discounts_enable'] );
				update_post_meta( $product->get_id(), '_cpo_qty_cart_discounts_enable', $data['discounts_data']['qty_cart_discounts_enable'] );
				update_post_meta( $product->get_id(), '_cpo_role_cart_discounts', $data['discounts_data']['role_cart_discounts'] );
				update_post_meta( $product->get_id(), '_cpo_qty_cart_discounts', $data['discounts_data']['qty_cart_discounts'] );
				update_post_meta( $product->get_id(), '_cpo_qty_cart_discounts_field', $data['discounts_data']['qty_cart_discounts_field'] );

				return array( 'discounts_data' => $data['discounts_data'] );
			} elseif ( 'formula_data' === $context ) {
				update_post_meta( $product->get_id(), '_cpo_formula_rules_enable', $data['formula_data']['rules_enable'] );
				update_post_meta( $product->get_id(), '_cpo_formula_scheme', $data['formula_data']['formula_scheme'] );
				update_post_meta( $product->get_id(), '_cpo_main_formula', $data['formula_data']['main_formula'] );

				return array( 'formula_data' => $data['formula_data'] );
			} elseif ( 'weight_data' === $context ) {
				update_post_meta( $product->get_id(), '_cpo_weight_enable', $data['weight_data']['weight_enable'] );
				update_post_meta( $product->get_id(), '_cpo_weight_rules_enable', $data['weight_data']['weight_rules_enable'] );
				update_post_meta( $product->get_id(), '_cpo_weight_scheme', $data['weight_data']['weight_scheme'] );
				update_post_meta( $product->get_id(), '_cpo_main_weight_formula', $data['weight_data']['main_weight_formula'] );

				return array( 'weight_data' => $data['weight_data'] );
			} elseif ( 'image_data' === $context ) {
				update_post_meta( $product->get_id(), '_cpo_image_enable', $data['image_data']['image_enable'] );
				update_post_meta( $product->get_id(), '_cpo_image_scheme', $data['image_data']['image_scheme'] );

				return array( 'image_data' => $data['image_data'] );
			} elseif ( 'dimensions_data' === $context ) {
				update_post_meta( $product->get_id(), '_cpo_dimensions_enable', $data['dimensions_data']['dimensions_enable'] );
				update_post_meta( $product->get_id(), '_cpo_d_unit_option', $data['dimensions_data']['d_unit_option'] );
				update_post_meta( $product->get_id(), '_cpo_d_length_option', $data['dimensions_data']['d_length_option'] );
				update_post_meta( $product->get_id(), '_cpo_convert_length', $data['dimensions_data']['convert_length'] );
				update_post_meta( $product->get_id(), '_cpo_d_width_option', $data['dimensions_data']['d_width_option'] );
				update_post_meta( $product->get_id(), '_cpo_convert_width', $data['dimensions_data']['convert_width'] );
				update_post_meta( $product->get_id(), '_cpo_d_height_option', $data['dimensions_data']['d_height_option'] );
				update_post_meta( $product->get_id(), '_cpo_convert_height', $data['dimensions_data']['convert_height'] );

				return array( 'dimensions_data' => $data['dimensions_data'] );
			} elseif ( 'nov_data' === $context ) {
				update_post_meta( $product->get_id(), '_cpo_nov_enable', $data['nov_data']['nov_enable'] );
				update_post_meta( $product->get_id(), '_cpo_wholesale_enable', $data['nov_data']['wholesale_enable'] );
				update_post_meta( $product->get_id(), '_cpo_nov', $data['nov_data']['nov'] );

				return array( 'nov_data' => $data['nov_data'] );
			}

			return array( 'error' => __( 'No context set. The data was not saved.', 'uni-cpo' ) );
		} catch ( Exception $e ) {
			return array( 'error' => $e->getMessage() );
		}
	}

	/**
	 * Saves content
	 *
	 * @since 4.0.0
	 *
	 * @param int $product_id
	 * @param string $content
	 * @param bool $is_json Whether $content is JSON object or already decoded to an array
	 *
	 * @return bool
	 */
	static public function save_content( $product_id, $content, $is_json = true ) {
		$product = wc_get_product( $product_id );

		if ( $product ) {
			do_action( 'before_cpo_save_content', $content, $product_id );

			if ( $is_json ) {
				$content = json_decode( $content, true );
			}

			$content = uni_cpo_encode( $content );
			$id      = update_post_meta( $product_id, '_cpo_content', $content );

			// update generated CSS
			delete_option( 'builder_css_cached_for' . $product_id );

			do_action( 'after_cpo_save_content', $product_id );

			return $id;
		}

		return false;
	}

	/**
	 * Updates content
	 *
	 * @since 4.0.0
	 *
	 * @param int $product_id
	 * @param string $content
	 *
	 * @return bool
	 */
	static public function update_content( $product_id, $content ) {
		$product = wc_get_product( $product_id );

		if ( $product ) {
			do_action( 'before_cpo_update_content', $content, $product_id );

			$id = update_post_meta( $product_id, '_cpo_content', $content );

			// update generated CSS
			update_option( 'builder_css_cached_for' . $product_id, '' );

			do_action( 'after_cpo_update_content', $product_id );

			return $id;
		}

		return false;
	}

}
