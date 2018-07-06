<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Cpo_Sc_Scheme class
*
*/
class Uni_Cpo_Setting_Cpo_Sc_Scheme extends Uni_Cpo_Setting implements  Uni_Cpo_Setting_Interface 
{
    /**
     * Init
     *
     */
    public function __construct()
    {
        $this->setting_key = 'cpo_sc_scheme';
        $this->setting_data = array(
            'title'      => __( 'Suboptions conditional rules', 'uni-cpo' ),
            'is_tooltip' => true,
            'desc_tip'   => __( 'Conditionally displaying suboptions', 'uni-cpo' ),
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
	        {{ const itemFromFilter = Builderius._queryBuilderFilter.filter(function(item) { return item.id === 'uni_cpo_'+Builderius._option_slug; }).shift(); }}
            <div class="uni-modal-row uni-clear<?php 
        echo  ' uni-premium-content' ;
        ?>">
                <div class="uni-select-option-repeat">
                    <div class="uni-select-option-repeat-wrapper">
                        <div class="uni-select-option-add-wrapper uni-clear">
							<?php 
        echo  $this->generate_field_label_html() ;
        ?>
                        </div>

                        <div class="uni-form-row">
                            <div class="uni-formula-conditional-rules-repeat">
                                <div class="uni-formula-conditional-rules-repeat-wrapper">
                                    <div class="uni-formula-conditional-rules-btn-wrap uni-clear">
                                        <span class="uni_formula_conditional_rule_add"><?php 
        esc_html_e( 'Add Rule', 'uni-cpo' );
        ?></span>
                                        <span class="uni-rules-remove-all"><?php 
        esc_html_e( 'Remove All', 'uni-cpo' );
        ?></span>
                                    </div>
                                    <div class="uni-formula-conditional-rules-options-wrapper">

                                        <div class="uni-formula-conditional-rules-options-template uni-formula-conditional-rules-options-row uni-clear">
                                            <div class="uni-formula-conditional-rules-move-wrapper">
                                                <span class="uni_formula_conditional_rule_move">
                                                    <i class="fas fa-arrows-alt"></i>
                                                </span>
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-wrapper">
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <div class="uni-query-builder-wrapper">
                                                        <div id="cpo-formula-rule-builder-<%row-count%>"
                                                             class="cpo-query-rule-builder query-builder-rcr"></div>
                                                        <input class="js-uni-fetch-scheme uni-cpo-settings-btn uni-cpo-settings-saved"
                                                               data-id="<%row-count%>" type="button"
                                                               value="<?php 
        esc_attr_e( 'Fetch the rule', 'uni-cpo' );
        ?>"/>
                                                    </div>
                                                    <input id="uni_cpo_formula_rule_scheme-<%row-count%>" type="hidden"
                                                           name="<?php 
        echo  $this->setting_key ;
        ?>[<%row-count%>][rule]" value=""
                                                           class="js-sort-formula_scheme-rule"/>
                                                </div>
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
	                                                {{ if (typeof itemFromFilter !== 'undefined' && !_.isEmpty(itemFromFilter.values)) { }}
	                                                    {{ _.each(itemFromFilter.values, function(v, k) { }}
	                                                        <label>{{- v}}
	                                                        <input name="<?php 
        echo  $this->setting_key ;
        ?>[<%row-count%>][options][]"
	                                                               type="checkbox"
	                                                               data-parsley-required="true"
	                                                               data-parsley-mincheck="1"
	                                                               data-parsley-trigger="change focusout submit"
	                                                               class="js-sort-formula_scheme-formula"
	                                                                value="{{- k}}"/>
	                                                        </label>
	                                                    {{ }); }}
	                                                {{ } else { }}
	                                                <p><?php 
        esc_html_e( 'It seems this option is not saved to DB/does not have slug/suboptions yet' );
        ?></p>
	                                                {{ } }}
                                                </div>
                                            </div>
                                            <div class="uni-formula-conditional-rules-remove-wrapper">
                                                <span class="uni_formula_conditional_rule_remove"><i
                                                            class="fas fa-times"></i></span>
                                            </div>
                                        </div>
	                                    <?php 
        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </script>
		<?php 
    }

}