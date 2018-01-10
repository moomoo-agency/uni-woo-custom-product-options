<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Rate class
*
*/

class Uni_Cpo_Setting_Cpo_Rate extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'cpo_rate';
		$this->setting_data = array(
			'title'              => __( 'Price / Rate', 'uni-cpo' ),
			'is_tooltip'         => true,
			'is_tooltip_warning' => true,
			'desc_tip'           => __( 'Enter the price (or rate) for this field. This value can be used in a formula. 
                Or leave it blank if you need just an information added by the user.', 'uni-cpo' ),
			'desc_tip_warning'   => __( 'Important to save to DB if modified', 'uni-cpo' ),
			'custom_attributes'  => array(
				'data-parsley-pattern' => '/^[\-]{0,1}(\d+(?:[\.]\d{0,4})?)$/',
			),
			'class'              => array( 'js-cpo-rate-field' ),
			'value'              => '{{- data }}'
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
						<?php echo $this->generate_text_html(); ?>
                    </div>
                </div>
            </div>
        </script>
		<?php
	}

}
