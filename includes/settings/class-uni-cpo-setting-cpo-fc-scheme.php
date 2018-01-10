<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Fc_Scheme class
*
*/

class Uni_Cpo_Setting_Cpo_Fc_Scheme extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'cpo_fc_scheme';
		$this->setting_data = array(
			'title' => __( 'Field conditional logic builder', 'uni-cpo' )
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
            <div class="uni-modal-row uni-clear uni-uni-modal-row-for-builder">
				<?php echo $this->generate_field_label_html(); ?>
                <div class="uni-field-conditional-rules-content-field-wrapper">
                    <div class="uni-query-builder-wrapper">
                        <div id="cpo-field-rule-builder" class="cpo-query-rule-builder-single"></div>
                        <input class="js-uni-fetch-scheme uni-cpo-settings-btn uni-cpo-settings-saved" type="button"
                               value="<?php esc_attr_e( 'Fetch the rule', 'uni-cpo' ) ?>"/>
                    </div>
                    <input id="uni_cpo_field_rule_scheme" type="hidden" name="cpo_fc_scheme" value="{{- data }}"
                           class="builderius-setting-field"/>
                </div>
            </div>
        </script>
		<?php
	}

}
