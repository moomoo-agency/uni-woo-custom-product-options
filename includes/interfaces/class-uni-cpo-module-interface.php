<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Uni Cpo Module Interface
 *
 * @category Interface
 */
interface Uni_Cpo_Module_Interface {
	public static function get_type();

	public static function template( $data );

	public static function get_css( $data );

	public static function get_settings();

	public static function js_template();
}
