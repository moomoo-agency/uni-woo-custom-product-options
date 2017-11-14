<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Label_Tag class
*
*/

class Uni_Cpo_Setting_Cpo_Label_Tag extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'cpo_label_tag';
		$this->setting_data = array(
			'title'   => __( 'Type of tag for label', 'uni-cpo' ),
			'options' => array(
				'label' => 'label',
				'span'  => 'span',
				'h2'    => 'H2',
				'h3'    => 'H3',
				'h4'    => 'H4',
				'h5'    => 'H5',
				'h6'    => 'H6',
				'p'     => 'p'
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
