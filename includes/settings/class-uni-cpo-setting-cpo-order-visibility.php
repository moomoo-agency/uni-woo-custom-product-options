<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Order_Visibility class
*
*/
class Uni_Cpo_Setting_Cpo_Order_Visibility extends Uni_Cpo_Setting implements  Uni_Cpo_Setting_Interface 
{
    /**
     * Init
     *
     */
    public function __construct()
    {
        $this->setting_key = 'cpo_order_visibility';
        $this->setting_data = array(
            'title'              => __( 'Hide meta in cart/order details?', 'uni-cpo' ),
            'is_tooltip'         => true,
            'is_tooltip_warning' => true,
            'desc_tip'           => __( 'Hides this option related meta in cart/order details.', 'uni-cpo' ),
            'desc_tip_warning'   => __( 'Important to save to DB if modified', 'uni-cpo' ),
            'options'            => array(
            'no'  => __( 'No', 'uni-cpo' ),
            'yes' => __( 'Yes', 'uni-cpo' ),
        ),
            'js_var'             => 'data',
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
        echo  $this->generate_radio_html() ;
        ?>
					</div>
				</div>
			</div>
		</script>
		<?php 
    }

}