<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Matrix_Data class
*
*/
class Uni_Cpo_Setting_Cpo_Matrix_Data extends Uni_Cpo_Setting implements  Uni_Cpo_Setting_Interface 
{
    /**
     * Init
     *
     */
    public function __construct()
    {
        $this->setting_key = 'cpo_matrix_data';
        $this->setting_data = array(
            'title'      => __( 'Matrix data', 'uni-cpo' ),
            'is_tooltip' => true,
            'desc_tip'   => __( '', 'uni-cpo' ),
            'js_var'     => 'data',
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
            <?php 
        ?>
        </script>
        <?php 
    }

}