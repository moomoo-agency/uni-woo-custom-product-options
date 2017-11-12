<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Uni_Cpo_Post_Types Class.
 */
class Uni_Cpo_Post_Types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_module_post_type' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_option_post_type' ), 5 );
	}

	/**
	 * Register core taxonomies.
	 */
	public static function register_taxonomies() {

		if ( ! is_blog_installed() ) {
			return;
		}

		if ( taxonomy_exists( 'option_type' ) ) {
			return;
		}

		register_taxonomy( 'option_type',
			apply_filters( 'uni_cpo_taxonomy_objects_option_type', array( 'uni_cpo_option' ) ),
			apply_filters( 'uni_cpo_taxonomy_args_option_type', array(
				'hierarchical'      => false,
				'show_ui'           => false,
				'show_in_nav_menus' => false,
				'query_var'         => is_admin(),
				'rewrite'           => false,
				'public'            => false,
			) )
		);

	}

	/**
	 * Register Module post type.
	 */
	public static function register_module_post_type() {
		if ( ! is_blog_installed() || post_type_exists( 'uni_module' ) ) {
			return;
		}

		$labels = array(
			'name'               => __( 'Builderius Module', 'uni-cpo' ),
			'singular_name'      => __( 'Builderius Module', 'uni-cpo' ),
			'add_new'            => __( 'New Builderius Module', 'uni-cpo' ),
			'add_new_item'       => __( 'Add Builderius Module', 'uni-cpo' ),
			'edit_item'          => __( 'Edit Builderius Module', 'uni-cpo' ),
			'new_item'           => __( 'All Builderius Modules', 'uni-cpo' ),
			'view_item'          => __( 'View Builderius Module', 'uni-cpo' ),
			'search_items'       => __( 'Search Builderius Modules', 'uni-cpo' ),
			'not_found'          => __( 'Builderius Modules not found', 'uni-cpo' ),
			'not_found_in_trash' => __( 'Builderius Modules not found in cart', 'uni-cpo' ),
			'parent_item_colon'  => __( 'Parent Builderius Module', 'uni-cpo' ),
			'menu_name'          => __( 'Builderius Modules', 'uni-cpo' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'show_in_rest'       => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			'menu_position'      => 4.8,
			'menu_icon'          => 'dashicons-welcome-widgets-menus',
			'capability_type'    => 'page',
			'hierarchical'       => true,
			'has_archive'        => true,
			'rewrite'            => array( 'slug' => 'uni-module', 'with_front' => false ),
			'supports'           => array( 'title', 'custom-fields' ),
			'taxonomies'         => array(),
		);
		register_post_type( 'uni_module', $args );

	}

	/**
	 * Register Option post type.
	 */
	public static function register_option_post_type() {
		if ( post_type_exists( 'uni_cpo_option' ) ) {
			return;
		}

		$labels = array(
			'name'               => __( 'CPO option', 'uni-cpo' ),
			'singular_name'      => __( 'CPO option', 'uni-cpo' ),
			'add_new'            => __( 'New CPO option', 'uni-cpo' ),
			'add_new_item'       => __( 'Add CPO option', 'uni-cpo' ),
			'edit_item'          => __( 'Edit CPO option', 'uni-cpo' ),
			'new_item'           => __( 'All CPO options', 'uni-cpo' ),
			'view_item'          => __( 'View CPO option', 'uni-cpo' ),
			'search_items'       => __( 'Search CPO options', 'uni-cpo' ),
			'not_found'          => __( 'CPO options not found', 'uni-cpo' ),
			'not_found_in_trash' => __( 'CPO options not found in cart', 'uni-cpo' ),
			'parent_item_colon'  => __( 'Parent CPO option', 'uni-cpo' ),
			'menu_name'          => __( 'CPO options', 'uni-cpo' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'show_in_rest'       => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'menu_position'      => 4.8,
			'menu_icon'          => 'dashicons-welcome-widgets-menus',
			'capability_type'    => 'page',
			'hierarchical'       => true,
			'has_archive'        => true,
			'rewrite'            => array( 'slug' => 'uni-cpo-option', 'with_front' => false ),
			'supports'           => array( 'title', 'custom-fields' ),
			'taxonomies'         => array(),
		);
		register_post_type( 'uni_cpo_option', $args );

	}

}

Uni_Cpo_Post_Types::init();
