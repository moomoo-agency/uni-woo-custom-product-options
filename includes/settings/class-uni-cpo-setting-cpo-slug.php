<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Slug class
*
*/

class Uni_Cpo_Setting_Cpo_Slug extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'cpo_slug';
		$this->setting_data = array(
			'title'              => __( 'Field slug', 'uni-cpo' ),
			'is_required'        => true,
			'is_tooltip_warning' => true,
			'is_tooltip'         => true,
			'desc_tip'           => __( 'Enter a unique slug name for this field (only lowercase latin letters, digits and underscore!). This will be used as a name of the formula variable connected with this field!', 'uni-cpo' ),
			'desc_tip_warning'   => __( 'Important to save to DB if modified', 'uni-cpo' ),
			'custom_attributes'  => array(
				'data-parsley-pattern' => '/^[a-z0-9]+(?:_[a-z0-9]+)*$/'
			),
			'class'              => array( 'js-cpo-slug-field' ),
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
				<div class="uni-modal-row-second uni-modal-row-slug-wrap">
					<div class="uni-fcell uni-slug-name">
						<?php echo '{' . UniCpo()->get_var_slug(); ?>
					</div>
					<div class="uni-scell uni-clear">
						<div id="js-cpo-field-slug-wrapper">
							<?php echo $this->generate_text_html(); ?>
						</div>
						<?php echo '}'; ?>
					</div>
				</div>
			</div>
		</script>
		<?php
	}

}
