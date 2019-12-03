<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Tooltip_Image class
*
*/

class Uni_Cpo_Setting_Cpo_Tooltip_Image extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'cpo_tooltip_image';
		$this->setting_data = array(
			'title' => esc_html__( 'Tooltip Image', 'uni-cpo' ),
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
            <div class="uni-modal-row uni-clear" data-uni-constrained="select[name=cpo_tooltip_type]" data-uni-constvalue="lightbox">
				<?php echo $this->generate_field_label_html() ?>
                <div class="uni-modal-row-second uni-modal-row_image uni-clear">
	                <?php
	                echo $this->generate_media_upload_html(
		                $this->setting_key . '[id]',
		                array(
			                'additional_fields' => array(
				                $this->setting_key . '[url]'  => array(
					                'class' => 'cpo_suboption_attach_uri',
					                'value' => '{{- data.url }}'
				                ),
				                $this->setting_key . '[alt]' => array(
					                'class' => 'cpo_suboption_attach_name',
					                'value' => '{{- data.alt }}'
				                )
			                ),
			                'preview'           => '{{- data.url }}',
			                'alt'               => '{{- data.alt }}',
			                'value'             => '{{- data.id }}',
			                'js_var'            => 'data.url'
		                )
	                );
	                ?>
                </div>
            </div>
        </script>
		<?php
	}

}
