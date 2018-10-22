<?php

/**
 * Plugin Name: Uni Woo Custom Product Options
 * Plugin URI: https://builderius.io/cpo
 * Description: Provides an opportunity to add extra product options with the possibility to calculate the price based on the chosen options and using custom maths formula!
 * Version: 4.4.1
 * Author: MooMoo Agency
 * Author URI: http://moomoo.agency
 * Domain Path: /languages/
 * Text Domain: uni-cpo
 * Requires PHP: 7.0
 * WC requires at least: 3.2.0
 * WC tested up to: 3.4.7
 * License: GPL v3
 *
 * @fs_premium_only /includes/options/class-uni-cpo-option-checkbox.php, /includes/options/class-uni-cpo-option-datepicker.php, /includes/options/class-uni-cpo-option-file-upload.php, /includes/options/class-uni-cpo-option-dynamic-notice.php, /includes/options/class-uni-cpo-option-range-slider.php, /includes/options/class-uni-cpo-option-matrix.php, /includes/options/class-uni-cpo-option-extra-cart-button.php, /includes/options/class-uni-cpo-option-google-map.php
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

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}

if ( !function_exists( 'uni_cpo_is_plugin_active' ) ) {
    /**
     * Helper function to determine whether a plugin is active.
     *
     * @since 4.2.4
     *
     * @param string $plugin_name plugin name, as the plugin-filename.php
     *
     * @return boolean true if the named plugin is installed and active
     */
    function uni_cpo_is_plugin_active( $plugin_name )
    {
        $active_plugins = (array) get_option( 'active_plugins', array() );
        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) );
        }
        $plugin_filenames = array();
        foreach ( $active_plugins as $plugin ) {
            
            if ( false !== strpos( $plugin, '/' ) ) {
                // normal plugin name (plugin-dir/plugin-filename.php)
                list( , $filename ) = explode( '/', $plugin );
            } else {
                // no directory, just plugin file
                $filename = $plugin;
            }
            
            $plugin_filenames[] = $filename;
        }
        return in_array( $plugin_name, $plugin_filenames );
    }

}
if ( !function_exists( 'uni_cpo_is_wc' ) ) {
    /**
     * Helper function to determine whether WC 3.2+ is active
     *
     * @since 4.2.4
     *
     * @param void
     *
     * @return boolean
     */
    function uni_cpo_is_wc()
    {
        return uni_cpo_is_plugin_active( 'woocommerce.php' );
    }

}

if ( !function_exists( 'unicpo_fs' ) ) {
    
    if ( !version_compare( PHP_VERSION, '7.0', '>=' ) ) {
        add_action( 'admin_notices', 'uni_cpo_fail_php_version' );
    } elseif ( !version_compare( get_bloginfo( 'version' ), '4.8', '>=' ) ) {
        add_action( 'admin_notices', 'uni_cpo_fail_wp_version' );
    } elseif ( !uni_cpo_is_wc() ) {
        add_action( 'admin_notices', 'uni_cpo_fail_wc_version' );
    } else {
        // Include the main class.
        if ( !class_exists( 'UniCpo' ) ) {
            include_once dirname( __FILE__ ) . '/class-uni-cpo.php';
        }
        // Create a helper function for easy SDK access.
        function unicpo_fs()
        {
            global  $unicpo_fs ;
            
            if ( !isset( $unicpo_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/includes/freemius/start.php';
                $unicpo_fs = fs_dynamic_init( array(
                    'id'              => '1534',
                    'slug'            => 'uni-woo-custom-product-options',
                    'type'            => 'plugin',
                    'public_key'      => 'pk_69013caadae19db148a7e4e250aab',
                    'is_premium'      => false,
                    'has_addons'      => true,
                    'has_paid_plans'  => true,
                    'has_affiliation' => 'all',
                    'menu'            => array(
                    'slug'   => 'uni-cpo-settings',
                    'parent' => array(
                    'slug' => 'woocommerce',
                ),
                ),
                    'is_live'         => true,
                ) );
            }
            
            return $unicpo_fs;
        }
        
        // Init Freemius.
        unicpo_fs();
        // Signal that SDK was initiated.
        do_action( 'unicpo_fs_loaded' );
        /**
         * Main instance of Uni_Cpo.
         *
         * Returns the main instance of Uni_Cpo to prevent the need to use globals.
         *
         * @since  4.0.0
         * @return Uni_Cpo
         */
        function UniCpo()
        {
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
    function uni_cpo_fail_php_version()
    {
        $message = sprintf( esc_html__( 'Uni CPO requires PHP version %s+. It is activated but NOT FUNCTIONAL!', 'uni-cpo' ), '7.0' );
        echo  uni_cpo_fail_notice_wrapper( $message ) ;
        return;
    }
    
    /**
     * WP version check notice
     *
     * @since 4.0.0
     * @return void
     */
    function uni_cpo_fail_wp_version()
    {
        $message = sprintf( esc_html__( 'Uni CPO requires WordPress version %s+. It is activated but NOT FUNCTIONAL!', 'uni-cpo' ), '4.8' );
        echo  uni_cpo_fail_notice_wrapper( $message ) ;
        return;
    }
    
    /**
     * WC version check notice
     *
     * @since 4.2.4
     * @return void
     */
    function uni_cpo_fail_wc_version()
    {
        $message = sprintf( esc_html__( 'Uni CPO requires WooCommerce version %s+. It is activated but NOT FUNCTIONAL', 'uni-cpo' ), '3.2' );
        echo  uni_cpo_fail_notice_wrapper( $message ) ;
        return;
    }
    
    /**
     * Fail notices wrapper. Returns sanitized string.
     *
     * @since 4.0.0
     *
     * @param string
     *
     * @return string
     */
    function uni_cpo_fail_notice_wrapper( $message )
    {
        $html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
        return wp_kses_post( $html_message );
    }

}
