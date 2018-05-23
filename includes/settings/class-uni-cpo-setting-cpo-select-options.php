<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Select_Options class
*
*/

class Uni_Cpo_Setting_Cpo_Select_Options extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'cpo_select_options';
		$this->setting_data = array(
			'title'      => __( 'Sub options', 'uni-cpo' ),
			'is_tooltip' => true,
			'desc_tip'   => __( 'Add some sub options for select and, please, keep unique slugs for them. These slugs might be used in a formula conditional rules (e.g. when you use operators "equal", "not equal" etc).', 'uni-cpo' ),
            'is_tooltip_warning' => true,
            'desc_tip_warning'   => __( 'Important to save to DB if modified', 'uni-cpo' ),
			'js_var'     => 'data'
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
                <div class="uni-select-option-repeat">

                    <div class="uni-select-option-repeat-wrapper">
                        <div class="uni-select-option-add-wrapper uni-clear">
							<?php echo $this->generate_field_label_html() ?>
                            <div class="uni-modal-row-second">
                                <span class="uni_select_option_add"><?php echo esc_html__( 'Add', 'uni-cpo' ) ?></span>
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
										echo $this->generate_radio_html(
											$this->setting_key . '[<%row-count%>][def]',
											array(
												'no_init_class' => true,
												'options'       => array(
													'checked' => __( 'Make default?', 'uni-cpo' )
												),
												'class'         => array( 'uni-cpo-deselectable-input' ),
												'js_var'        => '1'
											)
										);
										?>
                                    </div>
                                    <div class="uni-select-option-content-field-wrapper uni-exclude-suboption uni-clear">
		                                <?php
		                                echo $this->generate_checkbox_html(
			                                $this->setting_key . '[<%row-count%>][excl]',
			                                array(
				                                'no_init_class' => true,
				                                'label'         => __( 'Exclude?', 'uni-cpo' ),
				                                'js_var'        => '[]'
			                                )
		                                );
		                                ?>
                                    </div>
									<div class="uni-clear"></div>
                                    <div class="uni-select-option-content-field-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-item uni-clear">
                                            <label>
												<?php echo esc_html__( 'Title', 'uni-cpo' ) ?>
                                                <span class="uni-marked-required">*</span>
                                            </label>
											<?php
											echo $this->generate_text_html(
												$this->setting_key . '[<%row-count%>][label]',
												array(
													'no_init_class'     => true,
													'is_required'       => true,
													'class'             => array( 'js-cpo-label-slug-field' ),
													'custom_attributes' => array(
														'data-related-slug' => $this->setting_key . '\[<%row-count%>\]\[slug\]'
													)
												)
											);
											?>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-item uni-clear">
                                            <label>
												<?php echo esc_html__( 'Value', 'uni-cpo' ) ?>
                                                <span class="uni-marked-required">*</span>
                                            </label>
											<?php
											echo $this->generate_text_html(
												$this->setting_key . '[<%row-count%>][slug]',
												array(
													'no_init_class'     => true,
													'is_required'       => true,
													'custom_attributes' => array(
														'data-parsley-pattern' => '/^[a-z][a-z0-9_]*$/',
														'data-parsley-notequalto' => '.js-cpo-slug-field'
													),
													'class'             => array( 'js-cpo-slug-field' )
												)
											);
											?>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-item uni-clear">
                                            <label><?php echo esc_html__( 'Price / Rate', 'uni-cpo' ) ?></label>
											<?php
											echo $this->generate_text_html(
												$this->setting_key . '[<%row-count%>][rate]',
												array(
													'no_init_class'     => true,
													'custom_attributes' => array(
														'data-parsley-pattern' => '/^[\-]{0,1}(\d+(?:[\.]\d{0,4})?)$/',
													),
													'class'             => array( 'js-cpo-rate-field' )
												)
											);
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
										echo $this->generate_radio_html(
											$this->setting_key . '[{{- i }}][def]',
											array(
												'options' => array(
													'checked' => __( 'Make default?', 'uni-cpo' )
												),
												'class'   => array( 'uni-cpo-deselectable-input' ),
												'js_var'  => 'def'
											)
										);
										?>
                                    </div>
                                    <div class="uni-select-option-content-field-wrapper uni-exclude-suboption uni-clear">
		                                <?php
		                                echo $this->generate_checkbox_html(
			                                $this->setting_key . '[{{- i }}][excl]',
			                                array(
				                                'label' => __( 'Exclude?', 'uni-cpo' ),
				                                'js_var'  => 'obj.excl'
			                                )
		                                );
		                                ?>
                                    </div>
									<div class="uni-clear"></div>
                                    <div class="uni-select-option-content-field-wrapper uni-clear">
                                        <div class="uni-select-option-content-field-wrapper-item uni-clear">
                                            <label>
												<?php echo esc_html__( 'Title', 'uni-cpo' ) ?>
                                                <span class="uni-marked-required">*</span>
                                            </label>
											<?php
											echo $this->generate_text_html(
												$this->setting_key . '[{{- i }}][label]',
												array(
													'is_required'       => true,
													'value'             => '{{- obj.label }}',
													'class'             => array( 'js-cpo-label-slug-field' ),
													'custom_attributes' => array(
														'data-related-slug' => $this->setting_key . '\[{{- i }}\]\[slug\]'
													)
												)
											);
											?>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-item uni-clear">
                                            <label>
												<?php echo esc_html__( 'Value', 'uni-cpo' ) ?>
                                                <span class="uni-marked-required">*</span>
                                            </label>
											<?php
											echo $this->generate_text_html(
												$this->setting_key . '[{{- i }}][slug]',
												array(
													'is_required'       => true,
													'custom_attributes' => array(
														'data-parsley-pattern' => '/^[a-z][a-z0-9_]*$/',
														'data-parsley-notequalto' => '.js-cpo-slug-field'
													),
													'value'             => '{{- obj.slug }}',
													'class'             => array( 'js-cpo-slug-field' )
												)
											);
											?>
                                        </div>
                                        <div class="uni-select-option-content-field-wrapper-item uni-clear">
                                            <label><?php echo esc_html__( 'Price / Rate', 'uni-cpo' ) ?></label>
											<?php
											echo $this->generate_text_html(
												$this->setting_key . '[{{- i }}][rate]',
												array(
													'value'             => '{{- obj.rate }}',
													'custom_attributes' => array(
														'data-parsley-pattern' => '/^[\-]{0,1}(\d+(?:[\.]\d{0,4})?)$/',
													),
													'class'             => array( 'js-cpo-rate-field' )
												)
											);
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
