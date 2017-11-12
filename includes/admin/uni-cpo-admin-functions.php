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

// CPO settings tab
add_filter( 'woocommerce_product_data_tabs', 'uni_cpo_add_settings_tab' );
function uni_cpo_add_settings_tab( $product_data_tabs ) {

	$product_data_tabs['uni_cpo_settings'] = array(
		'label'  => __( 'CPO Form Builder', 'uni-cpo' ),
		'target' => 'uni_cpo_settings_data',
		'class'  => array( 'hide_if_grouped', 'hide_if_external', 'hide_if_variable' ),
	);

	return $product_data_tabs;
}

// CPO settings (price formula) tab content
add_action( 'woocommerce_product_data_panels', 'uni_cpo_add_custom_settings_tab_content' );
function uni_cpo_add_custom_settings_tab_content() {
	?>
    <div id="uni_cpo_settings_data" class="panel woocommerce_options_panel">
        <a
                href="<?php echo esc_url( Uni_Cpo_Product::get_edit_url() ); ?>"
                target="_blank">
			<?php esc_html_e( 'go to the builder', 'uni-cpo' ); ?>
        </a>
    </div>
	<?php
}

//
add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'uni_cpo_order_formatted_meta_data', 10, 2 );
function uni_cpo_order_formatted_meta_data( $formatted_meta, $item ) {
	try {
		$meta_data = $item->get_meta_data();

		array_walk(
			$meta_data,
			function ( $v ) use ( &$formatted_meta ) {
				$meta_data = $v->get_data();
				if ( false !== strpos( $meta_data['key'], UniCpo()->get_var_slug() ) ) {
					$slug = ltrim( $meta_data['key'], '_' );
					$post = uni_cpo_get_post_by_slug( $slug );

					if ( $post ) {
						$option = uni_cpo_get_option( $post->ID );
						if ( is_object( $option ) ) {
							$display_key   = uni_cpo_sanitize_label( $option->cpo_order_label() );
							$display_value = $meta_data['value'];
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
										if ( $meta_data['value'] === $suboption['slug'] ) {
											$display_value = $suboption['label'];
											break;
										}
									}
								}
							}
							$formatted_meta[ $meta_data['id'] ] = (object) array(
								'key'           => $meta_data['key'],
								'value'         => $meta_data['value'],
								'display_key'   => apply_filters( 'uni_cpo_order_item_display_meta_key', $display_key, $v ),
								'display_value' => wpautop( make_clickable( apply_filters( 'uni_cpo_order_item_display_meta_value', $display_value, $v ) ) ),
							);
						}
					}

				}
			}
		);

		return $formatted_meta;
	} catch ( Exception $e ) {
		return new WP_Error( 'cart-error', $e->getMessage() );
	}
}
