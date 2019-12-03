<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Font_Weight class
*
*/

class Uni_Cpo_Setting_Font_Weight extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'font_weight';
		$this->setting_data = array(
			'title'   => __( 'Font Weight', 'uni-cpo' ),
			'type'    => 'text',
			'options' => array(
				''    => __( 'None', 'uni-cpo' ),
				'300' => __( 'Light 300', 'uni-cpo' ),
				'400' => __( 'Normal 400', 'uni-cpo' ),
				'600' => __( 'Semi-Bold 600', 'uni-cpo' ),
				'700' => __( 'Bold 700', 'uni-cpo' ),
				'800' => __( 'Extra-Bold 800', 'uni-cpo' )
			),
			'js_var'  => 'data'
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
				<?php echo $this->generate_field_label_html() ?>
                <div class="uni-modal-row-second">
					<?php echo $this->generate_select_html() ?>
                </div>
            </div>
        </script>
		<?php
	}

}
