<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Radio_Options class
*
*/
class Uni_Cpo_Setting_Cpo_Radio_Options extends Uni_Cpo_Setting implements  Uni_Cpo_Setting_Interface 
{
    /**
     * Init
     *
     */
    public function __construct()
    {
        $this->setting_key = 'cpo_radio_options';
        $this->setting_data = array(
            'title'              => __( 'Sub options', 'uni-cpo' ),
            'is_tooltip'         => true,
            'desc_tip'           => __( 'Add some sub options for this option and, please, keep unique slugs for them. These slugs might be used in a formula conditional rules (e.g. when you use operators "equal", "not equal" etc).', 'uni-cpo' ),
            'is_tooltip_warning' => true,
            'desc_tip_warning'   => __( 'Important to save to DB if modified', 'uni-cpo' ),
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
            <div class="uni-modal-row uni-clear">
                <div class="uni-select-option-repeat">
                    <div class="uni-select-option-repeat-wrapper">
                        <div class="uni-select-option-add-wrapper uni-clear">
							<?php 
        echo  $this->generate_field_label_html() ;
        ?>
                            <div class="uni-modal-row-second">
                                <span class="uni_select_option_add"><?php 
        echo  esc_html__( 'Add', 'uni-cpo' ) ;
        ?></span>
                            </div>
                        </div>

                        <div class="uni-select-option-options-wrapper">

                            <div class="uni-select-option-options-template uni-select-option-options-row">
                                <div class="uni-select-option-move-wrapper">
                                    <span class="uni_select_option_move"><i class="fas fa-arrows-alt"></i></span>
                                </div>
                                <div class="uni-select-option-content-wrapper uni-clear">
                                    <div class="uni-select-option-content-field-wrapper uni-make-default-suboption uni-clear">
										<?php 
        echo  $this->generate_radio_html( $this->setting_key . '[<%row-count%>][def]', array(
            'no_init_class' => true,
            'options'       => array(
            'checked' => __( 'Make default?', 'uni-cpo' ),
        ),
            'class'         => array( 'uni-cpo-deselectable-input' ),
            'js_var'        => '1',
        ) ) ;
        ?>
                                    </div>
                                    <div class="uni-select-option-content-field-wrapper uni-exclude-suboption uni-clear">
		                                <?php 
        echo  $this->generate_checkbox_html( $this->setting_key . '[<%row-count%>][excl]', array(
            'no_init_class' => true,
            'label'         => __( 'Exclude?', 'uni-cpo' ),
            'js_var'        => '[]',
        ) ) ;
        ?>
                                    </div>
									<div class="uni-clear"></div>
                                    <div class="uni-select-option-content-field-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-item uni-clear">
                                            <label>
												<?php 
        echo  esc_html__( 'Label', 'uni-cpo' ) ;
        ?>
                                                <span class="uni-marked-required">*</span>
                                            </label>
											<?php 
        echo  $this->generate_text_html( $this->setting_key . '[<%row-count%>][label]', array(
            'no_init_class'     => true,
            'is_required'       => true,
            'class'             => array( 'js-cpo-label-slug-field' ),
            'custom_attributes' => array(
            'data-related-slug' => $this->setting_key . '\\[<%row-count%>\\]\\[slug\\]',
        ),
        ) ) ;
        ?>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-item uni-clear">
                                            <label>
												<?php 
        echo  esc_html__( 'Value', 'uni-cpo' ) ;
        ?>
                                                <span class="uni-marked-required">*</span>
                                            </label>
											<?php 
        echo  $this->generate_text_html( $this->setting_key . '[<%row-count%>][slug]', array(
            'no_init_class'     => true,
            'is_required'       => true,
            'custom_attributes' => array(
            'data-parsley-pattern'    => '/^[a-z][a-z0-9_]*$/',
            'data-parsley-notequalto' => '.js-cpo-slug-field',
        ),
            'class'             => array( 'js-cpo-slug-field' ),
        ) ) ;
        ?>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-item uni-clear">
                                            <label><?php 
        echo  esc_html__( 'Price / Rate (optional)', 'uni-cpo' ) ;
        ?></label>
											<?php 
        echo  $this->generate_text_html( $this->setting_key . '[<%row-count%>][rate]', array(
            'no_init_class'     => true,
            'custom_attributes' => array(
            'data-parsley-pattern' => '/^[\\-]{0,1}(\\d+(?:[\\.]\\d{0,4})?)$/',
        ),
            'class'             => array( 'js-cpo-rate-field' ),
        ) ) ;
        ?>
                                        </div>
                                    </div>
                                    <div class="uni-select-option-content-field-wrapper uni-clear<?php 
        echo  ' uni-premium-content' ;
        ?>">
                                    	<div class="uni-select-option-content-field-wrapper-item uni-clear">
                                            <label>
												<?php 
        echo  esc_html__( 'CSS Class (optional)', 'uni-cpo' ) ;
        ?>
                                            </label>
											<?php 
        echo  $this->generate_text_html( $this->setting_key . '[<%row-count%>][suboption_class]', array(
            'no_init_class' => true,
        ) ) ;
        ?>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-item uni-clear">
                                            <label>
                                                <?php 
        echo  esc_html__( 'Description (optional)', 'uni-cpo' ) ;
        ?>
                                            </label>
                                            <?php 
        echo  $this->generate_text_html( $this->setting_key . '[<%row-count%>][suboption_text]', array(
            'no_init_class' => true,
        ) ) ;
        ?>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-item uni-select-option-content-field-wrapper-colour uni-clear">
                                        	<label>
												<?php 
        echo  esc_html__( 'Colour (optional)', 'uni-cpo' ) ;
        ?>
                                            </label>
                                            <?php 
        echo  $this->generate_text_html( $this->setting_key . '[<%row-count%>][suboption_colour]', array(
            'no_init_class' => true,
            'class'         => array( 'builderius-setting-colorpick' ),
        ) ) ;
        ?>
                                        </div>
                                        <div class="uni-clear"></div>
                                        <div class="uni-select-option-content-field-wrapper-item uni-select-option-content-field-wrapper-image uni-clear uni-clear">
                                            <label>
												<?php 
        echo  esc_html__( 'Image', 'uni-cpo' ) ;
        ?>
												<?php 
        echo  uni_cpo_help_tip( __( 'Optional! Is used as a suboption image as well as can be used as the one that replaces the main product image', 'uni-cpo' ) ) ;
        ?>
                                            </label>
											<?php 
        echo  $this->generate_media_upload_html( $this->setting_key . '[<%row-count%>][attach_id]', array(
            'additional_fields' => array(
            $this->setting_key . '[<%row-count%>][attach_uri]'  => array(
            'class' => 'cpo_suboption_attach_uri',
            'value' => '',
        ),
            $this->setting_key . '[<%row-count%>][attach_name]' => array(
            'class' => 'cpo_suboption_attach_name',
            'value' => '',
        ),
        ),
            'preview'           => '',
            'no_init_class'     => true,
            'value'             => '',
        ) ) ;
        ?>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-item uni-select-option-content-field-wrapper-image uni-clear uni-clear">
                                            <label>
			                                    <?php 
        echo  esc_html__( 'Alt Image', 'uni-cpo' ) ;
        ?>
			                                    <?php 
        echo  uni_cpo_help_tip( __( 'Optional! Is used ONLY as the one that replaces the main product image', 'uni-cpo' ) ) ;
        ?>
                                            </label>
		                                    <?php 
        echo  $this->generate_media_upload_html( $this->setting_key . '[<%row-count%>][attach_id_r]', array(
            'additional_fields' => array(
            $this->setting_key . '[<%row-count%>][attach_uri_r]' => array(
            'class' => 'cpo_suboption_attach_uri',
            'value' => '',
        ),
        ),
            'preview'           => '',
            'no_init_class'     => true,
            'value'             => '',
        ) ) ;
        ?>
                                        </div>

                                    </div>
                                </div>
                                <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                    <span class="uni_select_option_remove">
                                        <i class="fas fa-times"></i>
                                    </span>
                                </div>
                            </div>

                            {{ if( ! _.isEmpty(data) ) { }}
                            {{ _.each(data, function(obj, i) { }}
                            {{ let def = '-1'; }}
                            {{ if ( typeof obj.def !== 'undefined' ) { }}
                            {{ def = obj.def; }}
                            {{ } }}
                            <div class="uni-select-option-options-row">
                                <div class="uni-select-option-move-wrapper">
                                    <span class="uni_select_option_move"><i class="fas fa-arrows-alt"></i></span>
                                </div>
                                <div class="uni-select-option-content-wrapper uni-clear">
                                    <div class="uni-select-option-content-field-wrapper uni-make-default-suboption uni-clear">
										<?php 
        echo  $this->generate_radio_html( $this->setting_key . '[{{- i }}][def]', array(
            'options' => array(
            'checked' => __( 'Make default?', 'uni-cpo' ),
        ),
            'class'   => array( 'uni-cpo-deselectable-input' ),
            'js_var'  => 'def',
        ) ) ;
        ?>
                                    </div>
                                    <div class="uni-select-option-content-field-wrapper uni-exclude-suboption uni-clear">
		                                <?php 
        echo  $this->generate_checkbox_html( $this->setting_key . '[{{- i }}][excl]', array(
            'label'  => __( 'Exclude?', 'uni-cpo' ),
            'js_var' => 'obj.excl',
        ) ) ;
        ?>
                                    </div>
									<div class="uni-clear"></div>
                                    <div class="uni-select-option-content-field-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-item uni-clear">
                                            <label>
												<?php 
        echo  esc_html__( 'Label', 'uni-cpo' ) ;
        ?>
                                                <span class="uni-marked-required">*</span>
                                            </label>
											<?php 
        echo  $this->generate_text_html( $this->setting_key . '[{{- i }}][label]', array(
            'is_required'       => true,
            'value'             => '{{- obj.label }}',
            'class'             => array( 'js-cpo-label-slug-field' ),
            'custom_attributes' => array(
            'data-related-slug' => $this->setting_key . '\\[{{- i }}\\]\\[slug\\]',
        ),
        ) ) ;
        ?>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-item uni-clear">
                                            <label>
												<?php 
        echo  esc_html__( 'Value', 'uni-cpo' ) ;
        ?>
                                                <span class="uni-marked-required">*</span>
                                            </label>
											<?php 
        echo  $this->generate_text_html( $this->setting_key . '[{{- i }}][slug]', array(
            'is_required'       => true,
            'custom_attributes' => array(
            'data-parsley-pattern'    => '/^[a-z][a-z0-9_]*$/',
            'data-parsley-notequalto' => '.js-cpo-slug-field',
        ),
            'value'             => '{{- obj.slug }}',
            'class'             => array( 'js-cpo-slug-field' ),
        ) ) ;
        ?>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-item uni-clear">
                                            <label><?php 
        echo  esc_html__( 'Price / Rate (optional)', 'uni-cpo' ) ;
        ?></label>
											<?php 
        echo  $this->generate_text_html( $this->setting_key . '[{{- i }}][rate]', array(
            'value'             => '{{- obj.rate }}',
            'custom_attributes' => array(
            'data-parsley-pattern' => '/^[\\-]{0,1}(\\d+(?:[\\.]\\d{0,4})?)$/',
        ),
            'class'             => array( 'js-cpo-rate-field' ),
        ) ) ;
        ?>
                                        </div>
                                    </div>
                                    <div class="uni-select-option-content-field-wrapper uni-clear<?php 
        echo  ' uni-premium-content' ;
        ?>">

                                        <div class="uni-select-option-content-field-wrapper-item uni-clear">
                                            <label><?php 
        echo  esc_html__( 'CSS Class (optional)', 'uni-cpo' ) ;
        ?></label>
											<?php 
        echo  $this->generate_text_html( $this->setting_key . '[{{- i }}][suboption_class]', array(
            'value' => '{{- obj.suboption_class }}',
        ) ) ;
        ?>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-item uni-clear">
                                            <label><?php 
        echo  esc_html__( 'Description (optional)', 'uni-cpo' ) ;
        ?></label>
                                            <?php 
        echo  $this->generate_text_html( $this->setting_key . '[{{- i }}][suboption_text]', array(
            'value' => '{{- obj.suboption_text }}',
        ) ) ;
        ?>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-item uni-select-option-content-field-wrapper-colour uni-clear">
                                        	<label>
												<?php 
        echo  esc_html__( 'Colour (optional)', 'uni-cpo' ) ;
        ?>
                                            </label>
                                            <?php 
        echo  $this->generate_text_html( $this->setting_key . '[{{- i }}][suboption_colour]', array(
            'value' => '{{- obj.suboption_colour }}',
            'class' => array( 'builderius-setting-colorpick' ),
        ) ) ;
        ?>
                                        </div>
                                        <div class="uni-clear"></div>
                                        <div class="uni-select-option-content-field-wrapper-item uni-select-option-content-field-wrapper-image uni-clear">
                                            <label>
												<?php 
        echo  esc_html__( 'Image', 'uni-cpo' ) ;
        ?>
												<?php 
        echo  uni_cpo_help_tip( __( 'Optional! Is used as a suboption image as well as can be used as the one that replaces the main product image', 'uni-cpo' ) ) ;
        ?>
                                            </label>
											<?php 
        echo  $this->generate_media_upload_html( $this->setting_key . '[{{- i }}][attach_id]', array(
            'additional_fields' => array(
            $this->setting_key . '[{{- i }}][attach_uri]'  => array(
            'class' => 'cpo_suboption_attach_uri',
            'value' => '{{- obj.attach_uri }}',
        ),
            $this->setting_key . '[{{- i }}][attach_name]' => array(
            'class' => 'cpo_suboption_attach_name',
            'value' => '{{- obj.attach_name }}',
        ),
        ),
            'preview'           => '{{- obj.attach_uri }}',
            'value'             => '{{- obj.attach_id }}',
            'js_var'            => 'obj.attach_id',
        ) ) ;
        ?>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-item uni-select-option-content-field-wrapper-image uni-clear uni-clear">
                                            <label>
			                                    <?php 
        echo  esc_html__( 'Alt Image', 'uni-cpo' ) ;
        ?>
			                                    <?php 
        echo  uni_cpo_help_tip( __( 'Optional! Is used ONLY as the one that replaces the main product image', 'uni-cpo' ) ) ;
        ?>
                                            </label>
		                                    <?php 
        echo  $this->generate_media_upload_html( $this->setting_key . '[{{- i }}][attach_id_r]', array(
            'additional_fields' => array(
            $this->setting_key . '[{{- i }}][attach_uri_r]' => array(
            'class' => 'cpo_suboption_attach_uri',
            'value' => '{{- obj.attach_uri_r }}',
        ),
        ),
            'preview'           => '{{- obj.attach_uri_r }}',
            'value'             => '{{- obj.attach_id_r }}',
            'js_var'            => 'obj.attach_id_r',
        ) ) ;
        ?>
                                        </div>

                                    </div>
                                </div>
                                <div class="uni-select-option-content-field-wrapper uni-select-option-remove-wrapper">
                                    <span class="uni_select_option_remove">
                                        <i class="fas fa-times"></i>
                                    </span>
                                </div>
                            </div>
                            {{ }); }}
                            {{ } }}

                        </div>

                    </div>
                </div>
            </div>
        </script>
		<?php 
    }

}