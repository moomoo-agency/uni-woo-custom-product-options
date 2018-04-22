<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Custom_Values class
*
*/

class Uni_Cpo_Setting_Cpo_Custom_Values extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'cpo_custom_values';
		$this->setting_data = array(
			'title'              => __( 'Custom values', 'uni-cpo' ),
			'is_tooltip_warning' => true,
			'is_tooltip'         => true,
			'desc_tip'           => __( 'A list of comma separated custom values. You must to add at least two values. Also you can\'t add comma as last char', 'uni-cpo' ),
			'desc_tip_warning'   => __( 'Important to save to DB if modified', 'uni-cpo' ),
			'custom_attributes'  => array(
				'data-parsley-pattern' => '/^(\w{1,}\.{0,1}\w{0,})(,(\w{1,}\.{0,1}\w{0,}))+$/',
			),
			'class'              => array( 'js-cpo-custom-values-field' ),
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
		            <?php echo $this->generate_text_html(); ?>
                </div>
            </div>
        </script>
		<?php
	}

}
