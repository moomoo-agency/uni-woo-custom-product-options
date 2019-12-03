<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Validation_Msg class
*
*/
class Uni_Cpo_Setting_Cpo_Validation_Msg extends Uni_Cpo_Setting implements  Uni_Cpo_Setting_Interface 
{
    /**
     * Init
     *
     */
    public function __construct()
    {
        $this->setting_key = 'cpo_validation_msg';
        $this->setting_data = array(
            'title'      => __( 'Custom validation messages', 'uni-cpo' ),
            'is_tooltip' => true,
            'desc_tip'   => __( 'Enables an opportunity to set custom validation message for certain validation event. In case of the last field the format is: "data-parsley-`constraint`-message="my msg"', 'uni-cpo' ),
        );
        add_action( 'wp_footer', array( $this, 'js_template' ), 10 );
    }
    
    /**
     * A template for the module
     *
     * @since 1.0
     * @return string
     */
    public function js_template()
    {
        ?>
        <script id="js-builderius-setting-<?php 
        echo  $this->setting_key ;
        ?>-tmpl" type="text/template">
            <div class="uni-modal-row uni-clear<?php 
        echo  ' uni-premium-content' ;
        ?>">
				<?php 
        echo  $this->generate_field_label_html() ;
        ?>
                <div class="uni-modal-row-second uni-clear">
                    <div class="uni-setting-fields-wrap-5 uni-clear">
						<?php 
        echo  $this->generate_text_html( $this->setting_key . '[req]', array(
            'placeholder' => __( 'when is required', 'uni-cpo' ),
            'value'       => '{{- data.req }}',
        ) ) ;
        ?>
                        {{ if(typeof data.type !== 'undefined') { }}
                        <?php 
        echo  $this->generate_text_html( $this->setting_key . '[type]', array(
            'placeholder' => __( 'when type is invalid', 'uni-cpo' ),
            'value'       => '{{- data.type }}',
        ) ) ;
        ?>
                        {{ } }}
                        <?php 
        echo  $this->generate_textarea_html( $this->setting_key . '[custom]', array(
            'placeholder' => __( 'for any custom validation', 'uni-cpo' ),
            'value'       => '{{- data.custom }}',
        ) ) ;
        ?>
                    </div>
                </div>
            </div>
        </script>
		<?php 
    }

}