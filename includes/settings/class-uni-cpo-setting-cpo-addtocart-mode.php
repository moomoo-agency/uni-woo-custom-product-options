<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Addtocart_mode class
*
*/

class Uni_Cpo_Setting_Cpo_Addtocart_mode extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'cpo_addtocart_mode';
		$this->setting_data = array(
			'title'      => __( 'Mode', 'uni-cpo' ),
			'is_tooltip' => true,
			'desc_tip'   => __( 'Choose between using like a regular "add to cart" button or specifically for "free sample" functionality (marks cart item as "sample").', 'uni-cpo' ),
			'options'    => array(
				'regular'  => __( 'Regular', 'uni-cpo' ),
				'samples' => __( '"Free sample"', 'uni-cpo' )
			),
			'js_var'     => 'data'
		);
		add_action( 'wp_footer', array( $this, 'js_template' ), 10 );
	}


	/**
	 * A template for the module
	 *
	 * @since 1.0
	 * @return string
	 */
	public function js_template() {
		?>
        <script id="js-builderius-setting-<?php echo $this->setting_key; ?>-tmpl" type="text/template">
            <div class="uni-modal-row uni-clear">
				<?php echo $this->generate_field_label_html(); ?>
                <div class="uni-modal-row-second uni-clear">
                    <div class="uni-setting-fields-wrap-2 uni-clear">
						<?php echo $this->generate_radio_html(); ?>
                    </div>
                </div>
            </div>
        </script>
		<?php
	}

}
