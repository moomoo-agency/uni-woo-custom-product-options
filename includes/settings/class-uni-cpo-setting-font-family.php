<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Font_Family class
*
*/

class Uni_Cpo_Setting_Font_Family extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'font_family';
		$this->setting_data = array(
			'title'   => __( 'Font Family', 'uni-cpo' ),
			'type'    => 'text',
			'options' => array(
				'inherit'   => __( 'Default', 'uni-cpo' ),
				'Helvetica' => __( 'Helvetica', 'uni-cpo' ),
				'Verdana'   => __( 'Verdana', 'uni-cpo' ),
				'Arial'     => __( 'Arial', 'uni-cpo' ),
				'Georgia'   => __( 'Georgia', 'uni-cpo' ),
				'Courier'   => __( 'Courier', 'uni-cpo' )
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
                <div class="uni-modal-row-second">
					<?php echo $this->generate_select_html() ?>
                </div>
            </div>
        </script>
		<?php
	}

}
