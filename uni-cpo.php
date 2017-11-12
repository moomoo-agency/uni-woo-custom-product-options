<?php
/**
 * Plugin Name: Uni CPO - WooCommerce Options and Price Calculation Formulas
 * Plugin URI: http://cpo.builderius.io
 * Description: Creates an opportunity to add custom options for products with the possibility to calculate product price based on the chosen options and using custom maths formula!
 * Version: 4.0.0
 * Author: MooMoo Agency
 * Author URI: http://moomoo.agency
 * Domain Path: /languages/
 * Text Domain: uni-cpo
 * Requires PHP: 7.0
 * WC requires at least: 3.0
 * WC tested up to: 3.2
 * License: GPL v3
*/

/**
 * Uni CPO Plugin
 * Copyright (C) 2017, MooMoo Agency - sales@moomoo.agency
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! version_compare( PHP_VERSION, '7.0', '>=' ) ) {
	add_action( 'admin_notices', 'uni_cpo_fail_php_version' );
} elseif ( ! version_compare( get_bloginfo( 'version' ), '4.8', '>=' ) ) {
	add_action( 'admin_notices', 'uni_cpo_fail_wp_version' );
} else {

	// Include the main class.
	if ( ! class_exists( 'UniCpo' ) ) {
		include_once dirname( __FILE__ ) . '/class-uni-cpo.php';
	}

	/**
	 * Main instance of Uni_Cpo.
	 *
	 * Returns the main instance of Uni_Cpo to prevent the need to use globals.
	 *
	 * @since  4.0.0
	 * @return Uni_Cpo
	 */
	function UniCpo() {
		return Uni_Cpo::instance();
	}

	// Global for backwards compatibility.
	$GLOBALS['unicpo'] = UniCpo();
}

/**
 * PHP version check notice
 *
 * @since 4.0.0
 * @return void
 */
function uni_cpo_fail_php_version() {
	$message = sprintf( esc_html__( 'UniCpo requires PHP version %s+. It is NOT ACTIVATED!', 'uni-cpo' ), '7.0' );
	echo uni_cpo_fail_notice_wrapper( $message );
}
/**
 * WP version check notice
 *
 * @since 4.0.0
 * @return void
 */
function uni_cpo_fail_wp_version() {
	$message = sprintf( esc_html__( 'UniCpo requires WordPress version %s+. It is NOT ACTIVATED!', 'uni-cpo' ), '4.8' );
	echo uni_cpo_fail_notice_wrapper( $message );
}
/**
 * Fail notices wrapper. Returns sanitized string.
 *
 * @since 4.0.0
 * @param string
 * @return string
 */
function uni_cpo_fail_notice_wrapper( $message ) {
	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	return wp_kses_post( $html_message );
}
