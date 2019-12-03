<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Border_Unit class
*
*/

class Uni_Cpo_Setting_Border_Unit extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'border_unit';
		$this->setting_data = array(
			'title' => '',
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
                <div class="uni-modal-row-second uni-clear uni-setting-fields-wrap-4">
                    <div class="uni-setting-fields-wrap-4">
                        <div class="uni-linked-border"></div>
						<?php
						echo $this->generate_linked_checkbox_html(
							'border|width'
						);
						echo $this->generate_radio_html(
							'border_unit',
							array(
								'options' => array(
									'px' => 'px'
								),
								'js_var'  => 'data'
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
