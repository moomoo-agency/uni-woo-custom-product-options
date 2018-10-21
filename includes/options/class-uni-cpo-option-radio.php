<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/*
*   Uni_Cpo_Option_Radio class
*
*/
class Uni_Cpo_Option_Radio extends Uni_Cpo_Option implements  Uni_Cpo_Option_Interface 
{
    /**
     * Stores extra (specific to this) option data.
     *
     * @var array
     */
    protected  $extra_data = array(
        'cpo_suboptions' => array(),
    ) ;
    /**
     * Constructor gets the post object and sets the ID for the loaded option.
     *
     */
    public function __construct( $option = 0 )
    {
        parent::__construct( $option );
    }
    
    public static function get_type()
    {
        return 'radio';
    }
    
    public static function get_title()
    {
        return __( 'Radio Inputs', 'uni-cpo' );
    }
    
    /**
     * Returns an array of special vars associated with the option
     *
     * @return array
     */
    public static function get_special_vars()
    {
        return array();
    }
    
    /**
     * Returns an array of data used in js query builder
     *
     * @return array
     */
    public static function get_filter_data()
    {
        $operators = array(
            'equal',
            'not_equal',
            'is_empty',
            'is_not_empty'
        );
        return array(
            'input'        => 'select',
            'operators'    => $operators,
            'special_vars' => array(),
        );
    }
    
    /*
    |--------------------------------------------------------------------------
    | Getters
    |--------------------------------------------------------------------------
    */
    /**
     * Get suboptions
     *
     * @param  string $context
     *
     * @return string
     */
    public function get_cpo_suboptions( $context = 'view' )
    {
        return $this->get_prop( 'cpo_suboptions', $context );
    }
    
    /*
    |--------------------------------------------------------------------------
    | Setters
    |--------------------------------------------------------------------------
    */
    /**
     * Set suboptions.
     *
     * @param string $options
     */
    public function set_cpo_suboptions( $options )
    {
        $this->set_prop( 'cpo_suboptions', $options );
    }
    
    /*
    |--------------------------------------------------------------------------
    | Other Actions
    |--------------------------------------------------------------------------
    */
    public function formatted_model_data()
    {
        $model['pid'] = $this->get_id();
        $model['settings']['general'] = $this->get_general();
        $model['settings']['general']['status'] = array(
            'sync' => array(
            'type' => 'none',
            'pid'  => 0,
        ),
        );
        $model['settings']['general'] = array_reverse( $model['settings']['general'] );
        $model['settings']['style'] = $this->get_style();
        $model['settings']['advanced'] = $this->get_advanced();
        $model['settings']['cpo_general'] = $this->get_cpo_general();
        $model['settings']['cpo_general']['main']['cpo_slug'] = $this->get_slug_ending();
        $model['settings']['cpo_suboptions'] = $this->get_cpo_suboptions();
        $model['settings']['cpo_conditional'] = $this->get_cpo_conditional();
        $model['settings']['cpo_validation'] = $this->get_cpo_validation();
        return stripslashes_deep( $model );
    }
    
    public function get_edit_field( $data, $value, $context = 'cart' )
    {
        $id = $data['id'];
        $type = $data['type'];
        $cpo_general_main = $data['settings']['cpo_general']['main'];
        $cpo_general_advanced = $data['settings']['cpo_general']['advanced'];
        $cpo_validation_main = ( isset( $data['settings']['cpo_validation']['main'] ) ? $data['settings']['cpo_validation']['main'] : array() );
        $cpo_validation_logic = ( isset( $data['settings']['cpo_validation']['logic'] ) ? $data['settings']['cpo_validation']['logic'] : array() );
        $is_cart_edit = ( isset( $cpo_general_advanced['cpo_enable_cartedit'] ) && 'yes' === $cpo_general_advanced['cpo_enable_cartedit'] ? true : false );
        $suboptions = ( isset( $data['settings']['cpo_suboptions']['data']['cpo_radio_options'] ) ? $data['settings']['cpo_suboptions']['data']['cpo_radio_options'] : array() );
        $attributes = array(
            'data-parsley-trigger' => 'change focusout submit',
        );
        $is_required = ( 'yes' === $cpo_general_main['cpo_is_required'] ? true : false );
        $slug = $this->get_slug();
        $input_css_class[] = $slug . '-field';
        $input_css_class[] = 'cpo-cart-item-option';
        $input_css_class[] = 'js-uni-cpo-field-' . $type;
        if ( 'order' === $context ) {
            $input_css_class[] = 'uni-admin-order-item-option-select';
        }
        if ( $is_required && 'cart' === $context ) {
            $attributes['data-parsley-required'] = 'true';
        }
        if ( !empty($cpo_validation_main) && isset( $cpo_validation_main['cpo_validation_msg'] ) && is_array( $cpo_validation_main['cpo_validation_msg'] ) ) {
            foreach ( $cpo_validation_main['cpo_validation_msg'] as $k => $v ) {
                if ( empty($v) ) {
                    continue;
                }
                switch ( $k ) {
                    case 'req':
                        $attributes['data-parsley-required-message'] = $v;
                        break;
                    case 'custom':
                        $extra_validation_msgs = preg_split( '/\\R/', $v );
                        $attributes = uni_cpo_field_attributes_modifier( $extra_validation_msgs, $attributes );
                        break;
                    default:
                        break;
                }
            }
        }
        
        if ( !empty($cpo_validation_logic['cpo_vc_extra']) ) {
            $extra_validation = preg_split( '/\\R/', $cpo_validation_logic['cpo_vc_extra'] );
            $attributes = uni_cpo_field_attributes_modifier( $extra_validation, $attributes );
        }
        
        ob_start();
        ?>
        <div class="cpo-cart-item-option-wrapper uni-node-<?php 
        echo  esc_attr( $id ) ;
        ?> <?php 
        if ( 'order' === $context ) {
            echo  esc_attr( "uni-admin-order-item-option-wrapper" ) ;
        }
        ?>">
            <label><?php 
        esc_html_e( uni_cpo_sanitize_label( $this->cpo_order_label() ) );
        ?></label>
			<?php 
        
        if ( 'order' === $context || 'cart' === $context && $is_cart_edit ) {
            ?>
                <select
                        class="<?php 
            echo  implode( ' ', array_map( function ( $el ) {
                return esc_attr( $el );
            }, $input_css_class ) ) ;
            ?>"
                        name="<?php 
            echo  esc_attr( $slug ) ;
            ?>"
					<?php 
            echo  self::get_custom_attribute_html( $attributes ) ;
            ?>>
                    <option value=""><?php 
            esc_html_e( 'Please select...', 'uni-cpo' );
            ?></option>
					<?php 
            foreach ( $suboptions as $suboption ) {
                if ( isset( $suboption['excl'] ) && !empty($suboption['excl']) ) {
                    continue;
                }
                
                if ( !empty($value) && $suboption['slug'] === $value ) {
                    $selected = 'selected="selected"';
                } elseif ( $suboption['def'] === 'checked' ) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                
                ?>
                        <option
                                value="<?php 
                echo  esc_attr( $suboption['slug'] ) ;
                ?>"
							<?php 
                echo  $selected ;
                ?>><?php 
                esc_html_e( $suboption['label'] );
                ?></option>
					<?php 
            }
            ?>
                </select>
			<?php 
        } else {
            ?>
                <select
                        class="<?php 
            echo  implode( ' ', array_map( function ( $el ) {
                return esc_attr( $el );
            }, $input_css_class ) ) ;
            ?>"
                        name="<?php 
            echo  esc_attr( $slug ) ;
            ?>"
                        disabled>
                    <option value=""><?php 
            esc_html_e( 'Please select...', 'uni-cpo' );
            ?></option>
					<?php 
            foreach ( $suboptions as $suboption ) {
                if ( isset( $suboption['excl'] ) && !empty($suboption['excl']) ) {
                    continue;
                }
                
                if ( !empty($value) && $suboption['slug'] === $value ) {
                    $selected = 'selected="selected"';
                } elseif ( $suboption['def'] === 'checked' ) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                
                ?>
                        <option
                                value="<?php 
                echo  esc_attr( $suboption['slug'] ) ;
                ?>"
							<?php 
                echo  $selected ;
                ?>><?php 
                esc_html_e( $suboption['label'] );
                ?></option>
					<?php 
            }
            ?>
                </select>
                <input
                        class="cpo-cart-item-option"
                        name="<?php 
            echo  esc_attr( $slug ) ;
            ?>"
                        value="<?php 
            echo  esc_attr( $value ) ;
            ?>"
                        type="hidden" />
			<?php 
        }
        
        ?>
        </div>
		<?php 
        return ob_get_clean();
    }
    
    public static function get_settings()
    {
        return array(
            'settings' => array(
            'general'         => array(
            'status' => array(
            'sync' => array(
            'type' => 'none',
            'pid'  => 0,
        ),
        ),
            'main'   => array(
            'width_px'  => 42,
            'height_px' => '',
        ),
        ),
            'style'           => array(
            'label'          => array(
            'color'            => '',
            'text_align_label' => '',
            'font_family'      => 'inherit',
            'font_weight'      => '',
            'font_size_label'  => array(
            'value' => '',
            'unit'  => 'px',
        ),
        ),
            'description'    => array(
            'color'          => '',
            'font_weight'    => '',
            'font_size_desc' => array(
            'value' => '',
            'unit'  => 'px',
        ),
        ),
            'font'           => array(
            'color'          => '#333333',
            'color_hover'    => '',
            'color_active'   => '',
            'text_align'     => 'center',
            'font_family'    => 'inherit',
            'font_style'     => 'inherit',
            'font_weight'    => '400',
            'font_size'      => array(
            'value' => '14',
            'unit'  => 'px',
        ),
            'letter_spacing' => '',
        ),
            'background'     => array(
            'color'        => '',
            'color_hover'  => '',
            'color_active' => '',
        ),
            'border'         => array(
            'color'        => '#d7d7d7',
            'color_hover'  => '#333333',
            'color_active' => '#333333',
            'width_px'     => 2,
            'offset_px'    => 2,
            'gap_px'       => '',
        ),
            'text_mode_item' => array(
            'padding' => array(
            'top'    => '',
            'right'  => 15,
            'bottom' => '',
            'left'   => 15,
            'unit'   => 'px',
        ),
        ),
        ),
            'advanced'        => array(
            'layout'    => array(
            'margin' => array(
            'top'    => '',
            'right'  => '',
            'bottom' => '',
            'left'   => '',
            'unit'   => 'px',
        ),
        ),
            'selectors' => array(
            'id_name'    => '',
            'class_name' => '',
        ),
        ),
            'cpo_general'     => array(
            'main'     => array(
            'cpo_slug'             => '',
            'cpo_is_required'      => 'no',
            'cpo_mode_radio'       => 'classic',
            'cpo_geom_radio'       => 'circle',
            'cpo_is_changeimage'   => 'no',
            'cpo_is_imagify'       => 'no',
            'cpo_is_resetbutton'   => 'no',
            'cpo_resetbutton_text' => 'Clear option',
            'cpo_encoded_image'    => '',
        ),
            'advanced' => array(
            'cpo_label'            => '',
            'cpo_label_tag'        => 'label',
            'cpo_order_label'      => '',
            'cpo_is_tooltip'       => 'no',
            'cpo_tooltip_type'     => 'classic',
            'cpo_tooltip'          => '',
            'cpo_tooltip_image'    => array(
            'url' => '',
            'id'  => 0,
            'alt' => '',
        ),
            'cpo_tooltip_class'    => '',
            'cpo_enable_cartedit'  => 'no',
            'cpo_order_visibility' => 'no',
        ),
        ),
            'cpo_suboptions'  => array(
            'data' => array(
            'cpo_radio_options' => array(),
        ),
        ),
            'cpo_conditional' => array(
            'main' => array(
            'cpo_is_fc'      => 'no',
            'cpo_fc_default' => 'hide',
            'cpo_fc_scheme'  => '',
        ),
        ),
            'cpo_validation'  => array(
            'main'  => array(
            'cpo_validation_msg' => array(
            'req'    => '',
            'custom' => '',
        ),
        ),
            'logic' => array(
            'cpo_vc_extra' => '',
        ),
        ),
        ),
        );
    }
    
    public static function js_template()
    {
        ?>
        <script id="js-builderius-module-<?php 
        echo  self::get_type() ;
        ?>-tmpl" type="text/template">
        	{{ const { id, type } = data; }}
            {{ const { general, style, advanced, cpo_general, cpo_suboptions, cpo_conditional } = data.settings; }}
            {{ const { id_name, class_name } = advanced.selectors; }}

            {{ const width_px = uniGet(general, 'main.width_px', '42'); }}
            {{ const height_px = uniGet(general, 'main.height_px', ''); }}

            {{ const color_label = uniGet( style, 'label.color', '' ); }}
            {{ const text_align_label = uniGet( style, 'label.text_align_label', 'inherit' ); }}
            {{ const font_family_label = uniGet( style, 'label.font_family', '' ); }}
            {{ const font_weight_label = uniGet( style, 'label.font_weight', '' ); }}
            {{ const font_size_label = uniGet( style, 'label.font_size_label', {value:'',unit:'px'} ); }}

            {{ const color_desc = uniGet( style, 'description.color', '' ); }}
            {{ const font_weight_desc = uniGet( style, 'description.font_weight', '' ); }}
            {{ const font_size_desc = uniGet( style, 'description.font_size_desc', {value:'',unit:'px'} ); }}

            {{ const color = uniGet(style, 'font.color', '#333333'); }}
            {{ const color_hover = uniGet(style, 'font.color_hover', ''); }}
            {{ const color_active = uniGet(style, 'font.color_active', ''); }}
            {{ const text_align = uniGet(style, 'font.text_align', ''); }}
            {{ const font_family = uniGet(style, 'font.font_family', ''); }}
            {{ const font_style = uniGet(style, 'font.font_style', ''); }}
            {{ const font_weight = uniGet(style, 'font.font_weight', ''); }}
            {{ const font_size = uniGet(style, 'font.font_size', ''); }}
            {{ const letter_spacing = uniGet(style, 'font.letter_spacing', ''); }}

			{{ const bg_color = uniGet(style, 'background.color', ''); }}
			{{ const bg_color_hover = uniGet(style, 'background.color_hover', ''); }}
			{{ const bg_color_active = uniGet(style, 'background.color_active', ''); }}

            {{ const border_width = uniGet(style, 'border.width_px', '2'); }}
            {{ const border_color = uniGet(style, 'border.color', '#d7d7d7'); }}
            {{ const border_color_active = uniGet(style, 'border.color_active', '#333333'); }}
            {{ const border_color_hover = uniGet(style, 'border.color_hover', '#333333'); }}
            {{ const offset_px = uniGet(style, 'border.offset_px', '2'); }}
            {{ const gap_px = uniGet(style, 'border.gap_px', ''); }}

            {{ const padding = uniGet( style, 'text_mode_item.padding', {top:'',right:15,bottom:'',left:15,unit:'px'} ); }}
            {{ const margin = uniGet( advanced, 'layout.margin', {top:'',right:'',bottom:'',left:'',unit:'px'} ); }}

            {{ const { cpo_slug, cpo_is_required } = cpo_general.main; }}

            {{ const cpo_mode_radio = uniGet(cpo_general.main, 'cpo_mode_radio', 'classic'); }}
            {{ const cpo_geom_radio = uniGet(cpo_general.main, 'cpo_geom_radio', 'circle'); }}

            {{ let radioOptions = []; }}
            {{ if (typeof cpo_suboptions.data !== 'undefined') { }}
            {{ radioOptions = Object.values(uniGet(cpo_suboptions.data, 'cpo_radio_options', [])); }}
            {{ } }}

            {{ let emptyModule = ''; }}
            {{ if ( radioOptions.length === 0 ) { }}
            {{ emptyModule = 'uni-module-empty'; }}
            {{ } }}

            {{ const { cpo_label_tag, cpo_label, cpo_is_tooltip, cpo_tooltip } = cpo_general.advanced; }}
			{{ const cpo_tooltip_type = uniGet( cpo_general, 'advanced.cpo_tooltip_type', 'classic' ); }}
			{{ const cpo_tooltip_image = uniGet( cpo_general, 'advanced.cpo_tooltip_image', {url:''} ); }}
			{{ const cpo_tooltip_class = uniGet( cpo_general, 'advanced.cpo_tooltip_class', '' ); }}

            {{ const cpo_is_resetbutton = uniGet(cpo_general.main, 'cpo_is_resetbutton', 'no'); }}
            {{ const cpo_resetbutton_text = uniGet(cpo_general.main, 'cpo_resetbutton_text', 'Clear option'); }}

            {{ const correct_width = Number(width_px) + Number(border_width) * 2 + Number(offset_px) * 2; }}

            <div
                    id="{{- id_name }}"
                    class="uni-module uni-module-{{- type }} uni-module-{{- type }}-{{- cpo_mode_radio }}-mode uni-node-{{- id }} {{- class_name }} {{- emptyModule }}"
                    data-node="{{- id }}"
                    data-type="{{- type }}">
            <style>
            	.uni-node-{{= id }} {
            		{{ if ( margin.top ) { }} margin-top: {{= margin.top + margin.unit }}; {{ } }}
                    {{ if ( margin.bottom ) { }} margin-bottom: {{= margin.bottom + margin.unit }}; {{ } }}
                    {{ if ( margin.left ) { }} margin-left: {{= margin.left + margin.unit }}; {{ } }}
                    {{ if ( margin.right ) { }} margin-right: {{= margin.right + margin.unit }}; {{ } }}
                }
                {{ if ( cpo_label_tag && cpo_label !== '' ) { }}
                    .uni-node-{{= id }} .uni-cpo-module-{{- type }}-label {
                        {{ if ( color_label !== '' ) { }} color: {{= color_label }}!important; {{ } }}
                        {{ if ( text_align_label !== '' ) { }} text-align: {{= text_align_label }}!important; display: block; {{ } }}
                        {{ if ( font_family_label !== 'inherit' ) { }} font-family: {{= font_family_label }}!important; {{ } }}
                        {{ if ( font_size_label.value !== '' ) { }} font-size: {{= font_size_label.value+font_size_label.unit }}!important; {{ } }}
                        {{ if ( font_weight_label !== '' ) { }} font-weight: {{= font_weight_label }}!important; {{ } }}
                    }
                {{ } }}
            	{{ if ( cpo_mode_radio === 'classic' ) { }}
	                .uni-node-{{= id }} .uni-cpo-option-label__text {
	                    {{ if ( color ) { }} color: {{= color }}; {{ } }}
	                    {{ if ( font_family !== 'inherit' ) { }} font-family: {{= font_family }}; {{ } }}
	                    {{ if ( font_style !== 'inherit' ) { }} font-style: {{= font_style }}; {{ } }}
	                    {{ if ( font_size.value ) { }} font-size: {{= font_size.value+font_size.unit }}; {{ } }}
	                    {{ if ( font_weight ) { }} font-weight: {{= font_weight }}; {{ } }}
	                    {{ if ( letter_spacing ) { }} letter-spacing: {{= letter_spacing+'em' }}; {{ } }}
	                }
	            {{ } else if ( cpo_mode_radio === 'colour' ) { }}
	            	.uni-node-{{= id }} .uni-cpo-radio-option-label {
                        {{ if ( width_px ) { }} width: {{= correct_width }}px; {{ } }}
	            		{{ if ( gap_px ) { }} margin-right: {{= gap_px }}px; margin-bottom: {{= gap_px }}px; {{ } }}
	            	}
	            	.uni-node-{{= id }} .uni-cpo-option-label__colour-wrap {
		            	{{ if ( offset_px ) { }} padding: {{= offset_px }}px; {{ } }}
                        {{ if ( border_width ) { }} border-width: {{= border_width }}px!important; border-style: solid; {{ } else { }} border-width: 0; border-style: solid; {{ } }}
		            	{{ if ( cpo_geom_radio === 'circle' ) { }} border-radius: 100%; {{ } else { }} border-radius: 0%; {{ } }}
		            }
		            {{ if (radioOptions) { }}
	            		{{ _.each(radioOptions, function(option) { }}
            				.uni-node-{{= id }} input:checked + label.uni-node-{{= id }}-cpo-option-label-{{- option.slug }} .uni-cpo-option-label__colour-wrap {
            					border-color: {{- option.suboption_colour }}!important;
            				}
	            			.uni-node-{{= id }} label.uni-node-{{= id }}-cpo-option-label-{{- option.slug }} .uni-cpo-option-label__colour-wrap .uni-cpo-option-label__colour {
            					background-color: {{- option.suboption_colour }};
            				}
	            		{{ }); }}
            		{{ } }}
		            .uni-node-{{= id }} .uni-cpo-option-label__colour-wrap .uni-cpo-option-label__colour {
	            		{{ if ( width_px ) { }} width: {{= width_px }}px;height: {{= width_px }}px; {{ } }}
	            		{{ if ( cpo_geom_radio === 'circle' ) { }} border-radius: 100%; {{ } else { }} border-radius: 0%; {{ } }}
	            	}
	            {{ } else if ( cpo_mode_radio === 'image' ) { }}
	            	.uni-node-{{= id }} .uni-cpo-radio-option-label {
                        {{ if ( width_px ) { }} width: {{= correct_width }}px; {{ } }}
	            		{{ if ( gap_px ) { }} margin-right: {{= gap_px }}px; margin-bottom: {{= gap_px }}px; {{ } }}
	            	}
	            	.uni-node-{{= id }} input:checked + label .uni-cpo-option-label__image-wrap {
	            		{{ if ( border_color_active ) { }} border-color: {{= border_color_active }}!important; {{ } }}
	            	}
	            	.uni-node-{{= id }} .uni-cpo-option-label__image-wrap {
	            		{{ if ( offset_px ) { }} padding: {{= offset_px }}px; {{ } }}
                        {{ if ( border_width ) { }} border-width: {{= border_width }}px!important; border-style: solid; {{ } else { }} border-width: 0; border-style: solid; {{ } }}
	            		{{ if ( border_color ) { }} border-color: {{= border_color }}!important; {{ } }}
		            	{{ if ( cpo_geom_radio === 'circle' ) { }} border-radius: 100%; {{ } else { }} border-radius: 0%; {{ } }}
		            }
	            	.uni-node-{{= id }} .uni-cpo-option-label__image-wrap img {
	            		{{ if ( width_px ) { }} width: {{= width_px }}px; {{ } }}
						{{ if ( height_px && !width_px  ) { }} width:auto; height: {{= height_px }}px; {{ } }}
	            		{{ if ( cpo_geom_radio === 'circle' ) { }} border-radius: 100%; {{ } else { }} border-radius: 0%; {{ } }}
	            	}
	            {{ } else if ( cpo_mode_radio === 'text' ) { }}
	            	.uni-node-{{= id }} .uni-cpo-radio-option-label {
	            		{{ if ( gap_px ) { }} margin-right: {{= gap_px }}px; margin-bottom: {{= gap_px }}px; {{ } }}
	            	}
	            	.uni-node-{{= id }} input:checked + label .uni-cpo-option-label__text-content {
	            		{{ if ( border_color_active ) { }} border-color: {{= border_color_active }}!important; {{ } }}
	            		{{ if ( bg_color_active ) { }} background-color: {{= bg_color_active }}!important; {{ } }}
	            		{{ if ( color_active ) { }} color: {{= color_active }}!important; {{ } }}
	            	}
	            	.uni-node-{{= id }} label:hover .uni-cpo-option-label__text-content {
	            		{{ if ( color_hover ) { }} color: {{= color_hover }}; {{ } }}
	            		{{ if ( border_color_hover ) { }} border-color: {{= border_color_hover }}!important; {{ } }}
	            		{{ if ( bg_color_hover ) { }} background-color: {{= bg_color_hover }}!important; {{ } }}
	            	}
	            	.uni-node-{{= id }} .uni-cpo-option-label__text-content {
		            	{{ if ( color ) { }} color: {{= color }}; {{ } }}
		            	{{ if ( text_align ) { }} text-align: {{= text_align }}; {{ } }}
	                    {{ if ( font_family !== 'inherit' ) { }} font-family: {{= font_family }}; {{ } }}
	                    {{ if ( font_style !== 'inherit' ) { }} font-style: {{= font_style }}; {{ } }}
	                    {{ if ( font_size.value ) { }} font-size: {{= font_size.value+font_size.unit }}; {{ } }}
	                    {{ if ( font_weight ) { }} font-weight: {{= font_weight }}; {{ } }}
	                    {{ if ( letter_spacing ) { }} letter-spacing: {{= letter_spacing+'em' }}; {{ } }}
	                    {{ if ( width_px ) { }} width: {{= width_px }}px; {{ } }}
	                    {{ if ( height_px ) { }} height: {{= height_px }}px;line-height: {{= height_px }}px; {{ } else if ( width_px ) { }} height: {{= width_px }}px;line-height: {{= width_px }}px; {{ } }}
                        {{ if ( border_width ) { }} border-width: {{= border_width }}px!important; border-style: solid; {{ } else { }} border-width: 0; border-style: solid; {{ } }}
	            		{{ if ( border_color ) { }} border-color: {{= border_color }}!important; {{ } }}
	            		{{ if ( bg_color ) { }} background-color: {{= bg_color }}!important; {{ } }}
	            		{{ if ( cpo_geom_radio === 'circle' ) { }} border-radius: 100%; {{ } else { }} border-radius: 0%; {{ } }}
	            		{{ if ( padding.top ) { }} padding-top: {{= padding.top + padding.unit }}; {{ } }}
                        {{ if ( padding.bottom ) { }} padding-bottom: {{= padding.bottom + padding.unit }}; {{ } }}
                        {{ if ( padding.left ) { }} padding-left: {{= padding.left + padding.unit }}; {{ } }}
                        {{ if ( padding.right ) { }} padding-right: {{= padding.right + padding.unit }}; {{ } }}
	                }
	            {{ } }}
                .uni-node-{{= id }} .uni-cpo-option-label__description {
                    {{ if ( color_desc !== '' ) { }} color: {{= color_desc }}!important; {{ } }}
                    {{ if ( font_size_desc.value !== '' ) { }} font-size: {{= font_size_desc.value+font_size_desc.unit }}!important; {{ } }}
                    {{ if ( font_weight_desc !== '' ) { }} font-weight: {{= font_weight_desc }}!important; {{ } }}
                }
            </style>
            {{ if ( cpo_label_tag && cpo_label ) { }}
                <{{- cpo_label_tag }} class="uni-cpo-module-{{- type }}-label {{ if ( cpo_is_required === 'yes' ) { }} uni_cpo_field_required {{ } }}">
                	{{- cpo_label }}
					{{ if ( cpo_is_tooltip === 'yes' && cpo_tooltip !== '' && cpo_tooltip_type === 'classic' ) { }} <span class="uni-cpo-tooltip" data-tip="{{- cpo_tooltip }}"></span> {{ } else if ( cpo_is_tooltip === 'yes' && cpo_tooltip_image.url !== '' && cpo_tooltip_type === 'lightbox' ) { }} <span class="uni-cpo-tooltip"></span> {{ } else if ( cpo_is_tooltip === 'yes' && cpo_tooltip_class !== '' && cpo_tooltip_type === 'popup' ) { }} <span class="uni-cpo-tooltip"></span> {{ } }}
            	</{{- cpo_label_tag }}>
        	{{ } }}

            {{ if ( radioOptions ) { }}
                {{ for (let i = 0; i < radioOptions.length; i++) { }}
                    {{ const option = radioOptions[i]; }}
                    {{ if (typeof option.excl !== 'undefined' && option.excl.length > 0) { continue; } }}
	                <input
	                    class="uni_cpo_{{- cpo_slug }}-field js-uni-cpo-field-{{- type }}"
	                    {{ if ( option.def === 'checked' ) { }} checked="checked" {{ } }}
	                    id="uni_cpo_{{- cpo_slug }}-field-{{- option.slug }}"
	                    name="{{- cpo_slug }}"
	                    type="radio"
	                    value="{{- option.slug }}"/>
	                <label for="uni_cpo_{{- cpo_slug }}-field-{{- option.slug }}" class="uni-cpo-option-label uni-cpo-radio-option-label uni-node-{{= id }}-cpo-option-label-{{- option.slug }} {{- option.suboption_class }}">
	                	{{ if ( cpo_mode_radio === 'classic' ) { }}
		                    <span class="uni-cpo-option-label__radio"></span>
		                    <span class="uni-cpo-option-label__text">
		                        {{- option.label }}
                                <span class="uni-cpo-option-label__description">
                                    {{- option.suboption_text }}
                                </span>
		                    </span>
                        <?php 
        ?>
		                {{ } }}
	                </label>
                {{ } }}
            {{ } }}
            {{ if (cpo_is_resetbutton === 'yes') { }}
                <div class = "uni-cpo-radio-resetbutton uni-clear">
                    <button class = "uni_cpo_{{- cpo_slug }}-field-reset-button js-uni-cpo-field-{{- type }}-reset-button">{{- cpo_resetbutton_text }}</button>
                </div>
            {{ } }}
            </div>
        </script>
		<?php 
    }
    
    public static function template( $data, $post_data = array() )
    {
        $id = $data['id'];
        $pid = ( !empty($data['pid']) ? absint( $data['pid'] ) : 0 );
        $type = $data['type'];
        $selectors = $data['settings']['advanced']['selectors'];
        $suboptions = ( isset( $data['settings']['cpo_suboptions']['data']['cpo_radio_options'] ) ? $data['settings']['cpo_suboptions']['data']['cpo_radio_options'] : array() );
        $cpo_general_main = $data['settings']['cpo_general']['main'];
        $cpo_mode_radio = ( isset( $cpo_general_main['cpo_mode_radio'] ) ? $cpo_general_main['cpo_mode_radio'] : 'classic' );
        $cpo_change_image = ( isset( $cpo_general_main['cpo_is_changeimage'] ) ? $cpo_general_main['cpo_is_changeimage'] : 'no' );
        $cpo_is_imagify = ( isset( $cpo_general_main['cpo_is_imagify'] ) ? $cpo_general_main['cpo_is_imagify'] : 'no' );
        $cpo_encoded_image = ( isset( $cpo_general_main['cpo_encoded_image'] ) ? $cpo_general_main['cpo_encoded_image'] : '' );
        $cpo_general_advanced = $data['settings']['cpo_general']['advanced'];
        $cpo_validation_main = ( isset( $data['settings']['cpo_validation']['main'] ) ? $data['settings']['cpo_validation']['main'] : array() );
        $cpo_validation_logic = ( isset( $data['settings']['cpo_validation']['logic'] ) ? $data['settings']['cpo_validation']['logic'] : array() );
        $cpo_is_resetbutton = ( isset( $cpo_general_main['cpo_is_resetbutton'] ) ? ( 'yes' === $cpo_general_main['cpo_is_resetbutton'] ? true : false ) : false );
        $cpo_resetbutton_text = ( isset( $cpo_general_main['cpo_resetbutton_text'] ) ? $cpo_general_main['cpo_resetbutton_text'] : 'Clear option' );
        $cpo_label_tag = $cpo_general_advanced['cpo_label_tag'];
        $attributes = array(
            'data-parsley-trigger'          => 'change focusout submit',
            'data-parsley-errors-container' => '.uni-node-' . $id,
            'data-parsley-class-handler'    => '.uni-node-' . $id,
        );
        $wrapper_attributes = array();
        $option = false;
        $rules_data = $data['settings']['cpo_conditional']['main'];
        $is_required = ( 'yes' === $cpo_general_main['cpo_is_required'] ? true : false );
        $is_tooltip = ( 'yes' === $cpo_general_advanced['cpo_is_tooltip'] ? true : false );
        $is_enabled = ( 'yes' === $rules_data['cpo_is_fc'] ? true : false );
        $is_hidden = ( 'hide' === $rules_data['cpo_fc_default'] ? true : false );
        $plugin_settings = UniCpo()->get_settings();
        $image_size = $plugin_settings['product_image_size'];
        $cpo_tooltip_type = ( isset( $cpo_general_advanced['cpo_tooltip_type'] ) ? $cpo_general_advanced['cpo_tooltip_type'] : 'classic' );
        $cpo_tooltip_image = ( isset( $cpo_general_advanced['cpo_tooltip_image']['url'] ) ? $cpo_general_advanced['cpo_tooltip_image']['url'] : '' );
        if ( !empty($data['pid']) ) {
            $option = uni_cpo_get_option( $data['pid'] );
        }
        $slug = ( $pid && is_object( $option ) && 'trash' !== $option->get_status() ? $option->get_slug() : '' );
        $css_id[] = $slug;
        $css_class = array(
            'uni-module',
            'uni-module-' . $type,
            'uni-node-' . $id,
            'uni-module-' . $type . '-' . $cpo_mode_radio . '-mode'
        );
        $input_css_class[] = $slug . '-field';
        $input_css_class[] = 'js-uni-cpo-field';
        $input_css_class[] = 'js-uni-cpo-field-' . $type;
        if ( !empty($slug) && 'yes' === $cpo_change_image ) {
            $input_css_class[] = 'uni-cpo-image-changer';
        }
        if ( !empty($selectors['id_name']) ) {
            array_push( $css_id, $selectors['id_name'] );
        }
        if ( !empty($selectors['class_name']) ) {
            array_push( $css_class, $selectors['class_name'] );
        }
        if ( count( $suboptions ) === 0 ) {
            array_push( $css_class, 'uni-module-empty' );
        }
        if ( 'yes' === $cpo_general_main['cpo_is_required'] ) {
            $attributes['data-parsley-required'] = 'true';
        }
        if ( !empty($cpo_validation_main) && isset( $cpo_validation_main['cpo_validation_msg'] ) && is_array( $cpo_validation_main['cpo_validation_msg'] ) ) {
            foreach ( $cpo_validation_main['cpo_validation_msg'] as $k => $v ) {
                if ( empty($v) ) {
                    continue;
                }
                switch ( $k ) {
                    case 'req':
                        $attributes['data-parsley-required-message'] = $v;
                        break;
                    case 'custom':
                        $extra_validation_msgs = preg_split( '/\\R/', $v );
                        $attributes = uni_cpo_field_attributes_modifier( $extra_validation_msgs, $attributes );
                        break;
                    default:
                        break;
                }
            }
        }
        
        if ( !empty($cpo_validation_logic['cpo_vc_extra']) ) {
            $extra_validation = preg_split( '/\\R/', $cpo_validation_logic['cpo_vc_extra'] );
            $attributes = uni_cpo_field_attributes_modifier( $extra_validation, $attributes );
        }
        
        
        if ( $cpo_encoded_image ) {
            $wrapper_attributes['data-layered'] = true;
            $input_css_class[] = 'uni-cpo-colorify-imagify-changer';
        }
        
        
        if ( 'yes' === $cpo_is_imagify ) {
            $wrapper_attributes['data-imagify'] = true;
            $input_css_class[] = 'uni-cpo-colorify-imagify-changer';
        }
        
        
        if ( $is_enabled && $is_hidden ) {
            $wrapper_attributes['style'] = 'display:none;';
            $input_css_class[] = 'uni-cpo-excluded-field';
        }
        
        $default_value = ( !empty($post_data) && !empty($slug) && !empty($post_data[$slug]) ? $post_data[$slug] : '' );
        $suboption_classes_default = array( 'uni-cpo-option-label', 'uni-cpo-radio-option-label' );
        ?>
    <div
            id="<?php 
        echo  implode( ' ', array_map( function ( $el ) {
            return esc_attr( $el );
        }, $css_id ) ) ;
        ?>"
			<?php 
        
        if ( count( $suboptions ) === 0 ) {
            ?>
				data-tip="<?php 
            esc_attr_e( 'Please add suboptions to this option!', 'uni-cpo' );
            ?>"
			<?php 
        }
        
        ?>
            class="<?php 
        echo  implode( ' ', array_map( function ( $el ) {
            return esc_attr( $el );
        }, $css_class ) ) ;
        ?>"
		<?php 
        echo  self::get_custom_attribute_html( $wrapper_attributes ) ;
        ?>>
		<?php 
        
        if ( !empty($cpo_general_advanced['cpo_label']) ) {
            ?>
            <<?php 
            echo  esc_attr( $cpo_label_tag ) ;
            ?> class="uni-cpo-module-<?php 
            echo  esc_attr( $type ) ;
            ?>-label <?php 
            if ( $is_required ) {
                ?> uni_cpo_field_required <?php 
            }
            ?>">
			<?php 
            esc_html_e( $cpo_general_advanced['cpo_label'] );
            ?>
			<?php 
            
            if ( $is_tooltip && $cpo_general_advanced['cpo_tooltip'] !== '' && $cpo_tooltip_type === 'classic' ) {
                ?>
                <span class="uni-cpo-tooltip"
                      data-tip="<?php 
                echo  uni_cpo_sanitize_tooltip( $cpo_general_advanced['cpo_tooltip'] ) ;
                ?>"></span>
			<?php 
            } else {
                
                if ( $is_tooltip && $cpo_tooltip_image !== '' && $cpo_tooltip_type === 'lightbox' ) {
                    ?>
				<a href="<?php 
                    esc_html_e( $cpo_tooltip_image );
                    ?>" data-lity class="uni-cpo-tooltip"></a>
			<?php 
                } else {
                    
                    if ( $is_tooltip && $cpo_general_advanced['cpo_tooltip_class'] !== '' && $cpo_tooltip_type === 'popup' ) {
                        ?>
				<span class="uni-cpo-tooltip <?php 
                        esc_html_e( $cpo_general_advanced['cpo_tooltip_class'] );
                        ?>"></span>
			<?php 
                    }
                
                }
            
            }
            
            ?>
            </<?php 
            echo  esc_attr( $cpo_label_tag ) ;
            ?>>
		<?php 
        }
        
        ?>
		<?php 
        foreach ( $suboptions as $suboption ) {
            if ( isset( $suboption['excl'] ) && !empty($suboption['excl']) ) {
                continue;
            }
            $suboption_classes = $suboption_classes_default;
            $attributes_new = $attributes;
            $data_image = '';
            if ( isset( $suboption['suboption_class'] ) && !empty($suboption['suboption_class']) ) {
                array_push( $suboption_classes, $suboption['suboption_class'] );
            }
            array_push( $suboption_classes, 'uni-node-' . $id . '-cpo-option-label-' . $suboption['slug'] );
            
            if ( !empty($default_value) && $suboption['slug'] === $default_value ) {
                $attributes_new['checked'] = 'checked';
            } elseif ( $suboption['def'] === 'checked' ) {
                $attributes_new['checked'] = 'checked';
            }
            
            ?>
            <input
                    class="<?php 
            echo  implode( ' ', array_map( function ( $el ) {
                return esc_attr( $el );
            }, $input_css_class ) ) ;
            ?>"
                    id="<?php 
            echo  esc_attr( $slug ) ;
            ?>-field-<?php 
            echo  esc_attr( $suboption['slug'] ) ;
            ?>"
                    name="<?php 
            echo  esc_attr( $slug ) ;
            ?>"
                    type="radio"
                    value="<?php 
            echo  esc_attr( $suboption['slug'] ) ;
            ?>"
				    <?php 
            echo  self::get_custom_attribute_html( $attributes_new ) ;
            ?> />
            <label
                    for="<?php 
            echo  esc_attr( $slug ) ;
            ?>-field-<?php 
            echo  esc_attr( $suboption['slug'] ) ;
            ?>"
                    class="<?php 
            echo  implode( ' ', array_map( function ( $el ) {
                return esc_attr( $el );
            }, $suboption_classes ) ) ;
            ?>">
				<?php 
            switch ( $cpo_mode_radio ) {
                case 'classic':
                    ?>
					<span class="uni-cpo-option-label__radio"></span>
	                <span class="uni-cpo-option-label__text"
	                      data-image="<?php 
                    echo  esc_attr( $data_image ) ;
                    ?>">
                        <?php 
                    esc_html_e( $suboption['label'] );
                    ?>
                        <?php 
                    
                    if ( !empty($suboption['suboption_text']) ) {
                        ?>
                            <span class="uni-cpo-option-label__description">
                                <?php 
                        esc_html_e( $suboption['suboption_text'] );
                        ?>
                            </span>
                        <?php 
                    }
                    
                    ?>
                    </span>
				<?php 
                    break;
                case 'colour':
                    break;
                case 'image':
                    break;
                case 'text':
                    break;
            }
            ?>

            </label>
		<?php 
        }
        ?>
        <?php 
        
        if ( $cpo_is_resetbutton ) {
            ?>
            <div class = "uni-cpo-radio-resetbutton uni-clear">
                <button class = "uni_cpo_<?php 
            echo  esc_attr( $slug ) ;
            ?>-field-reset-button js-uni-cpo-field-<?php 
            echo  esc_attr( $type ) ;
            ?>-reset-button"><?php 
            esc_html_e( $cpo_resetbutton_text );
            ?></button>
            </div>
        <?php 
        }
        
        ?>


        </div>
		<?php 
        self::conditional_rules( $data );
    }
    
    public static function get_css( $data )
    {
        $id = $data['id'];
        $type = $data['type'];
        $suboptions = ( isset( $data['settings']['cpo_suboptions']['data']['cpo_radio_options'] ) ? $data['settings']['cpo_suboptions']['data']['cpo_radio_options'] : array() );
        $main = $data['settings']['general']['main'];
        $label = ( !empty($data['settings']['style']['label']) ? $data['settings']['style']['label'] : array() );
        $description = ( !empty($data['settings']['style']['description']) ? $data['settings']['style']['description'] : array() );
        $font = $data['settings']['style']['font'];
        $bg = ( isset( $data['settings']['style']['background'] ) ? $data['settings']['style']['background'] : array() );
        $border = $data['settings']['style']['border'];
        $margin = ( isset( $data['settings']['advanced']['layout'] ) && isset( $data['settings']['advanced']['layout']['margin'] ) ? $data['settings']['advanced']['layout']['margin'] : array() );
        $padding = ( isset( $data['settings']['style']['text_mode_item'] ) && isset( $data['settings']['style']['text_mode_item']['padding'] ) ? $data['settings']['style']['text_mode_item']['padding'] : array() );
        $cpo_general_main = $data['settings']['cpo_general']['main'];
        $cpo_mode_radio = $cpo_general_main['cpo_mode_radio'];
        $cpo_geom_radio = $cpo_general_main['cpo_geom_radio'];
        $cpo_general_advanced = $data['settings']['cpo_general']['advanced'];
        $correct_width = absint( $main['width_px'] ) + absint( $border['width_px'] ) * 2 + absint( $border['offset_px'] ) * 2;
        ob_start();
        ?>
			.uni-node-<?php 
        echo  esc_attr( $id ) ;
        ?> {
        		<?php 
        
        if ( !empty($margin['top']) ) {
            ?> margin-top: <?php 
            echo  esc_attr( "{$margin['top']}{$margin['unit']}" ) ;
            ?>; <?php 
        }
        
        ?>
				<?php 
        
        if ( !empty($margin['bottom']) ) {
            ?> margin-bottom: <?php 
            echo  esc_attr( "{$margin['bottom']}{$margin['unit']}" ) ;
            ?>; <?php 
        }
        
        ?>
				<?php 
        
        if ( !empty($margin['left']) ) {
            ?> margin-left: <?php 
            echo  esc_attr( "{$margin['left']}{$margin['unit']}" ) ;
            ?>; <?php 
        }
        
        ?>
				<?php 
        
        if ( !empty($margin['right']) ) {
            ?> margin-right: <?php 
            echo  esc_attr( "{$margin['right']}{$margin['unit']}" ) ;
            ?>; <?php 
        }
        
        ?>
        	}
            <?php 
        
        if ( !empty($cpo_general_advanced['cpo_label']) ) {
            ?>
                .uni-node-<?php 
            echo  esc_attr( $id ) ;
            ?> .uni-cpo-module-<?php 
            echo  esc_attr( $type ) ;
            ?>-label {
                <?php 
            
            if ( !empty($label['color']) ) {
                ?> color: <?php 
                echo  esc_attr( $label['color'] ) ;
                ?>!important;<?php 
            }
            
            ?>
                <?php 
            
            if ( !empty($label['text_align_label']) ) {
                ?> text-align: <?php 
                echo  esc_attr( $label['text_align_label'] ) ;
                ?>!important; display: block; <?php 
            }
            
            ?>
                <?php 
            
            if ( !empty($label['font_family']) && $label['font_family'] !== 'inherit' ) {
                ?> font-family: <?php 
                echo  esc_attr( $label['font_family'] ) ;
                ?>!important;<?php 
            }
            
            ?>
                <?php 
            
            if ( !empty($label['font_weight']) ) {
                ?> font-weight: <?php 
                echo  esc_attr( $label['font_weight'] ) ;
                ?>!important;<?php 
            }
            
            ?>
                <?php 
            
            if ( !empty($label['font_size_label']['value']) ) {
                ?> font-size: <?php 
                echo  esc_attr( "{$label['font_size_label']['value']}{$label['font_size_label']['unit']}" ) ;
                ?>!important; <?php 
            }
            
            ?>
                }
            <?php 
        }
        
        ?>
			<?php 
        
        if ( $cpo_mode_radio === 'classic' ) {
            ?>
				.uni-node-<?php 
            echo  esc_attr( $id ) ;
            ?> .uni-cpo-option-label__text {
					<?php 
            
            if ( !empty($font['color']) ) {
                ?> color: <?php 
                echo  esc_attr( $font['color'] ) ;
                ?>;<?php 
            }
            
            ?>
					<?php 
            
            if ( $font['font_family'] !== 'inherit' ) {
                ?> font-family: <?php 
                echo  esc_attr( $font['font_family'] ) ;
                ?>;<?php 
            }
            
            ?>
					<?php 
            
            if ( $font['font_style'] !== 'inherit' ) {
                ?> font-style: <?php 
                echo  esc_attr( $font['font_style'] ) ;
                ?>;<?php 
            }
            
            ?>
					<?php 
            
            if ( !empty($font['font_weight']) ) {
                ?> font-weight: <?php 
                echo  esc_attr( $font['font_weight'] ) ;
                ?>;<?php 
            }
            
            ?>
					<?php 
            
            if ( !empty($font['font_size']['value']) ) {
                ?> font-size: <?php 
                echo  esc_attr( "{$font['font_size']['value']}{$font['font_size']['unit']}" ) ;
                ?>; <?php 
            }
            
            ?>
					<?php 
            
            if ( !empty($font['letter_spacing']) ) {
                ?> letter-spacing: <?php 
                echo  esc_attr( $font['letter_spacing'] ) ;
                ?>em;<?php 
            }
            
            ?>
		        }
			<?php 
        } else {
            
            if ( $cpo_mode_radio === 'colour' ) {
                ?>
				.uni-node-<?php 
                echo  esc_attr( $id ) ;
                ?> .uni-cpo-radio-option-label {
                    <?php 
                
                if ( !empty($main['width_px']) ) {
                    ?> width: <?php 
                    echo  esc_attr( $correct_width ) ;
                    ?>px; <?php 
                }
                
                ?>
            		<?php 
                
                if ( !empty($border['gap_px']) ) {
                    ?> margin-right: <?php 
                    echo  esc_attr( $border['gap_px'] ) ;
                    ?>px; margin-bottom: <?php 
                    echo  esc_attr( $border['gap_px'] ) ;
                    ?>px; <?php 
                }
                
                ?>
            	}
            	.uni-node-<?php 
                echo  esc_attr( $id ) ;
                ?> .uni-cpo-option-label__colour-wrap {
            		<?php 
                
                if ( !empty($border['offset_px']) ) {
                    ?> padding: <?php 
                    echo  esc_attr( $border['offset_px'] ) ;
                    ?>px;<?php 
                }
                
                ?>
                    <?php 
                
                if ( !empty($border['width_px']) ) {
                    ?> border-width: <?php 
                    echo  esc_attr( $border['width_px'] ) ;
                    ?>px!important; border-style: solid; <?php 
                } else {
                    ?> border-width: 0!important; border-style: solid; <?php 
                }
                
                ?>
	            	<?php 
                
                if ( $cpo_geom_radio === 'circle' ) {
                    ?> border-radius: 100%; <?php 
                } else {
                    ?> border-radius: 0%; <?php 
                }
                
                ?>
	            }
	            <?php 
                foreach ( $suboptions as $suboption ) {
                    ?>
					.uni-node-<?php 
                    echo  esc_attr( $id ) ;
                    ?> input:checked + label.uni-node-<?php 
                    echo  esc_attr( $id ) ;
                    ?>-cpo-option-label-<?php 
                    echo  esc_attr( $suboption['slug'] ) ;
                    ?> .uni-cpo-option-label__colour-wrap {
    					border-color: <?php 
                    echo  esc_attr( $suboption['suboption_colour'] ) ;
                    ?>!important;
    				}
    				.uni-node-<?php 
                    echo  esc_attr( $id ) ;
                    ?> label.uni-node-<?php 
                    echo  esc_attr( $id ) ;
                    ?>-cpo-option-label-<?php 
                    echo  esc_attr( $suboption['slug'] ) ;
                    ?> .uni-cpo-option-label__colour-wrap .uni-cpo-option-label__colour {
    					background-color: <?php 
                    echo  esc_attr( $suboption['suboption_colour'] ) ;
                    ?>;
    				}
				<?php 
                }
                ?>
				.uni-node-<?php 
                echo  esc_attr( $id ) ;
                ?> .uni-cpo-option-label__colour-wrap .uni-cpo-option-label__colour {
            		<?php 
                
                if ( !empty($main['width_px']) ) {
                    ?> width: <?php 
                    echo  esc_attr( $main['width_px'] ) ;
                    ?>px;height: <?php 
                    echo  esc_attr( $main['width_px'] ) ;
                    ?>px; <?php 
                }
                
                ?>
            		<?php 
                
                if ( $cpo_geom_radio === 'circle' ) {
                    ?> border-radius: 100%; <?php 
                } else {
                    ?> border-radius: 0%; <?php 
                }
                
                ?>
            	}
			<?php 
            } else {
                
                if ( $cpo_mode_radio === 'image' ) {
                    ?>
				.uni-node-<?php 
                    echo  esc_attr( $id ) ;
                    ?> .uni-cpo-radio-option-label {
                    <?php 
                    
                    if ( !empty($main['width_px']) ) {
                        ?> width: <?php 
                        echo  esc_attr( $correct_width ) ;
                        ?>px; <?php 
                    }
                    
                    ?>
            		<?php 
                    
                    if ( !empty($border['gap_px']) ) {
                        ?> margin-right: <?php 
                        echo  esc_attr( $border['gap_px'] ) ;
                        ?>px; margin-bottom: <?php 
                        echo  esc_attr( $border['gap_px'] ) ;
                        ?>px;<?php 
                    }
                    
                    ?>
            	}
            	.uni-node-<?php 
                    echo  esc_attr( $id ) ;
                    ?> input:checked + label .uni-cpo-option-label__image-wrap {
            		<?php 
                    
                    if ( !empty($border['color_active']) ) {
                        ?> border-color: <?php 
                        echo  esc_attr( $border['color_active'] ) ;
                        ?>!important;<?php 
                    }
                    
                    ?>
            	}
            	.uni-node-<?php 
                    echo  esc_attr( $id ) ;
                    ?> label:hover .uni-cpo-option-label__image-wrap {
					<?php 
                    
                    if ( !empty($border['color_hover']) ) {
                        ?> border-color: <?php 
                        echo  esc_attr( $border['color_hover'] ) ;
                        ?>!important;<?php 
                    }
                    
                    ?>
            	}
            	.uni-node-<?php 
                    echo  esc_attr( $id ) ;
                    ?> .uni-cpo-option-label__image-wrap {
            		<?php 
                    
                    if ( !empty($border['offset_px']) ) {
                        ?> padding: <?php 
                        echo  esc_attr( $border['offset_px'] ) ;
                        ?>px;<?php 
                    }
                    
                    ?>
                    <?php 
                    
                    if ( !empty($border['width_px']) ) {
                        ?> border-width: <?php 
                        echo  esc_attr( $border['width_px'] ) ;
                        ?>px!important; border-style: solid; <?php 
                    } else {
                        ?> border-width: 0!important; border-style: solid; <?php 
                    }
                    
                    ?>
            		<?php 
                    
                    if ( !empty($border['color']) ) {
                        ?> border-color: <?php 
                        echo  esc_attr( $border['color'] ) ;
                        ?>!important;<?php 
                    }
                    
                    ?>
	            	<?php 
                    
                    if ( $cpo_geom_radio === 'circle' ) {
                        ?> border-radius: 100%; <?php 
                    } else {
                        ?> border-radius: 0%; <?php 
                    }
                    
                    ?>
	            }
            	.uni-node-<?php 
                    echo  esc_attr( $id ) ;
                    ?> .uni-cpo-option-label__image-wrap img {
            		<?php 
                    
                    if ( !empty($main['width_px']) ) {
                        ?> width: <?php 
                        echo  esc_attr( $main['width_px'] ) ;
                        ?>px;<?php 
                    }
                    
                    ?>
					<?php 
                    
                    if ( !empty($main['height_px']) && empty($main['width_px']) ) {
                        ?> width:auto;height: <?php 
                        echo  esc_attr( $main['height_px'] ) ;
                        ?>px;<?php 
                    }
                    
                    ?>
            		<?php 
                    
                    if ( $cpo_geom_radio === 'circle' ) {
                        ?> border-radius: 100%; <?php 
                    } else {
                        ?> border-radius: 0%; <?php 
                    }
                    
                    ?>
            	}
			<?php 
                } else {
                    
                    if ( $cpo_mode_radio === 'text' ) {
                        ?>
	        	.uni-node-<?php 
                        echo  esc_attr( $id ) ;
                        ?> .uni-cpo-radio-option-label {
            		<?php 
                        
                        if ( !empty($border['gap_px']) ) {
                            ?> margin-right: <?php 
                            echo  esc_attr( $border['gap_px'] ) ;
                            ?>px; margin-bottom: <?php 
                            echo  esc_attr( $border['gap_px'] ) ;
                            ?>px;<?php 
                        }
                        
                        ?>
            	}
            	.uni-node-<?php 
                        echo  esc_attr( $id ) ;
                        ?> input:checked + label .uni-cpo-option-label__text-content {
            		<?php 
                        
                        if ( !empty($border['color_active']) ) {
                            ?>border-color: <?php 
                            echo  esc_attr( $border['color_active'] ) ;
                            ?>!important;<?php 
                        }
                        
                        ?>
				    <?php 
                        
                        if ( !empty($bg['color_active']) ) {
                            ?>background-color: <?php 
                            echo  esc_attr( $bg['color_active'] ) ;
                            ?>!important;<?php 
                        }
                        
                        ?>
					<?php 
                        
                        if ( !empty($font['color_active']) ) {
                            ?>color: <?php 
                            echo  esc_attr( $font['color_active'] ) ;
                            ?>;<?php 
                        }
                        
                        ?>
            	}
            	.uni-node-<?php 
                        echo  esc_attr( $id ) ;
                        ?> label:hover .uni-cpo-option-label__text-content {
					<?php 
                        
                        if ( !empty($border['color_hover']) ) {
                            ?>border-color: <?php 
                            echo  esc_attr( $border['color_hover'] ) ;
                            ?>!important;<?php 
                        }
                        
                        ?>
				    <?php 
                        
                        if ( !empty($bg['color_hover']) ) {
                            ?>background-color: <?php 
                            echo  esc_attr( $bg['color_hover'] ) ;
                            ?>!important;<?php 
                        }
                        
                        ?>
					<?php 
                        
                        if ( !empty($font['color_hover']) ) {
                            ?>color: <?php 
                            echo  esc_attr( $font['color_hover'] ) ;
                            ?>;<?php 
                        }
                        
                        ?>
            	}
            	.uni-node-<?php 
                        echo  esc_attr( $id ) ;
                        ?> .uni-cpo-option-label__text-content {
	            	<?php 
                        
                        if ( !empty($font['color']) ) {
                            ?> color: <?php 
                            echo  esc_attr( $font['color'] ) ;
                            ?>;<?php 
                        }
                        
                        ?>
					<?php 
                        
                        if ( !empty($font['text_align']) ) {
                            ?> text-align: <?php 
                            echo  esc_attr( $font['text_align'] ) ;
                            ?>;<?php 
                        }
                        
                        ?>
					<?php 
                        
                        if ( $font['font_family'] !== 'inherit' ) {
                            ?> font-family: <?php 
                            echo  esc_attr( $font['font_family'] ) ;
                            ?>;<?php 
                        }
                        
                        ?>
					<?php 
                        
                        if ( $font['font_style'] !== 'inherit' ) {
                            ?> font-style: <?php 
                            echo  esc_attr( $font['font_style'] ) ;
                            ?>;<?php 
                        }
                        
                        ?>
					<?php 
                        
                        if ( !empty($font['font_weight']) ) {
                            ?> font-weight: <?php 
                            echo  esc_attr( $font['font_weight'] ) ;
                            ?>;<?php 
                        }
                        
                        ?>
					<?php 
                        
                        if ( !empty($font['font_size']['value']) ) {
                            ?> font-size: <?php 
                            echo  esc_attr( "{$font['font_size']['value']}{$font['font_size']['unit']}" ) ;
                            ?>; <?php 
                        }
                        
                        ?>
					<?php 
                        
                        if ( !empty($font['letter_spacing']) ) {
                            ?> letter-spacing: <?php 
                            echo  esc_attr( $font['letter_spacing'] ) ;
                            ?>em;<?php 
                        }
                        
                        ?>
                    <?php 
                        
                        if ( !empty($main['width_px']) ) {
                            ?> width: <?php 
                            echo  esc_attr( $main['width_px'] ) ;
                            ?>px; <?php 
                        }
                        
                        ?>
                    <?php 
                        
                        if ( !empty($main['height_px']) ) {
                            ?> height: <?php 
                            echo  esc_attr( $main['height_px'] ) ;
                            ?>px; line-height: <?php 
                            echo  esc_attr( $main['height_px'] ) ;
                            ?>px; <?php 
                        } else {
                            
                            if ( $main['width_px'] ) {
                                ?> height: <?php 
                                echo  esc_attr( $main['width_px'] ) ;
                                ?>px; line-height: <?php 
                                echo  esc_attr( $main['width_px'] ) ;
                                ?>px; <?php 
                            }
                        
                        }
                        
                        ?>
                    <?php 
                        
                        if ( !empty($border['width_px']) ) {
                            ?> border-width: <?php 
                            echo  esc_attr( $border['width_px'] ) ;
                            ?>px!important; border-style: solid; <?php 
                        } else {
                            ?> border-width: 0!important; border-style: solid; <?php 
                        }
                        
                        ?>
            		<?php 
                        
                        if ( !empty($border['color']) ) {
                            ?> border-color: <?php 
                            echo  esc_attr( $border['color'] ) ;
                            ?>!important;<?php 
                        }
                        
                        ?>
            		<?php 
                        
                        if ( !empty($bg['color']) ) {
                            ?> background-color: <?php 
                            echo  esc_attr( $bg['color'] ) ;
                            ?>!important;<?php 
                        }
                        
                        ?>
	            	<?php 
                        
                        if ( $cpo_geom_radio === 'circle' ) {
                            ?> border-radius: 100%; <?php 
                        } else {
                            ?> border-radius: 0%; <?php 
                        }
                        
                        ?>
	            	<?php 
                        
                        if ( !empty($padding['top']) ) {
                            ?> padding-top: <?php 
                            echo  esc_attr( "{$padding['top']}{$padding['unit']}" ) ;
                            ?>; <?php 
                        }
                        
                        ?>
					<?php 
                        
                        if ( !empty($padding['bottom']) ) {
                            ?> padding-bottom: <?php 
                            echo  esc_attr( "{$padding['bottom']}{$padding['unit']}" ) ;
                            ?>; <?php 
                        }
                        
                        ?>
					<?php 
                        
                        if ( !empty($padding['left']) ) {
                            ?> padding-left: <?php 
                            echo  esc_attr( "{$padding['left']}{$padding['unit']}" ) ;
                            ?>; <?php 
                        }
                        
                        ?>
					<?php 
                        
                        if ( !empty($padding['right']) ) {
                            ?> padding-right: <?php 
                            echo  esc_attr( "{$padding['right']}{$padding['unit']}" ) ;
                            ?>; <?php 
                        }
                        
                        ?>
                }
	        <?php 
                    }
                
                }
            
            }
        
        }
        
        ?>
            .uni-node-<?php 
        echo  esc_attr( $id ) ;
        ?> .uni-cpo-option-label__description {
                <?php 
        
        if ( !empty($description['color']) ) {
            ?> color: <?php 
            echo  esc_attr( $description['color'] ) ;
            ?>!important;<?php 
        }
        
        ?>
                <?php 
        
        if ( !empty($description['font_weight']) ) {
            ?> font-weight: <?php 
            echo  esc_attr( $description['font_weight'] ) ;
            ?>!important;<?php 
        }
        
        ?>
                <?php 
        
        if ( !empty($description['font_size_desc']['value']) ) {
            ?> font-size: <?php 
            echo  esc_attr( "{$description['font_size_desc']['value']}{$description['font_size_desc']['unit']}" ) ;
            ?>!important; <?php 
        }
        
        ?>
            }

		<?php 
        return ob_get_clean();
    }
    
    public function calculate( $form_data )
    {
        $post_name = trim( $this->get_slug(), '{}' );
        $suboptions = $this->get_cpo_suboptions();
        
        if ( !empty($form_data[$post_name]) ) {
            if ( isset( $suboptions['data']['cpo_radio_options'] ) && !empty($suboptions['data']['cpo_radio_options']) ) {
                foreach ( $suboptions['data']['cpo_radio_options'] as $k => $v ) {
                    if ( (( !empty($v['slug']) ? $v['slug'] : '' )) === $form_data[$post_name] ) {
                        return array(
                            $post_name => array(
                            'calc'       => ( !empty($v['rate']) ? floatval( $v['rate'] ) : 0 ),
                            'cart_meta'  => $v['slug'],
                            'order_meta' => $v['label'],
                        ),
                        );
                    }
                }
            }
            // else
            return array(
                $post_name => array(
                'calc'       => 0,
                'cart_meta'  => '',
                'order_meta' => '',
            ),
            );
        } else {
            return array(
                $post_name => array(
                'calc'       => 0,
                'cart_meta'  => '',
                'order_meta' => '',
            ),
            );
        }
    
    }

}