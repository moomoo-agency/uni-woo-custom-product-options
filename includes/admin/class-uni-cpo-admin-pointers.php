<?php
/**
 * Adds and controls pointers for contextual help/tutorials
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Uni_Cpo_Admin_Pointers Class.
 */
class Uni_Cpo_Admin_Pointers {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'setup_pointers_for_screen' ) );
	}

	/**
	 * Setup pointers for screen.
	 */
	public function setup_pointers_for_screen() {
		if ( ! $screen = get_current_screen() ) {
			return;
		}

		switch ( $screen->id ) {
			case 'product':
				$this->create_product_tutorial();
				break;
		}
	}

	/**
	 * Pointers for creating a product.
	 */
	public function create_product_tutorial() {
		if ( ! isset( $_GET['cpo-tutorial'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		// These pointers will chain - they will not be shown at once.
		$pointers = array(
			'pointers' => array(
				'title'          => array(
					'target'       => '#title',
					'next'         => 'product_type',
					'next_trigger' => array(
						'target' => '#title',
						'event'  => 'input',
					),
					'options'      => array(
						'content'  => '<h3>' . esc_html__( 'Product name', 'uni-cpo' ) . '</h3>' .
										'<p>' . esc_html__( 'It is not directly related to Uni CPO, but always remember adding product name.', 'uni-cpo' ) . '</p>',
						'position' => array(
							'edge'  => 'top',
							'align' => 'left',
						),
					),
				),
				'product_type'        => array(
					'target'       => '#product-type',
					'next'         => 'regular_price',
					'next_trigger' => array(),
					'options'      => array(
						'content'  => '<h3>' . esc_html__( 'Product type', 'uni-cpo' ) . '</h3>' .
										'<p>' . esc_html__( 'You should choose "simple" or "simple subscription". Uni CPO does not support other types!', 'uni-cpo' ) . '</p>',
						'position' => array(
							'edge'  => 'bottom',
							'align' => 'middle',
						),
					),
				),
				'regular_price'  => array(
					'target'       => '#_regular_price',
					'next'         => 'submitdiv',
					'next_trigger' => array(
						'target' => '#_regular_price',
						'event'  => 'input',
					),
					'options'      => array(
						'content'  => '<h3>' . esc_html__( 'Prices', 'uni-cpo' ) . '</h3>' .
										'<p>' . esc_html__( 'Next you need to give your product a price otherwise it will be considered as free and Uni CPO visual form builder will not be shown.', 'uni-cpo' ) . '</p>',
						'position' => array(
							'edge'  => 'bottom',
							'align' => 'middle',
						),
					),
				),
				'submitdiv'      => array(
					'target'  => '#submitdiv',
					'next'    => 'cpo_tab',
					'options' => array(
						'content'  => '<h3>' . esc_html__( 'Create/save a product', 'uni-cpo' ) . '</h3>' .
						              '<p>' . esc_html__( 'Hit the "Publish" button to save changes. You may save the product as draft as well.', 'uni-cpo' ) . '</p>',
						'position' => array(
							'edge'  => 'right',
							'align' => 'middle',
						),
					),
				),
				'cpo_tab'   => array(
					'target'  => '.uni_cpo_settings_tab',
					'next'    => '',
					'options' => array(
						'content'  => '<h3>' . esc_html__( 'Start configuring the product', 'uni-cpo' ) . '</h3>' .
										'<p>' . esc_html__( 'Open "CPO Form builder" tab and click "Go to builder" to start configuring your product', 'uni-cpo' ) . '</p>',
						'position' => array(
							'edge'  => 'left',
							'align' => 'middle',
						),
					),
				)
			),
		);

		$this->enqueue_pointers( $pointers );
	}

	/**
	 * Enqueue pointers and add script to page.
	 *
	 * @param array $pointers
	 */
	public function enqueue_pointers( $pointers ) {
		$pointers = wp_json_encode( $pointers );
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
		wc_enqueue_js(
			"jQuery( function( $ ) {
				var uni_cpo_pointers = {$pointers};

				setTimeout( init_uni_cpo_pointers, 800 );

				function init_uni_cpo_pointers() {
					$.each( uni_cpo_pointers.pointers, function( i ) {
						show_uni_cpo_pointer( i );
						return false;
					});
				}

				function show_uni_cpo_pointer( id ) {
					var pointer = uni_cpo_pointers.pointers[ id ];
					var options = $.extend( pointer.options, {
						pointerClass: 'wp-pointer wc-pointer',
						close: function() {
							if ( pointer.next ) {
								show_uni_cpo_pointer( pointer.next );
							}
						},
						buttons: function( event, t ) {
							var close   = '" . esc_js( __( 'Dismiss', 'uni-cpo' ) ) . "',
								next    = '" . esc_js( __( 'Next', 'uni-cpo' ) ) . "',
								button  = $( '<a class=\"close\" href=\"#\">' + close + '</a>' ),
								button2 = $( '<a class=\"button button-primary\" href=\"#\">' + next + '</a>' ),
								wrapper = $( '<div class=\"wc-pointer-buttons\" />' );

							button.bind( 'click.pointer', function(e) {
								e.preventDefault();
								t.element.pointer('destroy');
							});

							button2.bind( 'click.pointer', function(e) {
								e.preventDefault();
								t.element.pointer('close');
							});

							wrapper.append( button );
							wrapper.append( button2 );

							return wrapper;
						},
					} );
					var this_pointer = $( pointer.target ).pointer( options );
					this_pointer.pointer( 'open' );

					if ( pointer.next_trigger ) {
						$( pointer.next_trigger.target ).on( pointer.next_trigger.event, function() {
							setTimeout( function() { this_pointer.pointer( 'close' ); }, 400 );
						});
					}
				}
			});"
		);
	}
}

new Uni_Cpo_Admin_Pointers();
