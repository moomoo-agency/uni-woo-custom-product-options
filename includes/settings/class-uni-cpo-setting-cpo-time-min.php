<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Time_Min class
*
*/

class Uni_Cpo_Setting_Cpo_Time_Min extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'cpo_time_min';
		$this->setting_data = array(
			'title'      => __( 'Minimum time', 'uni-cpo' ),
			'is_tooltip' => true,
			'desc_tip'   => __( 'Add the minimum/earliest time (inclusively) allowed for selection. You can use only 24-h format. For example: 09:00 or 19:00.', 'uni-cpo' ),
			'value'      => '{{- data }}'
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
            <div class="uni-modal-row uni-clear" data-uni-constrained="input[name=cpo_is_timepicker]" data-uni-constvalue="yes">
				<?php echo $this->generate_field_label_html(); ?>
                <div class="uni-modal-row-second uni-clear">
					<?php echo $this->generate_text_html(); ?>
                </div>
            </div>
        </script>
		<?php
	}

}
