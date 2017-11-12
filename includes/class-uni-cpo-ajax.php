<?php
/*
*   Uni_Cpo_Ajax Class
*
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Uni_Cpo_Ajax {

	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
		add_action( 'template_redirect', array( __CLASS__, 'do_cpo_ajax' ), 0 );
		self::add_ajax_events();
	}

	/**
	 * Get Ajax Endpoint.
	 */
	public static function get_endpoint( $request = '' ) {
		return esc_url_raw( add_query_arg( 'cpo-ajax', $request ) );
	}

	/**
	 * Set CPO AJAX constant and headers.
	 */
	public static function define_ajax() {
		if ( ! empty( $_GET['cpo-ajax'] ) ) {
			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}
			if ( ! defined( 'CPO_DOING_AJAX' ) ) {
				define( 'CPO_DOING_AJAX', true );
			}
			if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
				@ini_set( 'display_errors', 0 ); // Turn off display_errors during AJAX events to prevent malformed JSON
			}
			$GLOBALS['wpdb']->hide_errors();
		}
	}

	/**
	 * Send headers for CPO Ajax Requests
	 */
	private static function cpo_ajax_headers() {
		send_origin_headers();
		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		@header( 'X-Robots-Tag: noindex' );
		send_nosniff_header();
		nocache_headers();
		status_header( 200 );
	}

	/**
	 * Check for CPO Ajax request and fire action.
	 */
	public static function do_cpo_ajax() {
		global $wp_query;

		if ( ! empty( $_GET['cpo-ajax'] ) ) {
			$wp_query->set( 'cpo-ajax', sanitize_text_field( $_GET['cpo-ajax'] ) );
		}

		if ( $action = $wp_query->get( 'cpo-ajax' ) ) {
			self::cpo_ajax_headers();
			do_action( 'cpo_ajax_' . sanitize_text_field( $action ) );
			die();
		}
	}

	/**
	 *   Hook in methods
	 */
	public static function add_ajax_events() {

		$aAjaxEvents = array(
			'uni_cpo_save_content'               => false,
			'uni_cpo_delete_content'             => false,
			'uni_cpo_save_model'                 => false,
			'uni_cpo_fetch_similar_modules'      => false,
			'uni_cpo_save_settings_data'         => false,
			'uni_cpo_save_formula_data'          => false,
			'uni_cpo_save_nov_data'              => false,
			'uni_cpo_sync_with_module'           => false,
			'uni_cpo_price_calc'                 => true
		);

		foreach ( $aAjaxEvents as $sAjaxEvent => $bPriv ) {
			add_action( 'wp_ajax_' . $sAjaxEvent, array( __CLASS__, $sAjaxEvent ) );

			if ( $bPriv ) {
				add_action( 'wp_ajax_nopriv_' . $sAjaxEvent, array( __CLASS__, $sAjaxEvent ) );
			}
		}

	}

	/**
	 *   uni_cpo_save_content
	 */
	public static function uni_cpo_save_content() {
		check_ajax_referer( 'uni_cpo_builder', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( - 1 );
		}

		try {
			$content = uni_cpo_sanitize_text( $_POST['data'] );
			$result = Uni_Cpo_Product::save_content( absint( $_POST['product_id'] ), $content );

			if ( $result ) {
				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 *   uni_cpo_delete_content
	 */
	public static function uni_cpo_delete_content() {
		check_ajax_referer( 'uni_cpo_builder', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( - 1 );
		}

		try {
			$result = Uni_Cpo_Product::delete_content( absint( $_POST['product_id'] ) );

			if ( $result ) {
				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 *   uni_cpo_save_model
	 */
	public static function uni_cpo_save_model() {
		check_ajax_referer( 'uni_cpo_builder', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( - 1 );
		}

		try {
			$data = $_POST['model'];

			$post_id        = ( ! empty( $data['pid'] ) ) ? absint( $data['pid'] ) : 0;
			$model_obj_type = ( ! empty( $data['obj_type'] ) ) ? uni_cpo_clean( $data['obj_type'] ) : '';
			$model_type     = ( ! empty( $data['type'] ) ) ? uni_cpo_clean( $data['type'] ) : '';

			if ( ! $model_type ) {
				throw new Exception( __( 'Invalid model type', 'uni-cpo' ) );
			}

			if ( ! $model_obj_type ) {
				throw new Exception( __( 'Invalid builder model object type', 'uni-cpo' ) );
			}

			if ( $post_id > 0 ) {
				$model = uni_cpo_get_model( $model_obj_type, $post_id );
			} else {
				$model = uni_cpo_get_model( $model_obj_type, $post_id, $model_type );
			}

			if ( ! $model ) {
				throw new Exception( __( 'Invalid model', 'uni-cpo' ) );
			}

			if ( 'option' === $model_obj_type ) {
				$cpo_general      = $data['settings']['cpo_general'];
				$slug_being_saved = ( ! empty( $cpo_general['main']['cpo_slug'] ) )
					? uni_cpo_clean( $cpo_general['main']['cpo_slug'] )
					: sanitize_title_with_dashes( uniqid( 'option_' ) );

				if ( empty( $model->get_slug() ) ) {
					// slug is empty, it is a new option
					$slug_check_result = uni_cpo_get_unique_slug( $slug_being_saved );
				} elseif ( ! empty( $model->get_slug() ) ) {
					if ( UniCpo()->get_var_slug() . $slug_being_saved !== $model->get_slug() ) {
						// looks like slug is going to be changed, so let's check its uniqueness
						$slug_check_result = uni_cpo_get_unique_slug( $slug_being_saved );
					} else {
						$slug_check_result = array(
							'unique' => true,
							'slug'   => $model->get_slug()
						);
					}
				}

				if ( ! isset( $slug_check_result ) ) {
					throw new Exception( __( 'Something went srong', 'uni-cpo' ) );
				}

				if ( $slug_check_result['unique'] && $slug_check_result['slug'] ) {

					unset( $data['settings']['general']['status'] );
					$data['settings']['cpo_general']['main']['cpo_slug'] = '';

					$props = array(
						'slug' => $slug_check_result['slug'],
					);
					foreach ( $data['settings'] as $data_name => $data_data ) {
						$data_name = uni_cpo_clean( $data_name );
						$data_data = uni_cpo_clean( $data_data );
						$props[ $data_name ] = $data_data;
					}

					$model->set_props( $props );
					$model->save();
					$model_data = $model->formatted_model_data();

					wp_send_json_success( $model_data );

				} elseif ( ! $slug_check_result['unique'] && $slug_check_result['slug'] ) {
					wp_send_json_error( array( 'error' => $slug_check_result ) );
				}

			} elseif ( 'module' === $model_obj_type ) {
				// TODO
			}

		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 *   uni_cpo_fetch_similar_modules
	 */
	public static function uni_cpo_fetch_similar_modules() {
		check_ajax_referer( 'uni_cpo_builder', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( - 1 );
		}

		try {
			$data = uni_cpo_clean( $_POST );

			if ( ! isset( $data['type'] ) || ! isset( $data['obj_type'] ) ) {
				throw new Exception( __( 'Type is not specified', 'uni-cpo' ) );
			}

			$result = uni_cpo_get_similar_modules( $data );

			if ( $result ) {
				wp_send_json_success( $result );
			} else {
				wp_send_json_error();
			}
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 *   uni_cpo_save_settings_data
	 */
	public static function uni_cpo_save_settings_data() {
		check_ajax_referer( 'uni_cpo_builder', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( - 1 );
		}

		try {
			$model                 = $_POST['model'];
			$data['product_id']    = absint( $model['id'] );
			$data['settings_data'] = uni_cpo_clean( $model['settingsData'] );
			$result                = Uni_Cpo_Product::save_product_data( $data, 'settings_data' );

			if ( ! isset( $result['error'] ) ) {
				wp_send_json_success( $result );
			} else {
				wp_send_json_error( $result );
			}
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 *   uni_cpo_save_formula_data
	 */
	public static function uni_cpo_save_formula_data() {
		check_ajax_referer( 'uni_cpo_builder', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( - 1 );
		}

		try {
			$model                = $_POST['model'];
			$data['product_id']   = absint( $model['id'] );
			$data['formula_data'] = uni_cpo_clean( $model['formulaData'] );
			$result               = Uni_Cpo_Product::save_product_data( $data, 'formula_data' );

			if ( ! isset( $result['error'] ) ) {
				wp_send_json_success( $result );
			} else {
				wp_send_json_error( $result );
			}
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 *   uni_cpo_save_nov_data
	 */
	public static function uni_cpo_save_nov_data() {
		check_ajax_referer( 'uni_cpo_builder', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( - 1 );
		}

		try {
			$model              = $_POST['model'];
			$data['product_id'] = absint( $model['id'] );
			$data['nov_data']   = uni_cpo_clean( $model['novData'] );
			$result             = Uni_Cpo_Product::save_product_data( $data, 'nov_data' );

			if ( ! isset( $result['error'] ) ) {
				wp_send_json_success( $result );
			} else {
				wp_send_json_error( $result );
			}
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 *   uni_cpo_sync_with_module
	 */
	public static function uni_cpo_sync_with_module() {
		check_ajax_referer( 'uni_cpo_builder', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( - 1 );
		}

		try {
			$data = uni_cpo_clean( $_POST );

			if ( ! isset( $data['obj_type'] ) ) {
				throw new Exception( __( 'Type is not specified', 'uni-cpo' ) );
			}

			if ( ! isset( $data['pid'] ) ) {
				throw new Exception( __( 'Target post is not chosen', 'uni-cpo' ) );
			}

			if ( ! isset( $data['method'] ) ) {
				throw new Exception( __( 'Sync method is not chosen', 'uni-cpo' ) );
			}

			$result = uni_cpo_get_module_for_sync( $data );

			if ( $result ) {
				wp_send_json_success( $result );
			} else {
				wp_send_json_error();
			}
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 *   uni_cpo_price_calc
	 */
	public static function uni_cpo_price_calc() {
		check_ajax_referer( 'uni_cpo_frontend', 'security' );

		try {

			$form_data = uni_cpo_clean( $_POST['data'] );

			if ( ! isset( $form_data['product_id'] ) ) {
				throw new Exception( __( 'Product ID is not set', 'uni-cpo' ) );
			}

			$product_id       = absint( $form_data['product_id'] );
			$product          = wc_get_product( $product_id );
			$product_data     = Uni_Cpo_Product::get_product_data_by_id( $product_id );
			$variables        = array();
			$price_vars       = array();
			$extra_data       = array( 'order_product' => 'enabled' );
			$is_calc_disabled = false;
			$formatted_vars   = array();

			if ( 'on' === $product_data['settings_data']['cpo_enable']
			     && 'on' === $product_data['settings_data']['calc_enable'] ) {

				$main_formula = $product_data['formula_data']['main_formula'];

				$filtered_form_data = array_filter(
					$form_data,
					function ( $k ) {
						return false !== strpos( $k, UniCpo()->get_var_slug() );
					},
					ARRAY_FILTER_USE_KEY
				);

				if ( ! empty( $filtered_form_data ) ) {
					$posts = uni_cpo_get_posts_by_slugs( array_keys( $filtered_form_data ) );
					if ( ! empty( $posts ) ) {
						foreach ( $posts as $post ) {
							$option = uni_cpo_get_option( $post->ID );
							if ( is_object( $option ) ) {
								$calculate_result = $option->calculate( $filtered_form_data );
								if ( ! empty( $calculate_result ) ) {
									foreach ( $calculate_result as $k => $v ) {
										$variables[ '{' . $k . '}' ] = $v['calc'];
									}
								}
							}
						}
					}
				}
				$variables['{uni_cpo_price}'] = $product->get_price();

				// non option variables
				if ( 'on' === $product_data['nov_data']['nov_enable']
				     && ! empty( $product_data['nov_data']['nov'] )
				) {
					$variables = uni_cpo_process_formula_with_non_option_vars( $variables, $product_data );
				}

				$temp_variables = $variables;
				unset( $temp_variables['{uni_cpo_price}'] );
				array_walk(
					$temp_variables,
					function ( &$v, $k ) use ( $filtered_form_data, &$formatted_vars ) {
						$k                    = trim( $k, '{}' );
						$formatted_vars[ $k ] = ( isset( $filtered_form_data[ $k ] ) )
							? $filtered_form_data[ $k ]
							: $v;
					}
				);

				// formula conditional logic
				if ( 'on' === $product_data['formula_data']['rules_enable']
				     && ! empty( $product_data['formula_data']['formula_scheme'] )
				     && is_array( $product_data['formula_data']['formula_scheme'] )
				) {

					$conditional_formula = uni_cpo_process_formula_scheme( $formatted_vars, $product_data );
					if ( $conditional_formula ) {
						$main_formula = $conditional_formula;
					}

				}

				if ( 'disable' === $main_formula ) {
					$is_calc_disabled = true;
				}

				//
				if ( ! $is_calc_disabled ) {
					//print_r(' / formula before: ' . $main_formula);
					$main_formula = uni_cpo_process_formula_with_vars( $main_formula, $variables );
					//print_r(' / formula after: ' . $main_formula);

					// calculates formula
					$price_calculated = uni_cpo_calculate_formula( $main_formula );

					$price_min = $product_data['settings_data']['min_price'];
					$price_max = $product_data['settings_data']['max_price'];

					// check for min price
					if ( $price_calculated < $price_min ) {
						$price_calculated = $price_min;
					}
					//print_r('calc price: ' . $price_calculated);

					// check for max price
					if ( ! empty( $price_max ) && $price_calculated >= $price_max ) {
						$is_calc_disabled = true;
					}

					if ( true !== $is_calc_disabled ) {

						// filter, so 3rd party scripts can hook up
						$price_calculated = apply_filters(
							'uni_cpo_ajax_calculated_price',
							$price_calculated,
							$product,
							$filtered_form_data
						);

						$price_display = wc_get_price_to_display(
							$product,
							array( 'qty' => 1, 'price' => $price_calculated )
						);

						if ( $product->is_taxable() ) {
							$price_display_tax_rev = uni_cpo_get_display_price_reversed( $product, $price_calculated );
							// Returns the price with suffix inc/excl tax opposite to one above
							$price_display_suffix = $product->get_price_suffix( $price_calculated, 1 );
						}

						$price_vars['price'] = apply_filters(
							'uni_cpo_ajax_calculation_price_tag_filter',
							uni_cpo_price( $price_display ),
							$price_display
						);

						$price_vars['raw_price'] = $price_calculated;
						$price_vars['raw_total'] = $price_vars['raw_price'] * $form_data['quantity'];
						$price_vars['total']     = uni_cpo_price( $price_vars['raw_total'] );
						if ( $product->is_taxable() ) {
							$price_vars['raw_price_tax_rev'] = $price_display_tax_rev;
							$price_vars['raw_total_tax_rev'] = $price_vars['raw_price_tax_rev'] * $form_data['quantity'];
							$price_vars['total_tax_rev']     = uni_cpo_price( $price_vars['raw_total_tax_rev'] );
						}

						// price and total with suffixes
						if ( $product->is_taxable() ) {

							// price with suffix - strips unnecessary
							$price_display_suffix = str_replace(
								' <small class="woocommerce-price-suffix">',
								'',
								$price_display_suffix );
							$price_display_suffix = str_replace(
								' </small>',
								'',
								$price_display_suffix );

							// total with suffix
							// creates 'with suffix' value for total
							if ( get_option( 'woocommerce_prices_include_tax' ) === 'no'
							     && get_option( 'woocommerce_tax_display_shop' ) == 'incl' ) {
								$total_suffix = $product->get_price_suffix( $price_vars['raw_price_tax_rev'] * $form_data['quantity'] );
							} elseif ( get_option( 'woocommerce_prices_include_tax' ) === 'yes'
							           && get_option( 'woocommerce_tax_display_shop' ) == 'incl' ) {
								$total_suffix = $product->get_price_suffix( $price_vars['raw_price'] * $form_data['quantity'] );
							} elseif ( get_option( 'woocommerce_prices_include_tax' ) === 'no'
							           && get_option( 'woocommerce_tax_display_shop' ) == 'excl' ) {
								$total_suffix = $product->get_price_suffix( $price_vars['raw_price'] * $form_data['quantity'] );
							} elseif ( get_option( 'woocommerce_prices_include_tax' ) === 'yes'
							           && get_option( 'woocommerce_tax_display_shop' ) == 'excl' ) {
								$total_suffix = $product->get_price_suffix( $price_vars['raw_price_tax_rev'] * $form_data['quantity'] );
							}

							$total_suffix = str_replace( ' <small class="woocommerce-price-suffix">', '', $total_suffix );
							$total_suffix = str_replace( ' </small>', '', $total_suffix );
							$total_suffix = str_replace( '<span class="amount">', '', $total_suffix );
							$total_suffix = str_replace( '</span>', '', $total_suffix );

							// debug
							//print_r('$sTotalWithSuffix: '.$sTotalWithSuffix.' | ');

							$price_vars['price_suffix'] = $price_display_suffix;
							$price_vars['total_suffix'] = $total_suffix;

						}

					} else {
						if ( true === $is_calc_disabled ) {  // ordering is disabled

							$price_display       = 0;
							$price_vars['price'] = apply_filters(
								'uni_cpo_ajax_calculation_price_tag_disabled_filter',
								uni_cpo_price( $price_display ),
								$price_display
							);
							$extra_data          = array( 'order_product' => 'disabled' );

						}
					}

					$result['formatted_vars'] = $formatted_vars;
					$result['price_vars']     = $price_vars;
					$result['extra_data']     = $extra_data;

					wp_send_json_success( $result );

				} else {
					throw new Exception( __( 'Price calculation has been disabled during the calculation', 'uni-cpo' ) );
				}

			} else {
				throw new Exception( __( 'Price calculation is disabled in settings', 'uni-cpo' ) );
			}

		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

}

Uni_Cpo_Ajax::init();
