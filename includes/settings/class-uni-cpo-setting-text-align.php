<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Text_Align class
*
*/

class Uni_Cpo_Setting_Text_Align extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'text_align';
		$this->setting_data = array(
			'title'   => esc_html__( 'Text Align', 'uni-cpo' ),
			'options' => array(
				'left'   => 'left',
				'center' => 'center',
				'right'  => 'right'
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
                <div class="uni-modal-row-second uni-setting-radio-inputs-2">
					<?php echo $this->generate_radio_html(); ?>
                </div>
            </div>
        </script>
		<?php
	}

}
