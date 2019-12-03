<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Min_Val class
*
*/

class Uni_Cpo_Setting_Cpo_Min_Val extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'cpo_min_val';
		$this->setting_data = array(
			'title'             => __( 'Minimum value', 'uni-cpo' ),
			'is_tooltip'        => true,
			'desc_tip'          => __( 'Add the minimum allowed value for this field. Only integer or float number.', 'uni-cpo' ),
			'custom_attributes' => array(
				'data-parsley-pattern' => '/^(-?\d+(?:[\.]\d{0,4})?)$/',
			),
			'value'             => '{{- data }}'
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
            <div class="uni-modal-row uni-clear" data-uni-constrained="input[name=cpo_type]"
                 data-uni-constvalue="integer|double">
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
