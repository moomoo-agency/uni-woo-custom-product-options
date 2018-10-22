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

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

// Include core functions (available in both admin and frontend).
include 'uni-cpo-functions.php';
include 'uni-cpo-formatting-functions.php';
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
function uni_cpo_help_tip( $tip, $allow_html = false, $args = array() )
{
    
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
function uni_cpo_encode( $value )
{
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
function uni_cpo_decode( $value )
{
    $func = 'base64' . '_decode';
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
function uni_cpo_get_mod_values( $content )
{
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
function uni_cpo_get_modules_by_type( $data, $post_type = 'uni_cpo_option' )
{
    $query = new WP_Query( array(
        'post_type'      => $post_type,
        'meta_query'     => array( array(
        'key'     => '_module_type',
        'value'   => $data['type'],
        'compare' => '=',
    ) ),
        'posts_per_page' => -1,
        'post__not_in'   => ( !empty($data['exclude_id']) ? array( $data['exclude_id'] ) : array() ),
    ) );
    
    if ( !empty($query->posts) ) {
        return $query->posts;
    } else {
        return false;
    }

}

//
function uni_cpo_is_slug_exists( $slug, $post_type = 'uni_cpo_option' )
{
    $query = new WP_Query( array(
        'name'      => $slug,
        'post_type' => $post_type,
    ) );
    
    if ( !empty($query->posts) ) {
        return true;
    } else {
        return false;
    }

}

//
function uni_cpo_get_post_by_slug( $slug, $post_type = 'uni_cpo_option' )
{
    $query = new WP_Query( array(
        'name'      => $slug,
        'post_type' => $post_type,
    ) );
    if ( !empty($query->posts) ) {
        return $query->posts[0];
    }
    return null;
}

//
function uni_cpo_get_posts_by_slugs( $slugs, $post_type = 'uni_cpo_option' )
{
    $query = new WP_Query( array(
        'post_name__in'  => $slugs,
        'post_type'      => $post_type,
        'posts_per_page' => -1,
        'orderby'        => 'post_name__in',
    ) );
    if ( !empty($query->posts) ) {
        return $query->posts;
    }
    return null;
}

//
function uni_cpo_get_posts_by_ids( $ids, $post_type = 'uni_cpo_option' )
{
    $query = new WP_Query( array(
        'post__in'       => $ids,
        'post_type'      => $post_type,
        'posts_per_page' => -1,
        'orderby'        => 'post__in',
    ) );
    if ( !empty($query->posts) ) {
        return $query->posts;
    }
    return null;
}

//
function uni_cpo_get_posts_slugs( $post_type = 'uni_cpo_option' )
{
    $query = new WP_Query( array(
        'post_type'      => $post_type,
        'posts_per_page' => -1,
    ) );
    
    if ( !empty($query->posts) ) {
        $slugs_list = wp_list_pluck( $query->posts, 'post_name' );
        return $slugs_list;
    }
    
    return null;
}

//
function uni_cpo_truncate_post_slug( $slug, $length = 200 )
{
    
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
function uni_cpo_get_unique_slug( $slug )
{
    if ( empty($slug) ) {
        return array(
            'unique' => false,
            'slug'   => false,
        );
    }
    $suffix = 2;
    $existed_slugs = uni_cpo_get_posts_slugs();
    $reserved_slugs = uni_cpo_get_reserved_option_slugs();
    $prohibited_slugs = array_merge( $existed_slugs, $reserved_slugs );
    $is_slug_valid = ( !in_array( UniCpo()->get_var_slug() . $slug, $prohibited_slugs ) ? true : false );
    
    if ( $is_slug_valid ) {
        return array(
            'unique' => true,
            'slug'   => $slug,
        );
    } else {
        do {
            $alt_slug = uni_cpo_truncate_post_slug( $slug, 200 - (strlen( $suffix ) + 1) ) . "_{$suffix}";
            $is_slug_valid = ( !in_array( UniCpo()->get_var_slug() . $alt_slug, $prohibited_slugs ) ? true : false );
            $suffix++;
        } while (!$is_slug_valid);
        return array(
            'unique' => false,
            'slug'   => $alt_slug,
        );
    }

}

function uni_cpo_get_similar_modules( $data )
{
    $items = array();
    
    if ( 'option' === $data['obj_type'] ) {
        $posts = uni_cpo_get_modules_by_type( array(
            'type'       => $data['type'],
            'exclude_id' => $data['pid'],
        ) );
        if ( !empty($posts) ) {
            foreach ( $posts as $post ) {
                $module = uni_cpo_get_option( $post->ID );
                $items[$module->get_id()] = $module->get_slug();
            }
        }
    } elseif ( 'module' === $data['obj_type'] ) {
        $posts = uni_cpo_get_modules_by_type( array(
            'type'       => $data['type'],
            'exclude_id' => $data['pid'],
        ), 'uni_module' );
        // TODO
    }
    
    return $items;
}

function uni_cpo_get_module_for_sync( $data )
{
    
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

function uni_cpo_get_similar_products_ids( $data )
{
    $query = new WP_Query( array(
        'post_type'      => 'product',
        'tax_query'      => array( array(
        'taxonomy' => 'product_type',
        'field'    => 'slug',
        'terms'    => 'simple',
    ) ),
        'posts_per_page' => -1,
        'post__not_in'   => ( !empty($data['pid']) ? array( $data['pid'] ) : array() ),
    ) );
    
    if ( !empty($query->posts) ) {
        return $query->posts;
    } else {
        return false;
    }

}

function uni_cpo_get_settings_data_sanitized( $data_data, $data_name )
{
    $original_data = $data_data;
    $data_data = uni_cpo_clean( $data_data );
    return apply_filters(
        'uni_cpo_filter_settings_data',
        $data_data,
        $original_data,
        $data_name
    );
}

add_filter(
    'uni_cpo_filter_settings_data',
    'uni_cpo_filter_settings_data_func',
    10,
    3
);
function uni_cpo_filter_settings_data_func( $data_data, $original_data, $data_name )
{
    if ( 'general' === $data_name ) {
        $data_data['main']['content'] = ( !empty($original_data['main']['content']) ? uni_cpo_sanitize_text( $original_data['main']['content'] ) : '' );
    }
    
    if ( 'cpo_general' === $data_name ) {
        $data_data['advanced']['cpo_tooltip'] = ( !empty($original_data['advanced']['cpo_tooltip']) ? uni_cpo_sanitize_text( stripslashes_deep( $original_data['advanced']['cpo_tooltip'] ) ) : '' );
        $data_data['main']['cpo_notice_text'] = ( !empty($original_data['main']['cpo_notice_text']) ? html_entity_decode( sanitize_text_field( $original_data['main']['cpo_notice_text'] ) ) : '' );
    }
    
    if ( 'cpo_rules' === $data_name ) {
        $data_data['data'] = ( !empty($original_data['data']) ? $original_data['data'] : '' );
    }
    
    if ( 'cpo_validation' === $data_name ) {
        $data_data['main'] = ( !empty($original_data['main']) ? $original_data['main'] : '' );
        $data_data['logic'] = ( !empty($original_data['logic']) ? $original_data['logic'] : '' );
    }
    
    return $data_data;
}

function uni_cpo_option_apply_changes_walk( $v, $k, $d )
{
    
    if ( is_array( $v ) && !empty($v) && isset( $d[1][$k] ) ) {
        array_walk( $v, 'uni_cpo_option_apply_changes_walk', array( &$d[0][$k], $d[1][$k] ) );
    } elseif ( !is_array( $v ) && isset( $d[1][$v] ) ) {
        $d[0][$v] = $d[1][$v];
    } elseif ( !is_array( $v ) ) {
        $d[0][$v] = array();
    }

}

//////////////////////////////////////////////////////////////////////////////////////
// Calculation functions
//////////////////////////////////////////////////////////////////////////////////////
function uni_cpo_process_formula_with_non_option_vars( &$variables, $product_data, &$formatted_vars )
{
    
    if ( is_array( $product_data['nov_data']['nov'] ) && !empty($product_data['nov_data']['nov']) ) {
        
        if ( isset( $product_data['nov_data']['nov'][0] ) ) {
            $nov = $product_data['nov_data']['nov'][0];
        } else {
            $nov = $product_data['nov_data']['nov'][1];
        }
        
        $var_name = '{' . UniCpo()->get_nov_slug() . $nov['slug'] . '}';
        $formula = 0;
        
        if ( isset( $nov['roles'] ) && 'on' === $product_data['nov_data']['wholesale_enable'] && (!isset( $nov['matrix']['enable'] ) || 'on' !== $nov['matrix']['enable']) ) {
            $formula = uni_cpo_get_role_based_nov_formula( $nov );
        } elseif ( isset( $nov['matrix']['enable'] ) && 'on' === $nov['matrix']['enable'] ) {
        } else {
            $formula = ( isset( $nov['formula'] ) ? $nov['formula'] : '' );
        }
        
        $formula = uni_cpo_process_formula_with_vars( $formula, $variables );
        $nov_val = uni_cpo_calculate_formula( $formula );
        $variables[$var_name] = $nov_val;
        $formatted_vars[UniCpo()->get_nov_slug() . $nov['slug']] = $nov_val;
        array_splice( $product_data['nov_data']['nov'], 0, 1 );
        uni_cpo_process_formula_with_non_option_vars( $variables, $product_data, $formatted_vars );
    }
    
    return $variables;
}

function uni_cpo_get_role_based_nov_formula( $nov )
{
    $current_user = wp_get_current_user();
    
    if ( 0 === $current_user->ID ) {
        return $nov['formula'];
    } else {
        $role = ( $current_user->roles ? $current_user->roles[0] : false );
        
        if ( in_array( $role, $nov['roles'] ) ) {
            return $nov[$role]['formula'];
        } else {
            return $nov['formula'];
        }
    
    }

}

function uni_cpo_process_formula_scheme( $variables, $product_data, $purpose = 'price' )
{
    
    if ( 'price' === $purpose ) {
        $scheme_data = $product_data['formula_data']['formula_scheme'];
    } elseif ( 'weight' === $purpose ) {
        $scheme_data = $product_data['weight_data']['weight_scheme'];
    } elseif ( 'option_rules' === $purpose ) {
        $scheme_data = $product_data;
    }
    
    if ( !isset( $scheme_data ) ) {
        return false;
    }
    foreach ( $scheme_data as $scheme_key => $scheme_item ) {
        $formula_block = $scheme_item['formula'];
        $rules_block = json_decode( $scheme_item['rule'], true );
        $block_condition = $rules_block['condition'];
        $is_passed_block = false;
        $block_rules_count = count( $rules_block['rules'] );
        
        if ( $block_rules_count > 1 ) {
            $check_for_1 = array();
            $check_for_2 = array();
            foreach ( $rules_block['rules'] as $rule_key => $rule_item ) {
                $check_for_3 = array();
                
                if ( isset( $rule_item['rules'] ) ) {
                    $rule_1_condition = $rule_item['condition'];
                    foreach ( $rule_item['rules'] as $rule_2_key => $rule_2_item ) {
                        $check_for_3[] = uni_cpo_formula_condition_check( $rule_2_item, $variables );
                    }
                    
                    if ( false === in_array( false, $check_for_3, true ) && 'AND' === $rule_1_condition ) {
                        $is_passed_2 = true;
                    } elseif ( false !== in_array( true, $check_for_3, true ) && 'OR' === $rule_1_condition ) {
                        $is_passed_2 = true;
                    } else {
                        $is_passed_2 = false;
                    }
                    
                    array_push( $check_for_2, $is_passed_2 );
                } else {
                    $check_for_1[] = uni_cpo_formula_condition_check( $rule_item, $variables );
                }
            
            }
            $check_for_1 = array_merge( $check_for_1, $check_for_2 );
            
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
function uni_cpo_formula_condition_check( $rule, $variables )
{
    $var_name = $rule['id'];
    $rule_value = $rule['value'];
    $rule_type = $rule['type'];
    $is_passed = false;
    switch ( $rule['operator'] ) {
        case 'less':
            if ( isset( $variables[$var_name] ) ) {
                
                if ( 'date' === $rule_type ) {
                    $rule_date = new DateTime( $rule_value );
                    $chosen_date = new DateTime( $variables[$var_name] );
                    if ( $chosen_date < $rule_date ) {
                        $is_passed = true;
                    }
                } else {
                    if ( floatval( $variables[$var_name] ) < floatval( $rule_value ) ) {
                        $is_passed = true;
                    }
                }
            
            }
            break;
        case 'less_or_equal':
            if ( isset( $variables[$var_name] ) ) {
                
                if ( 'date' === $rule_type ) {
                    $rule_date = new DateTime( $rule_value );
                    $chosen_date = new DateTime( $variables[$var_name] );
                    if ( $chosen_date <= $rule_date ) {
                        $is_passed = true;
                    }
                } else {
                    if ( floatval( $variables[$var_name] ) <= floatval( $rule_value ) ) {
                        $is_passed = true;
                    }
                }
            
            }
            break;
        case 'equal':
            
            if ( isset( $variables[$var_name] ) && !is_array( $variables[$var_name] ) ) {
                
                if ( in_array( $rule_type, array( 'double', 'integer' ) ) ) {
                    $is_passed = floatval( $variables[$var_name] ) === floatval( $rule_value );
                } else {
                    $is_passed = $variables[$var_name] === $rule_value;
                }
            
            } elseif ( isset( $variables[$var_name] ) && is_array( $variables[$var_name] ) ) {
                foreach ( $variables[$var_name] as $value ) {
                    
                    if ( $value === $rule_value ) {
                        $is_passed = true;
                        break;
                    }
                
                }
            }
            
            break;
        case 'not_equal':
            
            if ( isset( $variables[$var_name] ) && !is_array( $variables[$var_name] ) ) {
                
                if ( in_array( $rule_type, array( 'double', 'integer' ) ) ) {
                    $is_passed = floatval( $variables[$var_name] ) !== floatval( $rule_value );
                } else {
                    $is_passed = $variables[$var_name] !== $rule_value;
                }
            
            } elseif ( isset( $variables[$var_name] ) && is_array( $variables[$var_name] ) ) {
                foreach ( $variables[$var_name] as $value ) {
                    
                    if ( $value !== $rule_value ) {
                        $is_passed = true;
                        break;
                    }
                
                }
            }
            
            break;
        case 'greater_or_equal':
            if ( isset( $variables[$var_name] ) ) {
                
                if ( 'date' === $rule_type ) {
                    $rule_date = new DateTime( $rule_value );
                    $chosen_date = new DateTime( $variables[$var_name] );
                    if ( $chosen_date >= $rule_date ) {
                        $is_passed = true;
                    }
                } else {
                    if ( floatval( $variables[$var_name] ) >= floatval( $rule_value ) ) {
                        $is_passed = true;
                    }
                }
            
            }
            break;
        case 'greater':
            if ( isset( $variables[$var_name] ) ) {
                
                if ( 'date' === $rule_type ) {
                    $rule_date = new DateTime( $rule_value );
                    $chosen_date = new DateTime( $variables[$var_name] );
                    if ( $chosen_date > $rule_date ) {
                        $is_passed = true;
                    }
                } else {
                    if ( floatval( $variables[$var_name] ) > floatval( $rule_value ) ) {
                        $is_passed = true;
                    }
                }
            
            }
            break;
        case 'is_empty':
            if ( !isset( $variables[$var_name] ) || empty($variables[$var_name]) ) {
                $is_passed = true;
            }
            break;
        case 'is_not_empty':
            if ( !empty($variables[$var_name]) ) {
                $is_passed = true;
            }
            break;
        case 'between':
            if ( isset( $variables[$var_name] ) ) {
                
                if ( 'date' === $rule_type ) {
                    $rule_startdate = new DateTime( $rule_value[0] );
                    $rule_enddate = new DateTime( $rule_value[1] );
                    $chosen_date = new DateTime( $variables[$var_name] );
                    if ( $rule_startdate <= $chosen_date && $chosen_date <= $rule_enddate ) {
                        $is_passed = true;
                    }
                } else {
                    $is_passed = floatval( $rule_value[0] ) <= floatval( $variables[$var_name] ) && floatval( $variables[$var_name] ) <= floatval( $rule_value[1] );
                }
            
            }
            break;
        case 'not_between':
            if ( isset( $variables[$var_name] ) ) {
                
                if ( 'date' === $rule_type ) {
                    $rule_startdate = new DateTime( $rule_value[0] );
                    $rule_enddate = new DateTime( $rule_value[1] );
                    $chosen_date = new DateTime( $variables[$var_name] );
                    if ( $rule_startdate >= $chosen_date || $chosen_date >= $rule_enddate ) {
                        $is_passed = true;
                    }
                } else {
                    $is_passed = floatval( $rule_value[0] ) >= floatval( $variables[$var_name] ) || floatval( $variables[$var_name] ) >= floatval( $rule_value[1] );
                }
            
            }
            break;
    }
    return $is_passed;
}

//
function uni_cpo_process_formula_with_vars( $main_formula, $variables = array() )
{
    $main_formula = preg_replace( '/\\s+/', '', $main_formula );
    
    if ( !empty($variables) ) {
        foreach ( $variables as $k => $v ) {
            
            if ( is_array( $v ) ) {
                if ( !empty($v) ) {
                    foreach ( $v as $k_child => $v_child ) {
                        $search = "/({$k_child})/";
                        $main_formula = preg_replace( $search, $v_child, $main_formula );
                    }
                }
            } else {
                $search = "/({$k})/";
                $main_formula = preg_replace( $search, $v, $main_formula );
            }
        
        }
        $pattern = "/{([^}]*)}/";
        $main_formula = preg_replace( $pattern, '0', $main_formula );
    } else {
        $pattern = "/{([^}]*)}/";
        $main_formula = preg_replace( $pattern, '0', $main_formula );
    }
    
    return $main_formula;
}

//
function uni_cpo_calculate_formula( $main_formula = '' )
{
    
    if ( !empty($main_formula) && 'disable' !== $main_formula ) {
        // change the all unused variables to zero, so formula calculation will not fail
        $pattern = "/{([^}]*)}/";
        $main_formula = preg_replace( $pattern, '0', $main_formula );
        // calculate
        $m = new EvalMath();
        $m->suppress_errors = true;
        $calc_price = $m->evaluate( $main_formula );
        $calc_price = ( !is_infinite( $calc_price ) && !is_nan( $calc_price ) ? $calc_price : 0 );
        return floatval( $calc_price );
    } else {
        return 0;
    }

}

//
function uni_cpo_option_js_condition_prepare( $scheme )
{
    $condition_operator = $scheme['condition'];
    $operator = ( 'AND' === $condition_operator ? '&&' : '||' );
    $rules = $scheme['rules'];
    $rules_count = count( $rules );
    
    if ( $rules_count > 1 ) {
        foreach ( $rules as $rule ) {
            
            if ( isset( $rule['rules'] ) ) {
                $statements[] = uni_cpo_option_js_condition_prepare( $rule );
            } else {
                $statements[] = uni_cpo_option_js_condition( $rule );
            }
        
        }
        $condition = '(' . implode( " {$operator} ", $statements ) . ')';
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
function uni_cpo_option_js_condition( $rule )
{
    $cpo_var = 'formData';
    switch ( $rule['operator'] ) {
        case 'less':
            $statement = "UniCpo.isProp({$cpo_var}, '{$rule['id']}') && {$cpo_var}.{$rule['id']} < {$rule['value']}";
            break;
        case 'less_or_equal':
            $statement = "UniCpo.isProp({$cpo_var}, '{$rule['id']}') && {$cpo_var}.{$rule['id']} <= {$rule['value']}";
            break;
        case 'equal':
            $statement = "UniCpo.isProp({$cpo_var}, '{$rule['id']}') && ({$cpo_var}.{$rule['id']}.constructor === Array ? {$cpo_var}.{$rule['id']}.indexOf('{$rule['value']}') !== -1 : (window.UniCpo.isNumber('{$rule['value']}') ? parseFloat({$cpo_var}.{$rule['id']}) === parseFloat('{$rule['value']}') : {$cpo_var}.{$rule['id']} === '{$rule['value']}'))";
            break;
        case 'not_equal':
            $statement = "!UniCpo.isProp({$cpo_var}, '{$rule['id']}') || (UniCpo.isProp({$cpo_var}, '{$rule['id']}') && ({$cpo_var}.{$rule['id']}.constructor === Array ? {$cpo_var}.{$rule['id']}.indexOf('{$rule['value']}') === -1 : (window.UniCpo.isNumber('{$rule['value']}') ? parseFloat({$cpo_var}.{$rule['id']}) !== parseFloat('{$rule['value']}') : {$cpo_var}.{$rule['id']} !== '{$rule['value']}')))";
            break;
        case 'greater_or_equal':
            $statement = "UniCpo.isProp({$cpo_var}, '{$rule['id']}') && {$cpo_var}.{$rule['id']} >= {$rule['value']}";
            break;
        case 'greater':
            $statement = "UniCpo.isProp({$cpo_var}, '{$rule['id']}') && {$cpo_var}.{$rule['id']} > {$rule['value']}";
            break;
        case 'is_empty':
            $statement = "!(!UniCpo.isProp({$cpo_var}, '{$rule['id']}') || ({$cpo_var}.{$rule['id']}.constructor === Array ? {$cpo_var}.{$rule['id']}.length === 0 : {$cpo_var}.{$rule['id']} === ''))";
            break;
        case 'is_not_empty':
            $statement = "(UniCpo.isProp({$cpo_var}, '{$rule['id']}') && ({$cpo_var}.{$rule['id']}.constructor === Array ? {$cpo_var}.{$rule['id']}.length > 0 : {$cpo_var}.{$rule['id']} !== ''))";
            break;
        case 'between':
            $statement = "(UniCpo.isProp({$cpo_var}, '{$rule['id']}') && {$cpo_var}.{$rule['id']} >= {$rule['value'][0]} && {$cpo_var}.{$rule['id']} <= {$rule['value'][1]})";
            break;
        case 'not_between':
            $statement = "(UniCpo.isProp({$cpo_var}, '{$rule['id']}') && ({$cpo_var}.{$rule['id']} <= {$rule['value'][0]} || {$cpo_var}.{$rule['id']} >= {$rule['value'][1]}))";
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
function uni_cpo_price( $price, $args = array() )
{
    $defaults = array(
        'ex_tax_label'       => false,
        'currency'           => '',
        'decimal_separator'  => wc_get_price_decimal_separator(),
        'thousand_separator' => wc_get_price_thousand_separator(),
        'decimals'           => wc_get_price_decimals(),
        'price_format'       => get_woocommerce_price_format(),
    );
    $data = apply_filters( 'wc_price_args', wp_parse_args( $args, $defaults ) );
    $negative = $price < 0;
    $price = apply_filters( 'uni_cpo_price_raw', floatval( ( $negative ? $price * -1 : $price ) ) );
    $price = apply_filters(
        'formatted_uni_cpo_price',
        number_format(
        $price,
        $data['decimals'],
        $data['decimal_separator'],
        $data['thousand_separator']
    ),
        $price,
        $data['decimals'],
        $data['decimal_separator'],
        $data['thousand_separator']
    );
    if ( apply_filters( 'uni_cpo_price_trim_zeros', false ) && $data['decimals'] > 0 ) {
        $price = wc_trim_zeros( $price );
    }
    $formatted_price = (( $negative ? '-' : '' )) . sprintf( $data['price_format'], get_woocommerce_currency_symbol( $data['currency'] ), $price );
    if ( $data['ex_tax_label'] && wc_tax_enabled() ) {
        $formatted_price .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
    }
    return apply_filters(
        'uni_cpo_price',
        $formatted_price,
        $price,
        $args
    );
}

// customers try to add a product to the cart from an archive page? let's check if it is possible to do!
add_filter(
    'woocommerce_loop_add_to_cart_link',
    'uni_cpo_add_to_cart_button',
    10,
    2
);
function uni_cpo_add_to_cart_button( $link, $product )
{
    $product_id = intval( $product->get_id() );
    $product_data = Uni_Cpo_Product::get_product_data_by_id( $product_id );
    if ( 'on' === $product_data['settings_data']['cpo_enable'] ) {
        $link = sprintf(
            '<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>',
            esc_url( get_permalink( $product_id ) ),
            esc_attr( ( isset( $quantity ) ? $quantity : 1 ) ),
            esc_attr( $product->get_id() ),
            esc_attr( $product->get_sku() ),
            esc_attr( ( isset( $class ) ? $class : 'button' ) ),
            esc_html( __( 'Select options', 'uni-cpo' ) )
        );
    }
    return $link;
}

//
add_action( 'uni_cpo_after_render_content', 'uni_cpo_calculate_button_html', 10 );
function uni_cpo_calculate_button_html()
{
    global  $post ;
    $product_data = Uni_Cpo_Product::get_product_data_by_id( $post->ID );
    
    if ( 'on' === $product_data['settings_data']['calc_btn_enable'] ) {
        $btn_text = apply_filters( 'uni_cpo_calculate_btn_text', '<i class="fas fa-calculator" aria-hidden="true"></i>' . esc_html__( 'Calculate', 'uni-cpo' ), $post->ID );
        echo  '<button type="button" class="uni-cpo-calculate-btn js-uni-cpo-calculate-btn button alt">' . $btn_text . '</button>' ;
    }

}

//
add_action( 'uni_cpo_after_render_content', 'uni_cpo_reset_form_btn_html', 10 );
function uni_cpo_reset_form_btn_html()
{
    global  $post ;
    $product_data = Uni_Cpo_Product::get_product_data_by_id( $post->ID );
    
    if ( 'on' === $product_data['settings_data']['reset_form_btn'] ) {
        $btn_text = apply_filters( 'uni_cpo_reset_form_btn_text', '' . esc_html__( 'Reset form', 'uni-cpo' ), $post->ID );
        echo  '<button type="button" class="uni-cpo-reset-form-btn js-uni-cpo-reset-form-btn button alt">' . $btn_text . '</button>' ;
    }

}

add_filter(
    'woocommerce_get_price_html',
    'uni_cpo_display_custom_price_on_archives',
    10,
    2
);
function uni_cpo_display_custom_price_on_archives( $price, $product )
{
    $product_id = intval( $product->get_id() );
    $product_data = Uni_Cpo_Product::get_product_data_by_id( $product_id );
    $product_post_id = 0;
    global  $wp_query ;
    if ( isset( $wp_query->queried_object->post_content ) && has_shortcode( $wp_query->queried_object->post_content, 'product_page' ) ) {
        
        if ( has_shortcode( $wp_query->queried_object->post_content, 'product_page' ) ) {
            $pattern = '\\[(\\[?)(product_page)(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*+(?:\\[(?!\\/\\2\\])[^\\[]*+)*+)\\[\\/\\2\\])?)(\\]?)';
            if ( preg_match_all( '/' . $pattern . '/s', $wp_query->queried_object->post_content, $matches ) && array_key_exists( 2, $matches ) && in_array( 'product_page', $matches[2] ) ) {
                foreach ( $matches[2] as $key => $value ) {
                    
                    if ( $value === 'product_page' ) {
                        $parsed = shortcode_parse_atts( $matches[3][$key] );
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
    
    if ( 'on' === $product_data['settings_data']['cpo_enable'] && 'on' === $product_data['settings_data']['calc_enable'] && (is_single() && $product_id !== $wp_query->queried_object_id || is_page() && $product_id !== $product_post_id || is_tax() || is_archive() || is_single() && !is_singular( 'product' ) && isset( $wp_query->queried_object->post_content ) && !has_shortcode( $wp_query->queried_object->post_content, 'product_page' )) ) {
        $raw_regular_price = $product->get_regular_price( 'edit' );
        $raw_sale_price = $product->get_sale_price( 'edit' );
        $display_regular_price = apply_filters( 'uni_cpo_price_regular_archive', wc_get_price_to_display( $product, array(
            'price' => $raw_regular_price,
        ) ), $product );
        $display_sale_price = apply_filters( 'uni_cpo_price_sale_archive', wc_get_price_to_display( $product, array(
            'price' => $raw_sale_price,
        ) ), $product );
        $starting_price = 0;
        $is_using_archive_tmpl = false;
        $starting_price = ( !empty($product_data['settings_data']['min_price']) ? floatval( $product_data['settings_data']['min_price'] ) : $price );
        $starting_price = apply_filters( 'uni_cpo_price_starting_archive', $starting_price, $product );
        $price = uni_cpo_price( $starting_price );
        
        if ( $product->is_taxable() && $starting_price && !($is_using_archive_tmpl && $product->is_on_sale()) ) {
            $tax_suffix = $product->get_price_suffix( $starting_price );
            $price = $price . $tax_suffix;
        }
        
        return $price;
    } else {
        return $price;
    }

}

//
function uni_cpo_get_price_for_meta()
{
    global  $product ;
    $product_id = intval( $product->get_id() );
    $product_data = Uni_Cpo_Product::get_product_data_by_id( $product_id );
    
    if ( 'on' === $product_data['settings_data']['cpo_enable'] && 'on' === $product_data['settings_data']['calc_enable'] ) {
        $starting_price = ( !empty($product_data['settings_data']['min_price']) ? floatval( $product_data['settings_data']['min_price'] ) : 0 );
        
        if ( 0 === $starting_price ) {
            $price = apply_filters( 'uni_cpo_display_price_meta_tag', $starting_price, $product );
            $price = wc_get_price_to_display( $product, array(
                'price' => $price,
            ) );
        } else {
            $price = wc_get_price_to_display( $product );
        }
        
        return $price;
    } else {
        return wc_get_price_to_display( $product );
    }

}

//
add_action( 'woocommerce_single_product_summary', 'uni_cpo_display_price_custom_meta', 11 );
function uni_cpo_display_price_custom_meta()
{
    global  $product ;
    $product_data = Uni_Cpo_Product::get_product_data_by_id( $product->get_id() );
    
    if ( 'on' === $product_data['settings_data']['cpo_enable'] && 'on' === $product_data['settings_data']['calc_enable'] && (!empty($product_data['settings_data']['min_price']) || !empty($product_data['settings_data']['starting_price'])) ) {
        $price = uni_cpo_get_price_for_meta();
        echo  '<meta itemprop="minPrice" content="' . esc_attr( $price ) . '" itemtype="http://schema.org/PriceSpecification" />' ;
    }

}

//
function uni_cpo_get_display_price_reversed( $product, $price )
{
    $tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
    $price_incl = wc_get_price_including_tax( $product, array(
        'qty'   => 1,
        'price' => $price,
    ) );
    $price_excl = wc_get_price_excluding_tax( $product, array(
        'qty'   => 1,
        'price' => $price,
    ) );
    $display_price = ( $tax_display_mode == 'incl' ? $price_excl : $price_incl );
    return $display_price;
}

// displays a new and discounted price in the cart
function uni_cpo_change_cart_item_price( $price, $cart_item )
{
    $product_id = $cart_item['product_id'];
    $product_data = Uni_Cpo_Product::get_product_data_by_id( $product_id );
    
    if ( 'on' === $product_data['settings_data']['cpo_enable'] && 'on' === $product_data['settings_data']['calc_enable'] ) {
        $product = wc_get_product( $product_id );
        $price_calc = wc_get_price_to_display( $product, array(
            'qty'   => 1,
            'price' => $cart_item['_cpo_price'],
        ) );
        $cpo_price = apply_filters( 'uni_cpo_get_cart_price_calculated_raw', $price_calc, $product_data );
        $cpo_price = wc_price( $cpo_price );
        return $cpo_price;
    } else {
        return $price;
    }

}

//
add_action(
    'woocommerce_before_calculate_totals',
    'uni_cpo_before_calculate_totals',
    10,
    1
);
function uni_cpo_before_calculate_totals( $object )
{
    if ( method_exists( $object, 'get_cart' ) ) {
        foreach ( $object->get_cart() as $cart_item_key => $values ) {
            $product = $values['data'];
            if ( $product->is_type( 'simple' ) && !empty($object->coupons) ) {
                foreach ( $object->coupons as $code => $coupon ) {
                    if ( $coupon->is_valid() && ($coupon->is_valid_for_product( $product, $values ) || $coupon->is_valid_for_cart()) ) {
                        if ( isset( $values['_cpo_price'] ) ) {
                            $product->set_price( $values['_cpo_price'] );
                        }
                    }
                }
            }
        }
    }
}

// associate with order's meta
add_filter(
    'woocommerce_add_cart_item_data',
    'uni_cpo_add_cart_item_data',
    10,
    4
);
add_filter(
    'woocommerce_get_cart_item_from_session',
    'uni_cpo_get_cart_item_from_session',
    10,
    3
);
add_filter(
    'woocommerce_add_cart_item',
    'uni_cpo_add_cart_item',
    10,
    1
);
// get item data to display in cart and checkout page
add_filter(
    'woocommerce_get_item_data',
    'uni_cpo_get_item_data',
    10,
    2
);
// add meta data for each order item
add_action(
    'woocommerce_checkout_create_order_line_item',
    'uni_cpo_checkout_create_order_line_item',
    10,
    4
);
// adds custom option data to the cart
function uni_cpo_add_cart_item_data(
    $cart_item_data,
    $product_id,
    $variation_id,
    $quantity
)
{
    try {
        $product_data = Uni_Cpo_Product::get_product_data_by_id( $product_id );
        $qty_field_slug = $product_data['settings_data']['qty_field'];
        
        if ( !empty($cart_item_data) ) {
            // is used when 'order again' has been initiated
            $form_data = $cart_item_data;
            $cart_item_data = array();
        } else {
            $form_data = wc_clean( $_POST );
        }
        
        
        if ( 'on' === $product_data['settings_data']['cpo_enable'] ) {
            $cart_item_data['_cpo_calc_option'] = ( 'on' === $product_data['settings_data']['calc_enable'] ? true : false );
            $cart_item_data['_cpo_cart_item_id'] = ( !empty($form_data['cpo_cart_item_id']) ? $form_data['cpo_cart_item_id'] : '' );
            $cart_item_data['_cpo_product_image'] = ( !empty($form_data['cpo_product_image']) ? $form_data['cpo_product_image'] : '' );
            // values to be unset
            $unset_values = apply_filters(
                'uni_cpo_add_to_cart_values_to_be_unset',
                array(
                'cpo_cart_item_id',
                'cpo_product_id',
                'add-to-cart',
                'cpo_product_image',
                'quantity'
            ),
                $cart_item_data,
                $product_id
            );
            if ( !empty($unset_values) ) {
                foreach ( $unset_values as $v ) {
                    if ( isset( $form_data[$v] ) ) {
                        unset( $form_data[$v] );
                    }
                }
            }
            // it is isset when ordering again has been initiated
            $cart_item_data['_cpo_data'] = ( isset( $form_data['cpo_data'] ) ? $form_data['cpo_data'] : $form_data );
            
            if ( true === boolval( $cart_item_data['_cpo_calc_option'] ) ) {
                
                if ( !empty($cart_item_data['_cpo_data']) ) {
                    $posts = uni_cpo_get_posts_by_slugs( array_keys( $cart_item_data['_cpo_data'] ) );
                    
                    if ( !empty($posts) ) {
                        $posts_ids = wp_list_pluck( $posts, 'ID' );
                        foreach ( $posts_ids as $post_id ) {
                            $option = uni_cpo_get_option( $post_id );
                            if ( is_object( $option ) ) {
                                
                                if ( 'extra_cart_button' === $option->get_type() ) {
                                    $cart_item_data['_cpo_is_free_sample'] = $option->calculate( $cart_item_data['_cpo_data'] );
                                    $post_name = trim( $option->get_slug(), '{}' );
                                    unset( $cart_item_data['_cpo_data'][$post_name] );
                                    continue;
                                }
                            
                            }
                        }
                    }
                
                }
                
                $price = uni_cpo_calculate_price_in_cart( $cart_item_data, $product_id );
            } else {
                $product = wc_get_product( $product_id );
                $price = $product->get_price();
            }
            
            $price = wc_format_decimal( $price );
            $cart_item_data['_cpo_price'] = $price;
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
function uni_cpo_get_cart_item_from_session( $session_data, $values, $key )
{
    $session_data['_cpo_calc_option'] = ( isset( $values['_cpo_calc_option'] ) ? boolval( $values['_cpo_calc_option'] ) : false );
    $session_data['_cpo_cart_item_id'] = ( isset( $values['_cpo_cart_item_id'] ) ? $values['_cpo_cart_item_id'] : '' );
    $session_data['_cpo_product_image'] = ( isset( $values['_cpo_product_image'] ) ? $values['_cpo_product_image'] : '' );
    $session_data['_cpo_data'] = ( isset( $values['_cpo_data'] ) ? $values['_cpo_data'] : '' );
    
    if ( isset( $session_data['_cpo_data'] ) ) {
        return uni_cpo_add_cart_item( $session_data );
    } else {
        return $session_data;
    }

}

function uni_cpo_add_cart_item( $cart_item_data )
{
    $product_id = $cart_item_data['product_id'];
    $is_calc_enabled = ( isset( $cart_item_data['_cpo_calc_option'] ) ? boolval( $cart_item_data['_cpo_calc_option'] ) : false );
    // price calc
    
    if ( true === $is_calc_enabled && isset( $cart_item_data['_cpo_data'] ) ) {
        $price = uni_cpo_calculate_price_in_cart( $cart_item_data, $product_id );
        $price = wc_format_decimal( $price );
        $cart_item_data['_cpo_price'] = $price;
        $cart_item_data['data']->set_price( $cart_item_data['_cpo_price'] );
    }
    
    return $cart_item_data;
}

//
function uni_cpo_get_item_data( $item_data, $cart_item )
{
    
    if ( !empty($cart_item['_cpo_data']) ) {
        // saves an information about chosen options and their values in cart meta
        $form_data = $cart_item['_cpo_data'];
        $filtered_form_data = array_filter( $form_data, function ( $k ) use( $form_data ) {
            return false !== strpos( $k, UniCpo()->get_var_slug() ) && !empty($form_data[$k]);
        }, ARRAY_FILTER_USE_KEY );
        
        if ( !empty($filtered_form_data) ) {
            $posts = uni_cpo_get_posts_by_slugs( array_keys( $filtered_form_data ) );
            
            if ( !empty($posts) ) {
                $posts_ids = wp_list_pluck( $posts, 'ID' );
                foreach ( $posts_ids as $post_id ) {
                    $option = uni_cpo_get_option( $post_id );
                    
                    if ( is_object( $option ) ) {
                        $post_name = trim( $option->get_slug(), '{}' );
                        $display_key = uni_cpo_sanitize_label( $option->cpo_order_label() );
                        $calculate_result = $option->calculate( $filtered_form_data );
                        if ( is_array( $calculate_result ) ) {
                            foreach ( $calculate_result as $k => $v ) {
                                
                                if ( $post_name === $k ) {
                                    // excluding special vars
                                    
                                    if ( is_array( $v['cart_meta'] ) ) {
                                        $value = implode( ', ', $v['cart_meta'] );
                                    } else {
                                        $value = $v['cart_meta'];
                                    }
                                    
                                    
                                    if ( is_array( $v['order_meta'] ) ) {
                                        $v['order_meta'] = array_map( function ( $item ) {
                                            
                                            if ( !is_numeric( $item ) ) {
                                                return esc_html__( $item );
                                            } else {
                                                return $item;
                                            }
                                        
                                        }, $v['order_meta'] );
                                        $display_value = implode( ', ', $v['order_meta'] );
                                    } else {
                                        
                                        if ( !is_numeric( $v['order_meta'] ) ) {
                                            $display_value = esc_html__( $v['order_meta'] );
                                        } else {
                                            $display_value = $v['order_meta'];
                                        }
                                    
                                    }
                                    
                                    $item_data[] = array(
                                        'name'    => $option->get_slug(),
                                        'key'     => esc_html__( $display_key ),
                                        'value'   => $value,
                                        'display' => $display_value,
                                    );
                                    break;
                                }
                            
                            }
                        }
                    }
                
                }
            }
        
        }
    
    }
    
    return $item_data;
}

// adds meta info for order items
function uni_cpo_checkout_create_order_line_item(
    $item,
    $cart_item_key,
    $values,
    $order
)
{
    
    if ( isset( $values['_cpo_data'] ) ) {
        $form_data = $values['_cpo_data'];
        foreach ( $form_data as $name => $value ) {
            $item->add_meta_data( '_' . $name, $value );
        }
    }
    
    $additional_data = apply_filters(
        'uni_cpo_additional_item_data',
        array(),
        $item,
        $cart_item_key,
        $values
    );
    if ( !empty($values['_cpo_product_image']) ) {
        $additional_data = $additional_data + array(
            '_uni_custom_item_image' => $values['_cpo_product_image'],
        );
    }
    if ( !empty($additional_data) && is_array( $additional_data ) ) {
        foreach ( $additional_data as $k => $v ) {
            $item->add_meta_data( $k, $v );
        }
    }
}

//
function uni_cpo_calculate_price_in_cart( &$cart_item_data, $product_id )
{
    try {
        $product = wc_get_product( $product_id );
        $product_data = Uni_Cpo_Product::get_product_data_by_id( $product_id );
        $form_data = $cart_item_data['_cpo_data'];
        $options_eval_result = array();
        $variables = array();
        $is_calc_disabled = false;
        $formatted_vars = array();
        $is_free_sample = ( isset( $cart_item_data['_cpo_is_free_sample'] ) ? $cart_item_data['_cpo_is_free_sample'] : false );
        $main_formula = $product_data['formula_data']['main_formula'];
        $filtered_form_data = array_filter( $form_data, function ( $k ) use( $form_data ) {
            return false !== strpos( $k, UniCpo()->get_var_slug() ) && !empty($form_data[$k]);
        }, ARRAY_FILTER_USE_KEY );
        
        if ( !empty($filtered_form_data) ) {
            $posts = uni_cpo_get_posts_by_slugs( array_keys( $filtered_form_data ) );
            
            if ( !empty($posts) ) {
                $posts_ids = wp_list_pluck( $posts, 'ID' );
                foreach ( $posts_ids as $post_id ) {
                    $option = uni_cpo_get_option( $post_id );
                    
                    if ( is_object( $option ) ) {
                        $calculate_result = $option->calculate( $filtered_form_data );
                        if ( !empty($calculate_result) ) {
                            $options_eval_result[$option->get_slug()] = $calculate_result;
                        }
                    }
                
                }
            }
        
        }
        
        array_walk( $options_eval_result, function ( $v ) use( &$variables, &$formatted_vars ) {
            foreach ( $v as $slug => $value ) {
                // prepare $variables for calculation purpose
                $variables['{' . $slug . '}'] = $value['calc'];
                // prepare $formatted_vars for conditional logic purpose
                $formatted_vars[$slug] = $value['cart_meta'];
            }
        } );
        $variables['{uni_cpo_price}'] = $product->get_price( 'edit' );
        // non option variables
        if ( 'on' === $product_data['nov_data']['nov_enable'] && !empty($product_data['nov_data']['nov']) ) {
            $variables = uni_cpo_process_formula_with_non_option_vars( $variables, $product_data, $formatted_vars );
        }
        // formula conditional logic
        
        if ( 'on' === $product_data['formula_data']['rules_enable'] && !empty($product_data['formula_data']['formula_scheme']) && is_array( $product_data['formula_data']['formula_scheme'] ) ) {
            $conditional_formula = uni_cpo_process_formula_scheme( $formatted_vars, $product_data );
            if ( $conditional_formula ) {
                $main_formula = $conditional_formula;
            }
        }
        
        if ( 'disable' === $main_formula || 0 === $is_free_sample ) {
            $is_calc_disabled = true;
        }
        //
        
        if ( !$is_calc_disabled ) {
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
            if ( !empty($price_max) && $price_calculated >= $price_max ) {
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

//
add_filter(
    'woocommerce_order_again_cart_item_data',
    'uni_cpo_woocommerce_order_again_cart_item_data',
    10,
    2
);
function uni_cpo_woocommerce_order_again_cart_item_data( $cart_item_meta, $item )
{
    return uni_cpo_re_add_cpo_item_data( $cart_item_meta, $item->get_meta_data() );
}

//
function uni_cpo_re_add_cpo_item_data( $item_data, $raw_data )
{
    $item_data['cpo_cart_item_id'] = current_time( 'timestamp' );
    $item_data['cpo_product_image'] = ( isset( $raw_data['_cpo_product_image'] ) ? $raw_data['_cpo_product_image'] : '' );
    unset( $item_data['cpo_price'] );
    if ( is_array( $raw_data ) ) {
        foreach ( $raw_data as $k => $v ) {
            
            if ( is_array( $v ) ) {
                
                if ( false !== strpos( $k, '_cpo' ) ) {
                    $meta_key_new = ltrim( $k, '_' );
                    $item_data[$meta_key_new] = $v;
                }
            
            } elseif ( is_a( $v, 'WC_Meta_Data' ) ) {
                $meta_data = $v->get_data();
                
                if ( false !== strpos( $meta_data['key'], '_cpo' ) ) {
                    $meta_key_new = ltrim( $meta_data['key'], '_' );
                    $item_data[$meta_key_new] = $meta_data['value'];
                }
                
                if ( '_uni_custom_item_image' === $meta_data['key'] ) {
                    $item_data['cpo_product_image'] = $meta_data['value'];
                }
            }
        
        }
    }
    return $item_data;
}

//
function uni_cpo_get_options_data_for_frontend( $product_id )
{
    
    if ( is_singular( 'product' ) ) {
        $product_data = Uni_Cpo_Product::get_product_data_by_id( $product_id );
        $content = $product_data['content'];
        $options_data = array();
        if ( is_array( $content ) && !empty($content) ) {
            array_walk( $content, function ( $row, $row_key ) use( &$options_data ) {
                if ( is_array( $row['columns'] ) && !empty($row['columns']) ) {
                    array_walk( $row['columns'], function ( $column, $column_key ) use( &$options_data, $row_key ) {
                        if ( is_array( $column['modules'] ) && !empty($column['modules']) ) {
                            array_walk( $column['modules'], function ( $module ) use( &$options_data, $row_key, $column_key ) {
                                
                                if ( !empty($module['settings']['cpo_general']['main']['cpo_slug']) ) {
                                    $slug = UniCpo()->get_var_slug() . $module['settings']['cpo_general']['main']['cpo_slug'];
                                    $label = ( isset( $module['settings']['cpo_general']['advanced']['cpo_label'] ) ? __( $module['settings']['cpo_general']['advanced']['cpo_label'] ) : '' );
                                    $suboptions = ( isset( $module['settings']['cpo_suboptions']['data']['cpo_radio_options'] ) ? $module['settings']['cpo_suboptions']['data']['cpo_radio_options'] : (( isset( $module['settings']['cpo_suboptions']['data']['cpo_select_options'] ) ? $module['settings']['cpo_suboptions']['data']['cpo_select_options'] : array() )) );
                                    $suboptions_formatted = array();
                                    $colorify_data = array();
                                    $is_imagify = ( !empty($module['settings']['cpo_general']['main']['cpo_is_imagify']) ? true : false );
                                    if ( !empty($suboptions) ) {
                                        foreach ( $suboptions as $suboption ) {
                                            
                                            if ( !empty($suboption['label']) && !empty($suboption['slug']) ) {
                                                $suboptions_formatted[$suboption['slug']]['label'] = __( $suboption['label'] );
                                                
                                                if ( !empty($suboption['attach_id']) || !empty($suboption['attach_id_r']) ) {
                                                    $replacement_attach_id = ( !empty($suboption['attach_id_r']) ? $suboption['attach_id_r'] : $suboption['attach_id'] );
                                                    $image_thumb = wp_get_attachment_image_src( $replacement_attach_id, 'woocommerce_single' );
                                                    $suboptions_formatted[$suboption['slug']]['imagify']['src'] = $image_thumb[0];
                                                    if ( !empty($suboption['def']) ) {
                                                        $suboptions_formatted[$suboption['slug']]['imagify']['def'] = $image_thumb[0];
                                                    }
                                                }
                                            
                                            }
                                        
                                        }
                                    }
                                    if ( !empty($module['settings']['cpo_general']['main']['cpo_encoded_image']) && !empty($module['settings']['cpo_general']['main']['cpo_slug']) ) {
                                        $colorify_data = array(
                                            'img_encoded' => $module['settings']['cpo_general']['main']['cpo_encoded_image'],
                                        );
                                    }
                                    $options_data[$slug] = array(
                                        'label'      => $label,
                                        'suboptions' => $suboptions_formatted,
                                        'colorify'   => $colorify_data,
                                        'is_imagify' => $is_imagify,
                                    );
                                }
                            
                            } );
                        }
                    } );
                }
            } );
        }
        return $options_data;
    }

}

//////////////////////////////////////////////////////////////////////////////////////
// WC order edit page
//////////////////////////////////////////////////////////////////////////////////////
// adds Add/Edit CPO options btn for order items
add_action(
    'woocommerce_order_item_add_action_buttons',
    'uni_cpo_woocommerce_order_item_add_action_buttons',
    10,
    1
);
function uni_cpo_woocommerce_order_item_add_action_buttons( $order )
{
    if ( $order->is_editable() ) {
        foreach ( $order->get_items() as $item_id => $item_product ) {
            $product_id = $item_product->get_product_id();
            $product_data = Uni_Cpo_Product::get_product_data_by_id( $product_id );
            $nonce = wp_create_nonce( 'order-item' );
            if ( 'on' === $product_data['settings_data']['cpo_enable'] ) {
                echo  '<button type="button" class="button cpo-edit-options-btn cpo-for-item-' . esc_attr( $item_id ) . '" data-security="' . esc_attr( $nonce ) . '" data-pid="' . esc_attr( $product_id ) . '" data-order_item_id="' . esc_attr( $item_id ) . '" style="display:none;">' . esc_html__( 'Add/Edit CPO option(s)', 'uni-cpo' ) . '</button>' ;
            }
        }
    }
}
