<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Radius class
*
*/

class Uni_Cpo_Setting_Radius extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'radius';
		$this->setting_data = array(
			'title' => __( 'Radius', 'uni-cpo' ),
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
                <div class="uni-modal-row-second uni-clear">
                    <div class="uni-setting-fields-wrap-2 uni-clear">
						<?php
						echo $this->generate_text_html(
							'radius[value]',
							array(
								'class'             => array(
									'uni-small'
								),
								'custom_attributes' => array(
									'data-parsley-type' => 'number'
								),
								'value'             => '{{= data.value }}'
							)
						);
						echo $this->generate_radio_html(
							'radius[unit]',
							array(
								'options' => array(
									'px' => 'px',
									'%'  => '%'
								),
								'js_var'  => 'data.unit'
							)
						);
						?>
                    </div>
                </div>
            </div>
        </script>
		<?php
	}

}
