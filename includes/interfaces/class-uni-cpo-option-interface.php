<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Uni Cpo Option Interface
 *
 * @category Interface
 */
interface Uni_Cpo_Option_Interface {
	public static function get_type();

	public static function get_settings();

	public function formatted_model_data();

	public static function js_template();

	public function get_edit_field( $data, $value );

	public static function template( $data, $post_data );

	public static function get_css( $data );

	public function calculate( $form_data );
}
