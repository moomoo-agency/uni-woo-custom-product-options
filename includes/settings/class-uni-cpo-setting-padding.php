<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Padding class
*
*/

class Uni_Cpo_Setting_Padding extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'padding';
		$this->setting_data = array(
			'title' => __( 'Paddings', 'builderius' ),
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
                    <div class="uni-setting-fields-wrap">
						<?php
						echo $this->generate_text_html(
							'padding[top]',
							array( 'class' => array( 'uni-setting-field-1' ), 'value' => '{{- data.top }}' )
						);
						echo $this->generate_text_html(
							'padding[right]',
							array( 'class' => array( 'uni-setting-field-1' ), 'value' => '{{- data.right }}' )
						);
						echo $this->generate_text_html(
							'padding[bottom]',
							array( 'class' => array( 'uni-setting-field-1' ), 'value' => '{{- data.bottom }}' )
						);
						echo $this->generate_text_html(
							'padding[left]',
							array( 'class' => array( 'uni-setting-field-1' ), 'value' => '{{- data.left }}' )
						);
						echo $this->generate_linked_checkbox_html();
						echo $this->generate_radio_html(
							'padding[unit]',
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
