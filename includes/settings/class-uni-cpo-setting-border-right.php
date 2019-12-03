<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Border_Right class
*
*/

class Uni_Cpo_Setting_Border_Right extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'border_right';
		$this->setting_data = array(
			'title' => esc_html__( 'Right', 'uni-cpo' ),
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
                    <div class="uni-custom-select-wrap">
						<?php
						echo $this->generate_select_html(
							'border_right[style]',
							array(
								'options' => array(
									'none'   => esc_html__( 'None', 'uni-cpo' ),
									'solid'  => esc_html__( 'Solid', 'uni-cpo' ),
									'dashed' => esc_html__( 'Dashed', 'uni-cpo' ),
									'dotted' => esc_html__( 'Dotted', 'uni-cpo' ),
									'double' => esc_html__( 'Double', 'uni-cpo' )
								),
								'js_var'  => 'data.style'
							)
						);
						?>
                    </div>
					<?php
					echo $this->generate_text_html(
						'border_right[width]',
						array(
							'class' => array(
								'uni-border-width'
							),
							'value' => '{{- data.width }}'
						)
					);
					echo $this->generate_text_html(
						'border_right[color]',
						array(
							'class' => array(
								'builderius-setting-colorpick'
							),
							'value' => '{{- data.color }}'
						)
					);
					?>
                </div>
            </div>
        </script>
		<?php
	}

}
