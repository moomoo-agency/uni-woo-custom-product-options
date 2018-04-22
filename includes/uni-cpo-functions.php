<?php

//
function uni_cpo_get_decimals_count( $value )
{
    
    if ( (int) $value == $value ) {
        return 0;
    } elseif ( !is_numeric( $value ) ) {
        return false;
    }
    
    return strlen( $value ) - strrpos( $value, '.' ) - 1;
}

//
function uni_cpo_get_all_roles()
{
    global  $wp_roles ;
    $all_roles = $wp_roles->roles;
    $role_names = array();
    foreach ( $all_roles as $role_name => $role_data ) {
        $role_names[$role_name] = $role_data['name'];
    }
    return $role_names;
}

function uni_cpo_field_attributes_modifier( $new_attrs, $attributes )
{
    array_walk( $new_attrs, function ( $v ) use( &$attributes ) {
        $rule = explode( '=', $v );
        
        if ( isset( $rule[0] ) && !empty($rule[1]) ) {
            $attr_name = $rule[0];
            $attr_val = trim( $rule[1], '"' );
            $attributes[$attr_name] = $attr_val;
        }
    
    } );
    return $attributes;
}

function uni_cpo_add_slashes( $attributes = array() )
{
    if ( !empty($attributes) && is_array( $attributes ) ) {
        foreach ( $attributes as $k => $v ) {
            $attributes[$k] = ( preg_match( "/(?=.*parsley)(?!.*message).*/", $k ) ? addslashes( $v ) : $v );
        }
    }
    return $attributes;
}

//
function uni_cpo_get_image_sizes( $size = '' )
{
    global  $_wp_additional_image_sizes ;
    $sizes = array();
    $get_intermediate_image_sizes = get_intermediate_image_sizes();
    // Create the full array with sizes and crop info
    foreach ( $get_intermediate_image_sizes as $_size ) {
        
        if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
            $sizes[$_size]['width'] = get_option( $_size . '_size_w' );
            $sizes[$_size]['height'] = get_option( $_size . '_size_h' );
            $sizes[$_size]['crop'] = (bool) get_option( $_size . '_crop' );
        } elseif ( isset( $_wp_additional_image_sizes[$_size] ) ) {
            $sizes[$_size] = array(
                'width'  => $_wp_additional_image_sizes[$_size]['width'],
                'height' => $_wp_additional_image_sizes[$_size]['height'],
                'crop'   => $_wp_additional_image_sizes[$_size]['crop'],
            );
        }
    
    }
    // Get only 1 size if found
    if ( $size ) {
        
        if ( isset( $sizes[$size] ) ) {
            return $sizes[$size];
        } else {
            return false;
        }
    
    }
    return $sizes;
}

//
function uni_cpo_get_image_sizes_list()
{
    $sizes = uni_cpo_get_image_sizes();
    $list = array();
    foreach ( $sizes as $k => $v ) {
        $list[$k] = $k;
    }
    return $list;
}

//
function uni_cpo_pro_content()
{
    return ( !unicpo_fs()->is__premium_only() ? 'uni-premium-content' : '' );
}
