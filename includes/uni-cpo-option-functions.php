<?php
/**
 * Uni Cpo Option Functions
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Main function for returning a model by its object type.
 *
 */
function uni_cpo_get_model( $model_obj_type, $model_id = 0, $model_type = false ) {
	if ( 'option' === $model_obj_type ) {
		return UniCpo()->option_factory->get_option( $model_id, $model_type );
	} elseif ( 'module' === $model_obj_type ) {
		return UniCpo()->module_factory->get_module( $model_id, $model_type );
	}
}

/**
 * Main function for returning options, uses the Uni_Cpo_Option_Factory class.
 *
 */
function uni_cpo_get_option( $option_id = 0, $option_type = false ) {
	return UniCpo()->option_factory->get_option( $option_id, $option_type );
}

/**
 * Get all registered option types.
 *
 */
function uni_cpo_get_option_types() {

	$option_types = array(
		'text_input',
		'text_area',
		'select',
		'radio',
		'checkbox',
		'datepicker',
		'file_upload',
		'range_slider',
		'dynamic_notice',
		'matrix',
		'extra_cart_button',
		'google_map'
	);

	// make it possible for third-party plugins to add new option types
	$option_types = apply_filters( 'uni_cpo_option_types', $option_types );

	$option_types = array_filter( $option_types, function ( $type ) {
		return ! in_array( $type, uni_cpo_get_reserved_option_types() );
	} );

	return $option_types;
}

/**
 * uni_cpo_get_reserved_option_types()
 *
 */
function uni_cpo_get_reserved_option_types() {
	return array( 'special_var' );
}

/**
 * uni_cpo_get_reserved_option_slugs()
 *
 */
function uni_cpo_get_reserved_option_slugs() {
	return array(
		'uni_cpo_quantity',
		'uni_cpo_list_of_attachments',
		'uni_cpo_raw_price',
		'uni_cpo_raw_price_tax_rev',
		'uni_cpo_price_tax_rev',
		'uni_cpo_price',
		'uni_cpo_price_suffix',
		'uni_cpo_price_discounted',
		'uni_cpo_raw_total',
		'uni_cpo_raw_total_tax_rev',
		'uni_cpo_total_tax_rev',
		'uni_cpo_total',
		'uni_cpo_total_suffix'
	);
}

/**
 * Get all registered module types.
 *
 */
function uni_cpo_get_module_types() {

	$module_types = array(
		'row',
		'column',
		'text',
		'button',
		'image'
	);

	// make it possible for third-party plugins to add new module types
	$module_types = apply_filters( 'uni_cpo_module_types', $module_types );

	return $module_types;
}

/**
 * Get all registered setting types.
 *
 */
function uni_cpo_get_setting_types() {

	$setting_types = array(
		'width_type',
		'width',
		'width_px',
		'content_width',
		'height_type',
		'height',
		'height_px',
		'vertical_align',
		'color',
		'color_active',
		'color_hover',
		'color_from',
		'color_to',
		'color_top',
		'color_bottom',
		'text_align',
		'text_align_label',
		'font_family',
		'font_style',
		'font_weight',
		'font_size',
		'font_size_label',
        'font_size_desc',
		'font_size_px',
		'letter_spacing',
		'line_height',
		'background_type',
		'background_color',
		'background_hover_color',
		'background_image',
		'border_top',
		'border_bottom',
		'border_left',
		'border_right',
		'border_unit',
		'margin',
		'padding',
		'offset_px',
		'gap_px',
		'id_name',
		'class_name',
		'float',
		'content',
		'align',
		'href',
		'target',
		'rel',
		'radius',
		'image',
		'divider_style',
		'sync',
		'cpo_slug',
		'cpo_is_required',
		'cpo_type',
		'cpo_min_val',
		'cpo_max_val',
		'cpo_step_val',
		'cpo_def_val',
		'cpo_min_chars',
		'cpo_max_chars',
		'cpo_rate',
		'cpo_label',
		'cpo_label_tag',
		'cpo_order_label',
		'cpo_is_tooltip',
		'cpo_tooltip',
		'cpo_tooltip_class',
		'cpo_tooltip_type',
		'cpo_tooltip_image',
		'cpo_enable_cartedit',
		'cpo_is_fc',
		'cpo_fc_default',
		'cpo_fc_scheme',
		'cpo_select_options',
		'cpo_radio_options',
		'cpo_date_rules',
		'cpo_mode_radio',
		'cpo_geom_radio',
		'cpo_upload_mode',
		'cpo_max_filesize',
		'cpo_mime_types',
		'cpo_mode_checkbox',
		'cpo_geom_checkbox',
		'cpo_is_changeimage',
		'cpo_is_resetbutton',
        'cpo_resetbutton_text',
		'cpo_validation_msg',
		'cpo_vc_extra',
		'cpo_is_vc',
		'cpo_vc_scheme',
		'cpo_is_datepicker_disabled',
		'cpo_date_type',
		'cpo_day_night',
		'cpo_date_min',
		'cpo_date_max',
		'cpo_date_conjunction',
		'cpo_disabled_dates',
		'cpo_is_timepicker',
		'cpo_timepicker_type',
		'cpo_time_min',
		'cpo_time_max',
		'cpo_minute_step',
		'cpo_range_type',
		'cpo_range_grid',
		'cpo_range_input',
		'cpo_range_from',
		'cpo_range_to',
		'cpo_range_prefix',
		'cpo_range_postfix',
		'cpo_custom_values',
		'cpo_notice_text',
        'cpo_matrix_data',
        'cpo_encoded_image',
		'cpo_sc_scheme',
		'cpo_is_sc',
		'cpo_sc_default',
		'cpo_order_visibility',
		'cpo_addtocart_mode',
		'cpo_samples_mode',
		'cpo_map_center',
		'cpo_map_zoom',
		'cpo_is_imagify'
	);

	// make it possible for third-party plugins to add new module types
	$setting_types = apply_filters( 'uni_cpo_setting_types', $setting_types );

	return $setting_types;
}
