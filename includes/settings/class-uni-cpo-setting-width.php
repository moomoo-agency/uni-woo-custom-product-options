<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Width class
*
*/

class Uni_Cpo_Setting_Width extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'width';
		$this->setting_data = array(
			'title' => __( 'Width', 'uni-cpo' ),
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
            <div class="uni-modal-row uni-clear" data-uni-constrained="select[name=width_type]"
                data-uni-constvalue="custom">
				<?php echo $this->generate_field_label_html() ?>
                <div class="uni-modal-row-second uni-clear">
                    <div class="uni-setting-fields-wrap-2 uni-clear">
						<?php
						echo $this->generate_text_html(
							'width[value]',
							array(
								'class'             => array(
									'uni-setting-field-2',
									'uni-small'
								),
								'custom_attributes' => array(
									'data-parsley-type' => 'number'
								),
								'value'             => '{{- data.value }}'
							)
						);
						echo $this->generate_radio_html(
							'width[unit]',
							array(
								'options' => array(
									'px'  => 'px',
									'%'   => '%',
									'em'  => 'em',
									'rem' => 'rem'
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
