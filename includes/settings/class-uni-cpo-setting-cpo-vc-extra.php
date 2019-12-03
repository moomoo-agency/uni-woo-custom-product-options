<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Vc_Extra class
*
*/
class Uni_Cpo_Setting_Cpo_Vc_Extra extends Uni_Cpo_Setting implements  Uni_Cpo_Setting_Interface 
{
    /**
     * Init
     *
     */
    public function __construct()
    {
        $this->setting_key = 'cpo_vc_extra';
        $this->setting_data = array(
            'title'      => __( 'Extra validation rules', 'uni-cpo' ),
            'is_tooltip' => true,
            'desc_tip'   => __( 'Add new validation rules for this option', 'uni-cpo' ),
            'value'      => '{{- data }}',
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
                    <div class="uni-setting-fields-wrap-2 uni-clear">
						<?php 
        echo  $this->generate_textarea_html() ;
        ?>
                    </div>
                </div>
            </div>
        </script>
		<?php 
    }

}