<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Notice_Text class
*
*/

class Uni_Cpo_Setting_Cpo_Notice_Text extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'cpo_notice_text';
		$this->setting_data = array(
			'title' => __( 'Notice text', 'uni-cpo' ),
			'is_tooltip' => true,
			'desc_tip'   => __( 'Custom text for this field. Simple HTML tags can be used here.', 'uni-cpo' ),
			'value' => '{{- data }}'
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
					<?php echo $this->generate_textarea_html(); ?>
                </div>
            </div>
			<div class="uni-modal-row uni-modal-row-variables-list uni-clear">
				<?php
				echo $this->generate_field_label_html(
					'cpo_notice_text[variables-list]',
					array(
						'title' => esc_html__( 'Available variables', 'uni-cpo' ),
					)
				)
				?>
                <div class="uni-modal-row-second">
                    <ul class="uni-variables-list uni-clear">
                        {{ _.each(Builderius._optionVars, function(arr, group){ }}
                        {{ if (arr) { }}
                        {{ _.each(arr, function(value){ }}
                        <li class="uni-cpo-var-{{- group }}">
                            <span>{{= '\{\{\{'+'data.'+value+'\}\}\}' }}</span>
                        </li>
                        {{ }); }}
                        {{ } }}
                        {{ }); }}
                        {{ _.each(builderiusCfg.cpo_data.other_vars, function(value){ }}
                        <li class="uni-cpo-var-other">
                            <span>{{= '\{\{\{'+'data.'+value+'\}\}\}' }}</span>
                        </li>
                        {{ }); }}
                    </ul>
                </div>
            </div>
        </script>
		<?php
	}

}
