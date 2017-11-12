<?php
/**
 * Uni Cpo Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @author        MooMoo
 * @category    Core
 * @package    UniCpo/Functions
 * @version     4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly
}

// Include core functions (available in both admin and frontend).
include( 'uni-cpo-functions.php' );
include( 'uni-cpo-formatting-functions.php' );

/**
 * Display an Uni Cpo help tip.
 *
 * @since  4.0.0
 *
 * @param  string $tip Help tip text
 * @param  bool $allow_html Allow sanitized HTML if true or escape
 *
 * @return string
 */
function uni_cpo_help_tip( $tip, $allow_html = false, $args = array() ) {
	if ( $allow_html ) {
		$tip = uni_cpo_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	if ( isset( $args['type'] ) && 'warning' === $args['type'] ) {
		$css_class = 'uni-cpo-tooltip-warning';
	} else {
		$css_class = 'uni-cpo-tooltip';
	}

	return '<span
                class="' . $css_class . '"
                data-tip="' . $tip . '">
                </span>';
}

/**
 * Serialize and encode
 *
 * @return    string
 *
 * @access    public
 * @since     4.0.0
 */
function uni_cpo_encode( $value ) {

	$func = 'base64' . '_encode';

	return $func( maybe_serialize( $value ) );

}

/**
 * Decode and unserialize
 *
 * @return    string
 *
 * @access    public
 * @since     4.0.0
 */
function uni_cpo_decode( $value ) {

	$func  = 'base64' . '_decode';
	$value = $func( $value );

	return maybe_unserialize( $value );

}

/**
 * Get values of modules from multidimensional array
 *
 * @since  4.0.0
 *
 * @return array
 */
function uni_cpo_get_mod_values( $content ) {
	$new_content = array();
	if ( is_array( $content ) ) {
		foreach ( $content as $key => $value ) {
			if ( 'content' === $key || 'html' === $key ) {
				$new_content[] = $value;
			}
			if ( is_array( $value ) ) {
				$new_content = array_merge( $new_content, uni_cpo_get_mod_values( $value ) );
			}
		}
	}

	return $new_content;
}

//
function uni_cpo_get_modules_by_type( $data, $post_type = 'uni_cpo_option' ) {
	$query = new WP_Query(
		array(
			'post_type'      => $post_type,
			'meta_query'     => array(
				array(
					'key'     => '_module_type',
					'value'   => $data['type'],
					'compare' => '=',
				),
			),
			'posts_per_page' => - 1,
			'post__not_in'   => ( ! empty( $data['exclude_id'] ) ) ? array( $data['exclude_id'] ) : array()
		)
	);
	if ( ! empty( $query->posts ) ) {
		return $query->posts;
	} else {
		return false;
	}
}

//
function uni_cpo_is_slug_exists( $slug, $post_type = 'uni_cpo_option' ) {
	$query = new WP_Query( array( 'name' => $slug, 'post_type' => $post_type ) );
	if ( ! empty( $query->posts ) ) {
		return true;
	} else {
		return false;
	}
}

//
function uni_cpo_get_post_by_slug( $slug, $post_type = 'uni_cpo_option' ) {
	$query = new WP_Query( array( 'name' => $slug, 'post_type' => $post_type ) );
	if ( ! empty( $query->posts ) ) {
		return $query->posts[0];
	}

	return null;
}

//
function uni_cpo_get_posts_by_slugs( $slugs, $post_type = 'uni_cpo_option' ) {
	$query = new WP_Query( array( 'post_name__in' => $slugs, 'post_type' => $post_type, 'posts_per_page' => - 1 ) );
	if ( ! empty( $query->posts ) ) {
		return $query->posts;
	}

	return null;
}

//
function uni_cpo_get_posts_slugs( $post_type = 'uni_cpo_option' ) {
	$query = new WP_Query( array( 'post_type' => $post_type, 'posts_per_page' => - 1 ) );
	if ( ! empty( $query->posts ) ) {
		$slugs_list = wp_list_pluck( $query->posts, 'post_name' );

		return $slugs_list;
	}

	return null;
}

//
function uni_cpo_truncate_post_slug( $slug, $length = 200 ) {
	if ( strlen( $slug ) > $length ) {
		$decoded_slug = urldecode( $slug );
		if ( $decoded_slug === $slug ) {
			$slug = substr( $slug, 0, $length );
		} else {
			$slug = utf8_uri_encode( $decoded_slug, $length );
		}
	}

	return rtrim( $slug, '-' );
}

//
function uni_cpo_get_unique_slug( $slug ) {

	if ( empty( $slug ) ) {
		return array( 'unique' => false, 'slug' => false );
	}

	$suffix           = 2;
	$existed_slugs    = uni_cpo_get_posts_slugs();
	$reserved_slugs   = uni_cpo_get_reserved_option_slugs();
	$prohibited_slugs = array_merge( $existed_slugs, $reserved_slugs );
	$is_slug_valid    = ( ! in_array( UniCpo()->get_var_slug() . $slug, $prohibited_slugs ) ) ? true : false;

	if ( $is_slug_valid ) {
		return array( 'unique' => true, 'slug' => $slug );
	} else {
		do {
			$alt_slug      = uni_cpo_truncate_post_slug( $slug, 200 - ( strlen( $suffix ) + 1 ) ) . "_$suffix";
			$is_slug_valid = ( ! in_array( UniCpo()->get_var_slug() . $alt_slug, $prohibited_slugs ) ) ? true : false;
			$suffix ++;
		} while ( ! $is_slug_valid );

		return array( 'unique' => false, 'slug' => $alt_slug );
	}
}

function uni_cpo_get_similar_modules( $data ) {
	$items = array();
	if ( 'option' === $data['obj_type'] ) {
		$posts = uni_cpo_get_modules_by_type(
			array(
				'type'       => $data['type'],
				'exclude_id' => $data['pid']
			)
		);
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$module                     = uni_cpo_get_option( $post->ID );
				$items[ $module->get_id() ] = $module->get_slug();
			}
		}
	} elseif ( 'module' === $data['obj_type'] ) {
		$posts = uni_cpo_get_modules_by_type(
			array(
				'type'       => $data['type'],
				'exclude_id' => $data['pid']
			),
			'uni_module'
		);
		// TODO
	}

	return $items;
}


function uni_cpo_get_module_for_sync( $data ) {
	if ( 'option' === $data['obj_type'] ) {
		$module = uni_cpo_get_option( $data['pid'] );
		if ( $module ) {
			return $module->formatted_model_data();
		}
	} elseif ( 'module' === $data['obj_type'] ) {
		// TODO
	}

	return false;
}

function uni_cpo_get_similar_products_ids( $data ) {
	$query = new WP_Query(
		array(
			'post_type'      => 'product',
			'tax_query' => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'simple',
				),
			),
			'posts_per_page' => - 1,
			'post__not_in'   => ( ! empty( $data['pid'] ) ) ? array( $data['pid'] ) : array()
		)
	);
	if ( ! empty( $query->posts ) ) {
		return $query->posts;
	} else {
		return false;
	}
}

//////////////////////////////////////////////////////////////////////////////////////
// Calculation functions
//////////////////////////////////////////////////////////////////////////////////////

function uni_cpo_process_formula_with_non_option_vars( &$variables, $product_data ) {

	if ( is_array( $product_data['nov_data']['nov'] ) && ! empty( $product_data['nov_data']['nov'] ) ) {
		$nov      = $product_data['nov_data']['nov'][0];
		$var_name = '{' . UniCpo()->get_nov_slug() . $nov['slug'] . '}';
		if ( isset( $nov['roles'] ) && 'on' === $product_data['nov_data']['wholesale_enable'] ) {
			$formula = uni_cpo_get_role_based_formula( $nov );
		} else {
			$formula = ( isset( $nov['formula'] ) ) ? $nov['formula'] : '';
		}
		$formula                = uni_cpo_process_formula_with_vars( $formula, $variables );
		$nov_val                = uni_cpo_calculate_formula( $formula );
		$variables[ $var_name ] = $nov_val;
		array_splice( $product_data['nov_data']['nov'], 0, 1 );
		uni_cpo_process_formula_with_non_option_vars( $variables, $product_data );
	}

	return $variables;
}

function uni_cpo_get_role_based_formula( $nov ) {
	$current_user = wp_get_current_user();

	if ( 0 === $current_user->ID ) {
		return $nov['formula'];
	} else {
		$role = $current_user->roles ? $current_user->roles[0] : false;
		if ( in_array( $role, $nov['roles'] ) ) {
			return $nov[ $role ]['formula'];
		} else {
			return $nov['formula'];
		}
	}
}

function uni_cpo_process_formula_scheme( $variables, $product_data ) {

	foreach ( $product_data['formula_data']['formula_scheme'] as $scheme_key => $scheme_item ) {
		$formula_block     = $scheme_item['formula'];
		$rules_block       = json_decode( $scheme_item['rule'], true );
		$block_condition   = $rules_block['condition'];
		$is_passed_block   = false;
		$block_rules_count = count( $rules_block['rules'] );

		if ( $block_rules_count > 1 ) {
			$check_for_1 = array();
			$check_for_2 = array();
			$is_passed_2 = '';
			foreach ( $rules_block['rules'] as $rule_key => $rule_item ) {
				if ( isset( $rule_item['rules'] ) ) {
					$rule_1_condition = $rule_item['condition'];
					foreach ( $rule_item['rules'] as $rule_2_key => $rule_2_item ) {
						$check_for_2[] = uni_cpo_formula_condition_check( $rule_2_item, $variables );
					}

					if ( false === in_array( false, $check_for_2, true ) && 'AND' === $rule_1_condition ) {
						$is_passed_2 = true;
					} elseif ( false !== in_array( true, $check_for_2, true ) && 'OR' === $rule_1_condition ) {
						$is_passed_2 = true;
					} else {
						$is_passed_2 = false;
					}
				} else {
					$check_for_1[] = uni_cpo_formula_condition_check( $rule_item, $variables );
				}
			}
			if ( is_bool( $is_passed_2 ) ) {
				array_push( $check_for_1, $is_passed_2 );
			}

			if ( false === in_array( false, $check_for_1, true ) && 'AND' === $block_condition ) {
				$is_passed_block = true;
			} elseif ( false !== in_array( true, $check_for_1, true ) && 'OR' === $block_condition ) {
				$is_passed_block = true;
			} else {
				$is_passed_block = false;
			}

		} else {
			foreach ( $rules_block['rules'] as $rule_key => $rule_item ) {
				$is_passed_block = uni_cpo_formula_condition_check( $rule_item, $variables );
			}
		}

		if ( $is_passed_block ) {
			return $formula_block;
		}
	}

	return false;
}

// formula condition check
function uni_cpo_formula_condition_check( $rule, $variables ) {


	$var_name   = $rule['id'];
	$rule_value = $rule['value'];
	$is_passed  = false;

	switch ( $rule['operator'] ) {

		case 'less':
			if ( isset( $variables[ $var_name ] ) && floatval( $variables[ $var_name ] ) < floatval( $rule_value ) ) {
				$is_passed = true;
			}
			break;

		case 'less_or_equal':
			if ( isset( $variables[ $var_name ] ) && floatval( $variables[ $var_name ] ) <= floatval( $rule_value ) ) {
				$is_passed = true;
			}
			break;

		case 'equal':
			if ( isset( $variables[ $var_name ] ) && ! is_array( $variables[ $var_name ] ) && $variables[ $var_name ] === $rule_value ) {
				$is_passed = true;
			} elseif ( isset( $variables[ $var_name ] ) && is_array( $variables[ $var_name ] ) ) {
				foreach ( $variables[ $var_name ] as $value ) {
					if ( $value === $rule_value ) {
						$is_passed = true;
						break;
					}
				}
			}
			break;

		case 'not_equal':
			if ( isset( $variables[ $var_name ] ) && ! is_array( $variables[ $var_name ] ) && $variables[ $var_name ] !== $rule_value ) {
				$is_passed = true;
			} elseif ( isset( $variables[ $var_name ] ) && is_array( $variables[ $var_name ] ) ) {
				foreach ( $variables[ $var_name ] as $value ) {
					if ( $value !== $rule_value ) {
						$is_passed = true;
						break;
					}
				}
			}
			break;

		case 'greater_or_equal':
			if ( isset( $variables[ $var_name ] ) && floatval( $variables[ $var_name ] ) >= floatval( $rule_value ) ) {
				$is_passed = true;
			}
			break;

		case 'greater':
			if ( isset( $variables[ $var_name ] ) && floatval( $variables[ $var_name ] ) > floatval( $rule_value ) ) {
				$is_passed = true;
			}
			break;

		case 'is_empty':
			if ( ! isset( $variables[ $var_name ] ) || empty( $variables[ $var_name ] ) ) {
				$is_passed = true;
			}
			break;

		case 'is_not_empty':
			if ( ! empty( $variables[ $var_name ] ) ) {
				$is_passed = true;
			}
			break;

	}

	return $is_passed;
}

//
function uni_cpo_process_formula_with_vars( $main_formula, $variables = array() ) {
	$main_formula = preg_replace( '/\s+/', '', $main_formula );

	if ( ! empty( $variables ) ) {
		foreach ( $variables as $k => $v ) {
			if ( is_array( $v ) ) {
				if ( ! empty( $v ) ) {
					foreach ( $v as $k_child => $v_child ) {
						$search       = "/($k_child)/";
						$main_formula = preg_replace( $search, $v_child, $main_formula );
					}
				}
			} else {
				$search       = "/($k)/";
				$main_formula = preg_replace( $search, $v, $main_formula );
			}
		}

		$pattern      = "/{([^}]*)}/";
		$main_formula = preg_replace( $pattern, '0', $main_formula );
	} else {
		$pattern      = "/{([^}]*)}/";
		$main_formula = preg_replace( $pattern, '0', $main_formula );
	}

	return $main_formula;
}

//
function uni_cpo_calculate_formula( $main_formula = '' ) {

	if ( ! empty( $main_formula ) && 'disable' !== $main_formula ) {
		// change the all unused variables to zero, so formula calculation will not fail
		$pattern      = "/{([^}]*)}/";
		$main_formula = preg_replace( $pattern, '0', $main_formula );

		// calculate
		$m                  = new EvalMath;
		$m->suppress_errors = true;
		$calc_price         = $m->evaluate( $main_formula );
		$calc_price         = ( ! is_infinite( $calc_price ) && ! is_nan( $calc_price ) ) ? $calc_price : 0;

		return floatval( $calc_price );
	} else {
		return 0;
	}
}

//
function uni_cpo_option_js_condition_prepare( $scheme ) {
	$condition_operator = $scheme['condition'];
	$operator           = ( 'AND' === $condition_operator ) ? '&&' : '||';
	$rules              = $scheme['rules'];
	$rules_count        = count( $rules );

	if ( $rules_count > 1 ) {
		foreach ( $rules as $rule ) {
			if ( isset( $rule['rules'] ) ) {
				$statements[] = uni_cpo_option_js_condition_prepare( $rule );
			} else {
				$statements[] = uni_cpo_option_js_condition( $rule );
			}
		}
		$condition = '(' . implode( " $operator ", $statements ) . ')';
	} else {
		foreach ( $rules as $rule ) {
			if ( isset( $rule['rules'] ) ) {
				$statement = uni_cpo_option_js_condition_prepare( $rule );
			} else {
				$statement = uni_cpo_option_js_condition( $rule );
			}
		}
		$condition = '(' . $statement . ')';
	}

	return $condition;
}

// option condition js builder
function uni_cpo_option_js_condition( $rule ) {

	$cpo_var = 'formData';

	switch ( $rule['operator'] ) {
		case 'less':
			$statement = "{$cpo_var}.{$rule['id']} < {$rule['value']}";
			break;
		case 'less_or_equal':
			$statement = "{$cpo_var}.{$rule['id']} <= {$rule['value']}";
			break;
		case 'equal':
			if ( is_int( $rule['value'] ) ) {
				$statement = "parseInt({$cpo_var}.{$rule['id']}, 10) === parseInt({$rule['value']}, 10)";
			} elseif ( is_float( $rule['value'] ) ) {
				$statement = "parseFloat({$cpo_var}.{$rule['id']}) === parseFloat({$rule['value']})";
			} else {
				$statement = "{$cpo_var}.{$rule['id']} === '{$rule['value']}'";
			}
			break;
		case 'not_equal':
			if ( is_int( $rule['value'] ) ) {
				$statement = "parseInt({$cpo_var}.{$rule['id']}, 10) !== parseInt({$rule['value']}, 10)";
			} elseif ( is_float( $rule['value'] ) ) {
				$statement = "parseFloat({$cpo_var}.{$rule['id']}) !== parseFloat({$rule['value']})";
			} else {
				$statement = "{$cpo_var}.{$rule['id']} !== '{$rule['value']}'";
			}
			break;
		case 'greater_or_equal':
			$statement = "{$cpo_var}.{$rule['id']} >= {$rule['value']}";
			break;
		case 'greater':
			$statement = "{$cpo_var}.{$rule['id']} > {$rule['value']}";
			break;
		case 'is_empty':
			$statement = "(typeof {$cpo_var}.{$rule['id']} === 'undefined' || {$cpo_var}.{$rule['id']} === '')";
			break;
		case 'is_not_empty':
			$statement = "(typeof {$cpo_var}.{$rule['id']} !== 'undefined' && {$cpo_var}.{$rule['id']} !== '')";
			break;
	}

	return $statement;

}

//////////////////////////////////////////////////////////////////////////////////////
// WC related functions and hooks
//////////////////////////////////////////////////////////////////////////////////////
/**
 * Format the price with a currency symbol. Adapted from wc_price()
 *
 * @param $price
 * @param array $args
 *
 * @return string
 */
function uni_cpo_price( $price, $args = array() ) {

	extract( apply_filters( 'wc_price_args', wp_parse_args( $args, array(
		'ex_tax_label'       => false,
		'currency'           => '',
		'decimal_separator'  => wc_get_price_decimal_separator(),
		'thousand_separator' => wc_get_price_thousand_separator(),
		'decimals'           => wc_get_price_decimals(),
		'price_format'       => get_woocommerce_price_format(),
	) ) ) );

	$negative = $price < 0;
	$price    = apply_filters( 'raw_uni_cpo_price', floatval( $negative ? $price * - 1 : $price ) );
	$price    = apply_filters(
		'formatted_uni_cpo_price',
		number_format( $price, $decimals, $decimal_separator, $thousand_separator ),
		$price,
		$decimals,
		$decimal_separator,
		$thousand_separator
	);

	if ( apply_filters( 'uni_cpo_price_trim_zeros', false ) && $decimals > 0 ) {
		$price = wc_trim_zeros( $price );
	}

	$formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, get_woocommerce_currency_symbol( $currency ), $price );

	if ( $ex_tax_label && wc_tax_enabled() ) {
		$formatted_price .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
	}

	return apply_filters( 'uni_cpo_price', $formatted_price, $price, $args );

}

/**
 * Raw price (float)
 *
 * @param $price
 *
 * @return float
 */
function uni_cpo_price_raw( $price ) {

	$decimal_separator  = wc_get_price_decimal_separator();
	$thousand_separator = wc_get_price_thousand_separator();
	$decimals           = wc_get_price_decimals();

	$negative = $price < 0;
	$price    = apply_filters( 'raw_uni_cpo_price', floatval( $negative ? $price * - 1 : $price ) );
	$price    = apply_filters(
		'formatted_uni_cpo_price',
		number_format(
			$price,
			$decimals,
			$decimal_separator,
			$thousand_separator
		),
		$price,
		$decimals,
		$decimal_separator,
		$thousand_separator
	);

	return (float) $price;
}

// customers try to add a product to the cart from an archive page? let's check if it is possible to do!
add_filter( 'woocommerce_loop_add_to_cart_link', 'uni_cpo_add_to_cart_button', 10, 2 );
function uni_cpo_add_to_cart_button( $link, $product ) {

	$product_id     = intval( $product->get_id() );
	$product_custom = get_post_custom( $product_id );
	$product_type   = $product->get_type();

	if ( isset( $product_custom['_uni_cpo_display_options_enable'][0] )
	     && $product_custom['_uni_cpo_display_options_enable'][0] == true ) {
		$link = sprintf(
			'<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button product_type_%s">%s</a>',
			esc_url( get_permalink( $product_id ) ),
			esc_attr( $product_id ),
			esc_attr( $product->get_sku() ),
			esc_attr( isset( $quantity ) ? $quantity : 1 ),
			esc_attr( $product_type ),
			esc_html( __( 'Select options', 'uni-cpo' ) )
		);
	}

	return $link;
}

//
add_action( 'woocommerce_before_add_to_cart_button', 'uni_cpo_calculate_button_output', 15 );
function uni_cpo_calculate_button_output() {
	global $post;
	$product_custom = get_post_custom( $post->ID );

	if ( isset( $product_custom['_uni_cpo_price_calculation_btn_enable'][0] )
	     && $product_custom['_uni_cpo_price_calculation_btn_enable'][0] == true ) {

		$sBtnText = apply_filters(
			'cpo_calc_btn_text_filter',
			'<i class="fa fa-calculator" aria-hidden="true"></i>' . esc_html__( 'Calculate', 'uni-cpo' ),
			$post->ID
		);

		echo '<button type="button" id="js-uni-cpo-calculate-btn" class="uni-cpo-calculate-btn button alt">' . $sBtnText . '</button>';

	}
}

add_filter( 'woocommerce_get_price_html', 'uni_cpo_display_price_with_preffix', 10, 2 );
function uni_cpo_display_price_with_preffix( $price, $product ) {

	$product_id      = intval( $product->get_id() );
	$product_data    = Uni_Cpo_Product::get_product_data_by_id( $product_id );
	$product_post_id = 0;
	global $wp_query;

	if ( isset( $wp_query->queried_object->post_content )
	     && has_shortcode( $wp_query->queried_object->post_content, 'product_page' ) ) {
		if ( has_shortcode( $wp_query->queried_object->post_content, 'product_page' ) ) {
			$pattern = '\[(\[?)(product_page)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
			if ( preg_match_all( '/' . $pattern . '/s', $wp_query->queried_object->post_content, $matches )
			     && array_key_exists( 2, $matches )
			     && in_array( 'product_page', $matches[2] ) ) {
				foreach ( $matches[2] as $key => $value ) {
					if ( $value === 'product_page' ) {
						$parsed = shortcode_parse_atts( $matches[3][ $key ] );
						if ( is_array( $parsed ) ) {
							foreach ( $parsed as $attr_name => $attr_value ) {
								if ( $attr_name === 'id' ) {
									$product_post_id = intval( $attr_value );
									break 2;
								}
							}
						}
					}
				}
			}
		}
	}

	if ( 'on' === $product_data['settings_data']['cpo_enable'] && 'on' === $product_data['settings_data']['calc_enable']
	     && ! empty( $product_data['settings_data']['min_price'] )
	     && (
		     ( is_single() && $product_id !== $wp_query->queried_object_id )
		     || ( is_page() && $product_id !== $product_post_id )
		     || is_tax() || is_archive()
		     || ( is_single() && ! is_singular( 'product' ) && isset( $wp_query->queried_object->post_content )
		          && ! has_shortcode( $wp_query->queried_object->post_content, 'product_page' )
		     )
	     )
	) {

		$price         = wc_get_price_to_display( $product, array( 'price' => $product_data['settings_data']['min_price'] ) );
		$price         = apply_filters( 'uni_cpo_display_price_archive_page', $price, $product );
		$display_price = uni_cpo_price( $price );
		$display_price = sprintf( __( 'from %s', 'uni-cpo' ), $display_price );
		if ( $product->is_taxable() ) {
			$price = $display_price . $product->get_price_suffix( $price );
		} else {
			$price = $display_price;
		}

		return $price;
	} else {
		return $price;
	}
}

//
function uni_cpo_get_price_for_meta() {

	global $product;
	$product_id   = intval( $product->get_id() );
	$product_data = Uni_Cpo_Product::get_product_data_by_id( $product_id );

	if ( 'on' === $product_data['settings_data']['cpo_enable'] && 'on' === $product_data['settings_data']['calc_enable']
	     && ! empty( $product_data['settings_data']['min_price'] )
	) {
		$price = wc_get_price_to_display( $product, array( 'price' => $product_data['settings_data']['min_price'] ) );
		$price = apply_filters( 'uni_cpo_display_price_meta_tag', $price, $product );

		return $price;
	} else {
		return wc_get_price_to_display( $product );
	}

}

//
function uni_cpo_display_price_custom_meta() {

	global $product;
	$product_id   = intval( $product->get_id() );
	$product_data = Uni_Cpo_Product::get_product_data_by_id( $product_id );

	if ( 'on' === $product_data['settings_data']['cpo_enable'] && 'on' === $product_data['settings_data']['calc_enable']
	     && ! empty( $product_data['settings_data']['min_price'] )
	) {
		$price = wc_get_price_to_display( $product, array( 'price' => $product_data['settings_data']['min_price'] ) );
		$price = apply_filters( 'uni_cpo_display_price_meta_tag', $price, $product );
		echo '<meta itemprop="lowPrice" content="' . esc_attr( $price ) . '" />';
	} else {
		$price = wc_get_price_to_display( $product );
		echo '<meta itemprop="price" content="' . esc_attr( $price ) . '" />';
	}

}

//
function uni_cpo_get_display_price_reversed( $product, $price ) {

	$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
	$price_incl       = wc_get_price_including_tax( $product, array( 'qty' => 1, 'price' => $price ) );
	$price_excl       = wc_get_price_excluding_tax( $product, array( 'qty' => 1, 'price' => $price ) );
	$display_price    = $tax_display_mode == 'incl' ? $price_excl : $price_incl;

	return $display_price;
}

// displays a new and discounted price in the cart
function uni_cpo_change_cart_item_price( $price, $cart_item, $cart_item_key ) {

	$product_id   = $cart_item['product_id'];
	$product_data = Uni_Cpo_Product::get_product_data_by_id( $product_id );

	if ( 'on' === $product_data['settings_data']['cpo_enable']
	     && 'on' === $product_data['settings_data']['calc_enable']
	) {

		$product    = wc_get_product( $product_id );
		$price_calc = wc_get_price_to_display( $product, array(
			'qty'   => 1,
			'price' => $cart_item['_uni_cpo_price']
		) );

		$cpo_price = apply_filters( 'cpo_get_cart_price_calculated_raw', $price_calc, $product_data );
		$cpo_price = wc_price( $cpo_price );

		return $cpo_price;
	} else {
		return $price;
	}

}

//
add_action( 'woocommerce_before_calculate_totals', 'uni_cpo_before_calculate_totals', 10, 1 );
function uni_cpo_before_calculate_totals( $object ) {
	if ( method_exists( $object, 'get_cart' ) ) {
		foreach ( $object->get_cart() as $cart_item_key => $values ) {
			$product = $values['data'];

			if ( $product->is_type( 'simple' ) && ! empty( $object->coupons ) ) {
				foreach ( $object->coupons as $code => $coupon ) {
					if ( $coupon->is_valid()
					     && (
						     $coupon->is_valid_for_product( $product, $values )
						     || $coupon->is_valid_for_cart()
					     )
					) {
						if ( isset( $values['uni_cpo_price'] ) ) {
							$product->set_price( $values['uni_cpo_price'] );
						}
					}
				}
			}

		}
	}
}

// associate with order's meta
add_filter( 'woocommerce_add_cart_item_data', 'uni_cpo_add_cart_item_data', 10, 2 );
add_filter( 'woocommerce_get_cart_item_from_session', 'uni_cpo_get_cart_item_from_session', 10, 3 );
add_filter( 'woocommerce_add_cart_item', 'uni_cpo_add_cart_item', 10, 1 );
// get item data to display in cart and checkout page
add_filter( 'woocommerce_get_item_data', 'uni_cpo_get_item_data', 10, 2 );
// add meta data for each order item
add_action( 'woocommerce_checkout_create_order_line_item', 'uni_cpo_checkout_create_order_line_item', 10, 4 );

// adds custom option data to the cart
function uni_cpo_add_cart_item_data( $cart_item_data, $product_id ) {

	try {
		$product_data = Uni_Cpo_Product::get_product_data_by_id( $product_id );
		$form_data    = $_POST;

		if ( 'on' === $product_data['settings_data']['cpo_enable'] ) {

			$cart_item_data['_uni_cpo_calc_option']  = ( 'on' === $product_data['settings_data']['calc_enable'] )
				? true : false;
			$cart_item_data['_uni_cpo_cart_item_id'] = ( ! empty( $form_data['uni_cpo_cart_item_id'] ) )
				? $form_data['uni_cpo_cart_item_id']
				: '';
			// TODO create an array of values which must be unset and add a filter
			unset( $form_data['uni_cpo_cart_item_id'] );
			unset( $form_data['uni_cpo_product_id'] );
			unset( $form_data['add-to-cart'] );
			unset( $form_data['quantity'] );

			// it is isset when ordering again has been initiated
			// TODO "order again"
			if ( ! isset( $cart_item_data['_uni_cpo_data'] ) ) {
				$cart_item_data['_uni_cpo_data'] = $form_data;
			}

			if ( true === boolval( $cart_item_data['_uni_cpo_calc_option'] ) ) {
				$price = uni_cpo_calculate_price_in_cart( $cart_item_data, $product_id );
			} else {
				$product = wc_get_product( $product_id );
				$price   = $product->get_price();
			}
			$price                            = uni_cpo_price_raw( $price );
			$cart_item_data['_uni_cpo_price'] = $price;

		}

		return $cart_item_data;

	} catch ( Exception $e ) {
		if ( $e->getMessage() ) {
			wc_add_notice( $e->getMessage(), 'error' );
		}

		return false;
	}
}

//
function uni_cpo_get_cart_item_from_session( $session_data, $values, $key ) {

	$session_data['_uni_cpo_calc_option']  = ( isset( $values['_uni_cpo_calc_option'] ) )
		? boolval( $values['_uni_cpo_calc_option'] )
		: false;
	$session_data['_uni_cpo_cart_item_id'] = ( isset( $values['_uni_cpo_cart_item_id'] ) )
		? $values['_uni_cpo_cart_item_id']
		: '';
	$session_data['_uni_cpo_data']         = $values['_uni_cpo_data'];
	//$session_data['_uni_cpo_item_attachments']  = ( isset($values['_uni_cpo_item_attachments']) )
	// ? $values['_uni_cpo_item_attachments']
	// : array();

	if ( isset( $session_data['_uni_cpo_data'] ) ) {
		return uni_cpo_add_cart_item( $session_data );
	} else {
		return $session_data;
	}
}

function uni_cpo_add_cart_item( $cart_item_data ) {

	$product_id      = $cart_item_data['product_id'];
	$is_calc_enabled = ( isset( $cart_item_data['_uni_cpo_calc_option'] ) )
		? boolval( $cart_item_data['_uni_cpo_calc_option'] )
		: false;

	// price calc
	if ( true === $is_calc_enabled && isset( $cart_item_data['_uni_cpo_data'] ) ) {
		$price                            = uni_cpo_calculate_price_in_cart( $cart_item_data, $product_id );
		$price                            = uni_cpo_price_raw( $price );
		$cart_item_data['_uni_cpo_price'] = $price;
		$cart_item_data['data']->set_price( $cart_item_data['_uni_cpo_price'] );
	}

	return $cart_item_data;
}

//
function uni_cpo_get_item_data( $item_data, $cart_item ) {

	if ( ! empty( $cart_item['_uni_cpo_data'] ) ) {

		// saves an information about chosen options and their values in cart meta
		$form_data          = $cart_item['_uni_cpo_data'];
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
						$display_key   = uni_cpo_sanitize_label( $option->cpo_order_label() );
						$display_value = $filtered_form_data[ $option->get_slug() ];
						if ( is_callable( array( $option, 'get_cpo_suboptions' ) ) ) {
							$suboptions_data = $option->get_cpo_suboptions();
							$suboptions      = array();
							if ( isset( $suboptions_data['data']['cpo_radio_options'] ) ) {
								$suboptions = $suboptions_data['data']['cpo_radio_options'];
							} elseif ( isset( $suboptions_data['data']['cpo_select_options'] ) ) {
								$suboptions = $suboptions_data['data']['cpo_select_options'];
							}
							if ( ! empty( $suboptions ) ) {
								foreach ( $suboptions as $suboption ) {
									if ( $filtered_form_data[ $option->get_slug() ] === $suboption['slug'] ) {
										$display_value = $suboption['label'];
										break;
									}
								}
							}
						}
						$item_data[] = array(
							'name'  => $display_key,
							'value' => $display_value
						);
					}
				}
			}
		}

	}

	return $item_data;
}

// adds meta info for order items
function uni_cpo_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
	if ( isset( $values['_uni_cpo_data'] ) ) {
		$form_data = $values['_uni_cpo_data'];

		foreach ( $form_data as $name => $value ) {
			$item->add_meta_data( '_' . $name, $value );
		}
	}
}

//
function uni_cpo_calculate_price_in_cart( $cart_item_data, $product_id ) {

	try {

		$product          = wc_get_product( $product_id );
		$product_data     = Uni_Cpo_Product::get_product_data_by_id( $product_id );
		$form_data        = $cart_item_data['_uni_cpo_data'];
		$variables        = array();
		$is_calc_disabled = false;
		$formatted_vars   = array();

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
			$main_formula = uni_cpo_process_formula_with_vars( $main_formula, $variables );

			// calculates formula
			$price_calculated = uni_cpo_calculate_formula( $main_formula );

			$price_min = $product_data['settings_data']['min_price'];
			$price_max = $product_data['settings_data']['max_price'];

			// check for min price
			if ( $price_calculated < $price_min ) {
				$price_calculated = $price_min;
			}

			// check for max price
			if ( ! empty( $price_max ) && $price_calculated >= $price_max ) {
				$is_calc_disabled = true;
			}

			if ( true !== $is_calc_disabled ) {
				// filter, so 3rd party scripts can hook up
				$price_calculated = apply_filters(
					'uni_cpo_in_cart_calculated_price',
					$price_calculated,
					$product,
					$filtered_form_data
				);

				return $price_calculated;
			} else {
				return $price_max;
			}
		} else {
			return 0;
		}

	} catch ( Exception $e ) {
		return new WP_Error( 'cart-error', $e->getMessage() );
	}
}
