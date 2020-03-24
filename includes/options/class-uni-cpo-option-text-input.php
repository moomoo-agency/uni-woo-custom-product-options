<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Option_Text_Input class
*
*/

class Uni_Cpo_Option_Text_Input extends Uni_Cpo_Option implements Uni_Cpo_Option_Interface {

	/**
	 * Stores extra (specific to this) option data.
	 *
	 * @var array
	 */
	protected $extra_data = array();

	/**
	 * Constructor gets the post object and sets the ID for the loaded option.
	 *
	 */
	public function __construct( $option = 0 ) {

		parent::__construct( $option );

	}

	public static function get_type() {
		return 'text_input';
	}

	public static function get_title() {
		return __( 'Text Input', 'uni-cpo' );
	}

	/**
	 * Returns an array of special vars associated with the option
	 *
	 * @return array
	 */
	public static function get_special_vars() {
		return array( 'count', 'count_spaces' );
	}

	/**
	 * Returns an array of data used in js query builder
	 *
	 * @return array
	 */
	public static function get_filter_data() {
		$operators = array(
			'less',
			'less_or_equal',
			'equal',
			'not_equal',
			'greater_or_equal',
			'greater',
			'is_empty',
			'is_not_empty'
		);

		$special_operators = array(
			'less',
			'less_or_equal',
			'equal',
			'not_equal',
			'greater_or_equal',
			'greater',
			'is_empty',
			'is_not_empty',
			'between',
			'not_between'
		);

		return array(
			'input'        => 'text',
			'operators'    => $operators,
			'special_vars' => array(
				'count'        => array(
					'type'      => 'integer',
					'input'     => 'text',
					'operators' => $special_operators
				),
				'count_spaces' => array(
					'type'      => 'integer',
					'input'     => 'text',
					'operators' => $special_operators
				)
			)
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	public function get_cpo_rate() {
		$cpo_general = $this->get_cpo_general();

		return ( ! empty( $cpo_general['main']['cpo_rate'] ) ) ? floatval( $cpo_general['main']['cpo_rate'] ) : 0;
	}

	/*
    |--------------------------------------------------------------------------
    | Other Actions
    |--------------------------------------------------------------------------
    */

	public function formatted_model_data() {

		$model['pid']                                         = $this->get_id();
		$model['settings']['general']                         = $this->get_general();
		$model['settings']['general']['status']               = array(
			'sync' => array(
				'type' => 'none',
				'pid'  => 0
			)
		);
		$model['settings']['general']                         = array_reverse( $model['settings']['general'] );
		$model['settings']['style']                           = $this->get_style();
		$model['settings']['advanced']                        = $this->get_advanced();
		$model['settings']['cpo_general']                     = $this->get_cpo_general();
		$model['settings']['cpo_general']['main']['cpo_slug'] = $this->get_slug_ending();
		$model['settings']['cpo_conditional']                 = $this->get_cpo_conditional();
		$model['settings']['cpo_validation']                  = $this->get_cpo_validation();

		return stripslashes_deep( $model );
	}

	public function get_edit_field( $data, $value, $context = 'cart' ) {
		$id                   = $data['id'];
		$type                 = $data['type'];
		$main                 = $data['settings']['general']['main'];
		$cpo_general_main     = $data['settings']['cpo_general']['main'];
		$cpo_general_advanced = $data['settings']['cpo_general']['advanced'];
		$cpo_validation_main  = ( isset( $data['settings']['cpo_validation']['main'] ) )
			? $data['settings']['cpo_validation']['main']
			: array();
		$cpo_validation_logic = ( isset( $data['settings']['cpo_validation']['logic'] ) )
			? $data['settings']['cpo_validation']['logic']
			: array();
		$is_cart_edit         = ( isset( $cpo_general_advanced['cpo_enable_cartedit'] ) && 'yes' === $cpo_general_advanced['cpo_enable_cartedit'] )
			? true
			: false;
		$attributes           = array( 'data-parsley-trigger' => 'change focusout submit' );
		$input_type           = 'text';
		$is_required          = ( 'yes' === $cpo_general_main['cpo_is_required'] ) ? true : false;

		$slug              = $this->get_slug();
		$input_css_class[] = $slug . '-field';
		$input_css_class[] = 'cpo-cart-item-option';
		$input_css_class[] = 'js-uni-cpo-field-' . $type;

		if ( 'order' === $context ) {
			$input_css_class[] = 'uni-admin-order-item-option-input';
		}

		if ( $is_required && 'cart' === $context ) {
			$attributes['data-parsley-required'] = 'true';
		}
        if ( 'integer' === $cpo_general_main['cpo_type'] ) {
            $input_type                      = 'number';
            $attributes['data-parsley-type'] = 'integer';
        }
        if ( 'double' === $cpo_general_main['cpo_type'] ) {
            $input_type                      = 'text';
            $attributes['data-parsley-type-step'] = '0.1';
            $attributes['data-parsley-pattern']   = '/^(-?\d+(?:[\.]\d{0,1})?)$/';
        }
        if ( in_array( $cpo_general_main['cpo_type'], array( 'integer', 'double' ) ) ) {
            if ( isset( $cpo_general_main['cpo_min_val'] ) && '' !== $cpo_general_main['cpo_min_val'] ) {
                $attributes['data-parsley-min'] = $cpo_general_main['cpo_min_val'];
            }
            if ( isset( $cpo_general_main['cpo_max_val'] ) && '' !== $cpo_general_main['cpo_max_val'] ) {
                $attributes['data-parsley-max'] = $cpo_general_main['cpo_max_val'];
            }
        }
		if ( 'string' === $cpo_general_main['cpo_type'] ) {
			if ( ! empty( $cpo_general_main['cpo_min_chars'] ) ) {
				$attributes['data-parsley-minlength'] = $cpo_general_main['cpo_min_chars'];
			}
			if ( ! empty( $cpo_general_main['cpo_max_chars'] ) ) {
				$attributes['data-parsley-maxlength'] = $cpo_general_main['cpo_max_chars'];
			}
		}
		if ( empty( $main['width']['value'] ) ) {
			$attributes['size'] = 2;
		}
        if ( ! empty( $cpo_general_main['cpo_step_val'] )
             && in_array( $cpo_general_main['cpo_type'], array( 'integer', 'double' ) ) ) {
            $attributes['data-parsley-type-step'] = $cpo_general_main['cpo_step_val'];
            if ( 'double' === $cpo_general_main['cpo_type'] ) {
                $decimals_count                     = uni_cpo_get_decimals_count( $cpo_general_main['cpo_step_val'] );
                $attributes['data-parsley-pattern'] = '/^(-?\d+(?:[\.]\d{0,' . $decimals_count . '})?)$/';
            }
        }

		if ( ! empty( $cpo_validation_main ) && isset( $cpo_validation_main['cpo_validation_msg'] )
		     && is_array( $cpo_validation_main['cpo_validation_msg'] ) ) {
			foreach ( $cpo_validation_main['cpo_validation_msg'] as $k => $v ) {
				if ( empty( $v ) ) {
					continue;
				}
				switch ( $k ) {
					case 'req':
						$attributes['data-parsley-required-message'] = $v;
						break;
					case 'type' :
						$attributes['data-parsley-type-message'] = $v;
						break;
					case 'custom' :
						$extra_validation_msgs = preg_split( '/\R/', $v );
						$attributes            = uni_cpo_field_attributes_modifier( $extra_validation_msgs, $attributes );
						break;
					default :
						break;
				}
			}
		}

		if ( ! empty( $cpo_validation_logic['cpo_vc_extra'] ) ) {
			$extra_validation = preg_split( '/\R/', $cpo_validation_logic['cpo_vc_extra'] );
			$attributes       = uni_cpo_field_attributes_modifier( $extra_validation, $attributes );
		}

		if ( $context !== 'cart' ) {
			$attributes = array();
		}

		ob_start();
		?>
        <div class="cpo-cart-item-option-wrapper uni-node-<?php echo esc_attr( $id ) ?> <?php if ( 'order' === $context ) { echo esc_attr( "uni-admin-order-item-option-wrapper" ); } ?>">
            <label><?php esc_html_e( uni_cpo_get_proper_option_label_sp( uni_cpo_sanitize_label( $this->cpo_order_label() ) ) ) ?></label>
			<?php if ( 'order' === $context || ( 'cart' === $context && $is_cart_edit ) ) { ?>
                <input
                        class="<?php echo implode( ' ', array_map( function ( $el ) {
							return esc_attr( $el );
						}, $input_css_class ) ); ?>"
                        name="<?php echo esc_attr( $slug ) ?>"
                        value="<?php echo esc_attr( $value ) ?>"
                        type="<?php echo esc_attr( $input_type ); ?>"
					<?php echo $this::get_custom_attribute_html( $attributes ); ?> />
			<?php } else { ?>
                <input
                        class="<?php echo implode( ' ', array_map( function ( $el ) {
							return esc_attr( $el );
						}, $input_css_class ) ); ?>"
                        name="<?php echo esc_attr( $slug ) ?>"
                        value="<?php echo esc_attr( $value ) ?>"
                        type="<?php echo esc_attr( $input_type ); ?>"
                        disabled/>
                <input
                        class="cpo-cart-item-option"
                        name="<?php echo esc_attr( $slug ) ?>"
                        value="<?php echo esc_attr( $value ) ?>"
                        type="hidden"/>
			<?php } ?>
        </div>
		<?php

		return ob_get_clean();
	}

	public static function get_settings() {
		return array(
			'settings' => array(
				'general'         => array(
					'status' => array(
						'sync' => array(
							'type' => 'none',
							'pid'  => 0
						),
					),
					'main'   => array(
						'width'  => array(
							'value' => 100,
							'unit'  => '%'
						),
						'height' => array(
							'value' => 36,
							'unit'  => 'px'
						)
					)
				),
				'style'           => array(
                    'label'   => array(
                        'color'          => '',
                        'text_align_label'     => '',
                        'font_family'    => 'inherit',
                        'font_weight'    => '',
                        'font_size_label'      => array(
                            'value' => '',
                            'unit'  => 'px'
                        ),
                    ),
					'font'   => array(
						'color'          => '',
						'text_align'     => '',
						'font_family'    => 'inherit',
						'font_style'     => 'inherit',
						'font_weight'    => '',
						'font_size'      => array(
							'value' => '',
							'unit'  => 'px'
						),
						'letter_spacing' => ''
					),
					'border' => array(
						'border_unit'   => 'px',
						'border_top'    => array(
							'style' => 'solid',
							'width' => '1',
							'color' => '#d7d7d7'
						),
						'border_bottom' => array(
							'style' => 'solid',
							'width' => '1',
							'color' => '#d7d7d7'
						),
						'border_left'   => array(
							'style' => 'solid',
							'width' => '1',
							'color' => '#d7d7d7'
						),
						'border_right'  => array(
							'style' => 'solid',
							'width' => '1',
							'color' => '#d7d7d7'
						),
						'radius'        => array(
							'value' => 5,
							'unit'  => 'px'
						),
					),
					'text_input'    => array(
						'padding' => array(
							'top'    => 0,
							'right'  => 14,
							'bottom' => 0,
							'left'   => 14,
							'unit'   => 'px'
						)
					)
				),
				'advanced'        => array(
					'layout'    => array(
						'margin'  => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
							'unit'   => 'px'
						)
					),
					'selectors' => array(
						'id_name'    => '',
						'class_name' => ''
					)
				),
				'cpo_general'     => array(
					'main'     => array(
						'cpo_slug'        => '',
						'cpo_is_required' => 'no',
						'cpo_type'        => 'string',
						'cpo_min_val'     => '',
						'cpo_max_val'     => '',
						'cpo_step_val'    => '',
						'cpo_def_val'     => '',
						'cpo_min_chars'   => '',
						'cpo_max_chars'   => '',
						'cpo_rate'        => ''
					),
					'advanced' => array(
						'cpo_label'        => '',
						'cpo_label_tag'    => 'label',
						'cpo_order_label'  => '',
						'cpo_is_tooltip'   => 'no',
						'cpo_tooltip_type' => 'classic',
						'cpo_tooltip'      => '',
						'cpo_tooltip_image' => array(
							'url' => '',
							'id' => 0,
							'alt' => ''
						),
						'cpo_tooltip_class' => '',
						'cpo_enable_cartedit' => 'no',
						'cpo_order_visibility' => 'no'
					)
				),
				'cpo_conditional' => array(
					'main' => array(
						'cpo_is_fc'      => 'no',
						'cpo_fc_default' => 'hide',
						'cpo_fc_scheme'  => ''
					)
				),
				'cpo_validation' => array(
					'main' => array(
						'cpo_validation_msg' => array(
                            'req' => '',
                            'type' => '',
                            'custom' => ''
                        )
					),
					'logic' => array(
						'cpo_vc_extra'   => '',
						'cpo_is_vc'      => 'no',
						'cpo_vc_scheme'  => array()
					)
				)
			)
		);
	}

	public static function js_template() {
		?>
        <script id="js-builderius-module-<?php echo self::get_type(); ?>-tmpl" type="text/template">
            {{ const { id, type } = data; }}
            {{ const { id_name, class_name } = data.settings.advanced.selectors; }}
            {{ const { width, height } = data.settings.general.main; }}

            {{ const color_label = uniGet( data.settings.style, 'label.color', '' ); }}
            {{ const text_align_label = uniGet( data.settings.style, 'label.text_align_label', 'inherit' ); }}
            {{ const font_family_label = uniGet( data.settings.style, 'label.font_family', '' ); }}
            {{ const font_weight_label = uniGet( data.settings.style, 'label.font_weight', '' ); }}
            {{ const font_size_label = uniGet( data.settings.style, 'label.font_size_label', {value:'',unit:'px'} ); }}

            {{ const { color, text_align, font_family, font_style, font_weight, font_size, letter_spacing, line_height } = data.settings.style.font; }}
            {{ const { border_unit, border_top, border_bottom, border_left, border_right, radius } = data.settings.style.border; }}
            {{ const { margin } = data.settings.advanced.layout; }}
            {{ const padding = uniGet( data.settings.style, 'text_input.padding', {top:0,right:14,bottom:0,left:14,unit:'px'} ); }}

            {{ const { cpo_slug, cpo_is_required, cpo_def_val, cpo_max_chars } = data.settings.cpo_general.main; }}
            {{ let cpo_type = data.settings.cpo_general.main.cpo_type; if ( cpo_type === 'string' ) { cpo_type = 'text'; } else { cpo_type = 'number'; } }}
            {{ const { cpo_label_tag, cpo_label, cpo_is_tooltip, cpo_tooltip } = data.settings.cpo_general.advanced; }}
			{{ const cpo_tooltip_type = uniGet( data.settings.cpo_general, 'advanced.cpo_tooltip_type', 'classic' ); }}
			{{ const cpo_tooltip_image = uniGet( data.settings.cpo_general, 'advanced.cpo_tooltip_image', {url:''} ); }}
			{{ const cpo_tooltip_class = uniGet( data.settings.cpo_general, 'advanced.cpo_tooltip_class', '' ); }}
            <div
                id="{{- id_name }}"
                class="uni-module uni-module-{{- type }} uni-node-{{- id }} {{- class_name }}"
                data-node="{{- id }}"
                data-type="{{- type }}">
            <style>
            	.uni-node-{{= id }} {
            		{{ if ( margin.top !== '' ) { }} margin-top: {{= margin.top + margin.unit }}!important; {{ } }}
                    {{ if ( margin.bottom !== '' ) { }} margin-bottom: {{= margin.bottom + margin.unit }}!important; {{ } }}
                    {{ if ( margin.left !== '' ) { }} margin-left: {{= margin.left + margin.unit }}!important; {{ } }}
                    {{ if ( margin.right !== '' ) { }} margin-right: {{= margin.right + margin.unit }}!important; {{ } }}
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
        		.uni-node-{{= id }} input[type="text"], .uni-node-{{= id }} input[type="number"] {
        			{{ if ( width.value !== '' ) { }} width: {{= width.value+width.unit }}!important; max-width: {{= width.value+width.unit }}!important; {{ } }}
        			{{ if ( height.value !== '' ) { }} height: {{= height.value+height.unit }}!important; min-height: {{= height.value+height.unit }}!important; {{ } }}
                    {{ if ( border_top.style !== 'none' && border_top.color !== '' ) { }} border-top: {{= border_top.width + 'px '+ border_top.style +' '+ border_top.color }}!important; {{ } }}
                    {{ if ( border_bottom.style !== 'none' && border_bottom.color !== '' ) { }} border-bottom: {{= border_bottom.width + 'px '+ border_bottom.style +' '+ border_bottom.color }}!important; {{ } }}
                    {{ if ( border_left.style !== 'none' && border_left.color !== '' ) { }} border-left: {{= border_left.width + 'px '+ border_left.style +' '+ border_left.color }}!important; {{ } }}
                    {{ if ( border_right.style !== 'none' && border_right.color !== '' ) { }} border-right: {{= border_right.width + 'px '+ border_right.style +' '+ border_right.color }}!important; {{ } }}
        			{{ if ( radius.value !== '' ) { }} border-radius: {{= radius.value + radius.unit }}!important; {{ } }}
                    {{ if ( padding.top !== '' ) { }} padding-top: {{= padding.top + padding.unit }}!important; {{ } }}
                    {{ if ( padding.bottom !== '' ) { }} padding-bottom: {{= padding.bottom + padding.unit }}!important; {{ } }}
                    {{ if ( padding.left !== '' ) { }} padding-left: {{= padding.left + padding.unit }}!important; {{ } }}
                    {{ if ( padding.right !== '' ) { }} padding-right: {{= padding.right + padding.unit }}!important; {{ } }}
                    {{ if ( color !== '' ) { }} color: {{= color }}!important; {{ } }}
                    {{ if ( text_align !== '' ) { }} text-align: {{= text_align }}!important; {{ } }}
                    {{ if ( font_family !== 'inherit' ) { }} font-family: {{= font_family }}!important; {{ } }}
                    {{ if ( font_style !== 'inherit' ) { }} font-style: {{= font_style }}!important; {{ } }}
                    {{ if ( font_size.value !== '' ) { }} font-size: {{= font_size.value+font_size.unit }}!important; {{ } }}
                    {{ if ( font_weight !== '' ) { }} font-weight: {{= font_weight }}!important; {{ } }}
                    {{ if ( letter_spacing !== '' ) { }} letter-spacing: {{= letter_spacing+'em' }}!important; {{ } }}
        		}
        	</style>
            {{ if ( cpo_label_tag && cpo_label !== '' ) { }}
                <{{- cpo_label_tag }} class="uni-cpo-module-{{- type }}-label {{ if ( cpo_is_required === 'yes' ) { }} uni_cpo_field_required {{ } }}">
                	{{- cpo_label }}
                	{{ if ( cpo_is_tooltip === 'yes' && cpo_tooltip !== '' && cpo_tooltip_type === 'classic' ) { }} <span class="uni-cpo-tooltip" data-tip="{{- cpo_tooltip }}"></span> {{ } else if ( cpo_is_tooltip === 'yes' && cpo_tooltip_image.url !== '' && cpo_tooltip_type === 'lightbox' ) { }} <span class="uni-cpo-tooltip"></span> {{ } else if ( cpo_is_tooltip === 'yes' && cpo_tooltip_class !== '' && cpo_tooltip_type === 'popup' ) { }} <span class="uni-cpo-tooltip"></span> {{ } }}
            	</{{- cpo_label_tag }}>
        	{{ } }}
            <input
                class="{{- cpo_slug }}-field js-uni-cpo-field-{{- type }}"
                id="{{- cpo_slug }}-field"
                name="{{- cpo_slug }}"
                type="{{- cpo_type }}"
                {{ if ( width.value === '' ) { }} size="2" {{ } }}
                value="{{- cpo_def_val }}"/>
            </div>
        </script>
		<?php
	}

	public static function template( $data, $post_data = array() ) {
		$id                   = $data['id'];
		$pid                  = ( ! empty( $data['pid'] ) ) ? absint( $data['pid'] ) : 0;
		$type                 = $data['type'];
		$selectors            = $data['settings']['advanced']['selectors'];
		$main            	  = $data['settings']['general']['main'];
		$cpo_general_main     = $data['settings']['cpo_general']['main'];
		$cpo_general_advanced = $data['settings']['cpo_general']['advanced'];
		$cpo_validation_main  = ( isset( $data['settings']['cpo_validation']['main'] ) )
            ? $data['settings']['cpo_validation']['main']
            : array();
		$cpo_validation_logic = ( isset( $data['settings']['cpo_validation']['logic'] ) )
			? $data['settings']['cpo_validation']['logic']
			: array();
		$cpo_label_tag        = $cpo_general_advanced['cpo_label_tag'];
		$attributes           = array( 'data-parsley-trigger' => 'change focusout submit' );
		$wrapper_attributes   = array();
		$option               = false;
		$input_type           = 'text';
		$rules_data           = $data['settings']['cpo_conditional']['main'];
		$is_required          = ( 'yes' === $cpo_general_main['cpo_is_required'] ) ? true : false;
		$is_tooltip           = ( 'yes' === $cpo_general_advanced['cpo_is_tooltip'] ) ? true : false;
		$is_enabled           = ( 'yes' === $rules_data['cpo_is_fc'] ) ? true : false;
		$is_hidden            = ( 'hide' === $rules_data['cpo_fc_default'] ) ? true : false;
		$cpo_tooltip_type  	  = ( isset( $cpo_general_advanced['cpo_tooltip_type'] ) )
            ? $cpo_general_advanced['cpo_tooltip_type']
            : 'classic';
		$cpo_tooltip_image    = ( isset( $cpo_general_advanced['cpo_tooltip_image']['url'] ) )
            ? $cpo_general_advanced['cpo_tooltip_image']['url']
            : '';

		if ( ! empty( $data['pid'] ) ) {
			$option = uni_cpo_get_option( $data['pid'] );
		}

		$slug              = ( $pid && is_object( $option ) && 'trash' !== $option->get_status() ) ? $option->get_slug() : '';
		$css_id[]          = $slug;
		$css_class         = array(
			'uni-module',
			'uni-module-' . $type,
			'uni-node-' . $id
		);
		$input_css_class[] = $slug . '-field';
		$input_css_class[] = 'js-uni-cpo-field';
		$input_css_class[] = 'js-uni-cpo-field-' . $type;
		if ( ! empty( $selectors['id_name'] ) ) {
			array_push( $css_id, $selectors['id_name'] );
		}
		if ( ! empty( $selectors['class_name'] ) ) {
			array_push( $css_class, $selectors['class_name'] );
		}

		if ( $is_required ) {
			$attributes['data-parsley-required'] = 'true';
		}
		if ( 'integer' === $cpo_general_main['cpo_type'] ) {
			$input_type                      = 'number';
			$attributes['data-parsley-type'] = 'integer';
		}
		if ( 'double' === $cpo_general_main['cpo_type'] ) {
			//$input_type                      = 'text';
			$input_type                      = 'number';
			//$attributes['data-parsley-type-step'] = '0.1';
			$attributes['data-parsley-pattern']   = '/^(-?\d+(?:[\.]\d{0,1})?)$/';
		}
		if ( in_array( $cpo_general_main['cpo_type'], array( 'integer', 'double' ) ) ) {
			if ( isset( $cpo_general_main['cpo_min_val'] ) && '' !== $cpo_general_main['cpo_min_val'] ) {
				$attributes['data-parsley-min'] = $cpo_general_main['cpo_min_val'];
			}
			if ( isset( $cpo_general_main['cpo_max_val'] ) && '' !== $cpo_general_main['cpo_max_val'] ) {
				$attributes['data-parsley-max'] = $cpo_general_main['cpo_max_val'];
			}
		}
		if ( 'string' === $cpo_general_main['cpo_type'] ) {
			if ( ! empty( $cpo_general_main['cpo_min_chars'] ) ) {
				$attributes['data-parsley-minlength'] = $cpo_general_main['cpo_min_chars'];
			}
			if ( ! empty( $cpo_general_main['cpo_max_chars'] ) ) {
				$attributes['data-parsley-maxlength'] = $cpo_general_main['cpo_max_chars'];
			}
		}
		if ( empty( $main['width']['value'] ) ) {
			$attributes['size'] = 2;
		}
		if ( ! empty( $cpo_general_main['cpo_step_val'] )
		      && in_array( $cpo_general_main['cpo_type'], array( 'integer', 'double' ) ) ) {
			$attributes['step'] = $cpo_general_main['cpo_step_val'];
			if ( 'double' === $cpo_general_main['cpo_type'] ) {
				//unset($attributes['step']);
				$decimals_count                     = uni_cpo_get_decimals_count( $cpo_general_main['cpo_step_val'] );
				$attributes['data-parsley-pattern'] = '/^(-?\d+(?:[\.]\d{0,' . $decimals_count . '})?)$/';
			}
		}

		if ( ! empty( $cpo_validation_main ) && isset( $cpo_validation_main['cpo_validation_msg'] )
                && is_array( $cpo_validation_main['cpo_validation_msg'] ) ) {
		    foreach ( $cpo_validation_main['cpo_validation_msg'] as $k => $v ) {
		        if ( empty($v) ) {
		            continue;
                }
		        switch ( $k ) {
			        case 'req':
				        $attributes['data-parsley-required-message'] = $v;
				        break;
                    case 'type' :
	                    $attributes['data-parsley-type-message'] = $v;
	                    break;
                    case 'custom' :
	                    $extra_validation_msgs = preg_split( '/\R/', $v );
	                    $attributes = uni_cpo_field_attributes_modifier( $extra_validation_msgs, $attributes );
	                    break;
			        default :
	                    break;
		        }
            }
		}

		if ( ! empty( $cpo_validation_logic['cpo_vc_extra'] ) ) {
			$extra_validation = preg_split( '/\R/', $cpo_validation_logic['cpo_vc_extra'] );
			$attributes = uni_cpo_field_attributes_modifier( $extra_validation, $attributes );
		}

		if ( $is_enabled && $is_hidden ) {
			$wrapper_attributes['style'] = 'display:none;';
			$input_css_class[]           = 'uni-cpo-excluded-field';
		}

		$wrapper_attributes = apply_filters( 'uni_wrapper_attributes_for_option', $wrapper_attributes, $slug, $id );
		$default_value = ( ! empty( $post_data ) && ! empty( $slug ) && ! empty( $post_data[$slug] ) )
			? $post_data[$slug]
            : $cpo_general_main['cpo_def_val'];
		?>
    <div
            id="<?php echo implode( ' ', array_map( function ( $el ) {
				return esc_attr( $el );
			}, $css_id ) ); ?>"
            class="<?php echo implode( ' ', array_map( function ( $el ) {
				return esc_attr( $el );
			}, $css_class ) ); ?>"
		<?php echo self::get_custom_attribute_html( $wrapper_attributes ); ?>>
		<?php
		if ( ! empty( $cpo_general_advanced['cpo_label'] ) ) { ?>
            <<?php echo esc_attr( $cpo_label_tag ); ?> class="uni-cpo-module-<?php echo esc_attr( $type ); ?>-label <?php if ( $is_required ) { ?> uni_cpo_field_required <?php } ?>">
			<?php esc_html_e( uni_cpo_get_proper_option_label_sp( $cpo_general_advanced['cpo_label'] ) ); ?>
			<?php if ( $is_tooltip && $cpo_general_advanced['cpo_tooltip'] !== '' && $cpo_tooltip_type === 'classic' ) { ?>
                <span class="uni-cpo-tooltip" data-tip="<?php echo uni_cpo_sanitize_tooltip( $cpo_general_advanced['cpo_tooltip'] ); ?>"></span>
			<?php } else if ( $is_tooltip && $cpo_tooltip_image !== '' && $cpo_tooltip_type === 'lightbox' ) { ?>
				<a href="<?php esc_html_e( $cpo_tooltip_image ); ?>" data-lity class="uni-cpo-tooltip"></a>
			<?php } else if ( $is_tooltip && $cpo_general_advanced['cpo_tooltip_class'] !== '' && $cpo_tooltip_type === 'popup' ) { ?>
				<span class="uni-cpo-tooltip <?php esc_html_e( $cpo_general_advanced['cpo_tooltip_class'] ); ?>"></span>
			<?php } ?>
            </<?php echo esc_attr( $cpo_label_tag ); ?>>
		<?php } ?>
        <input
                class="<?php echo implode( ' ', array_map( function ( $el ) {
					return esc_attr( $el );
				}, $input_css_class ) ); ?>"
                id="<?php echo esc_attr( $slug ); ?>-field"
                name="<?php echo esc_attr( $slug ); ?>"
                type="<?php echo esc_attr( $input_type ); ?>"
                value="<?php echo esc_attr( $default_value ); ?>"
			    <?php echo self::get_custom_attribute_html( $attributes ); ?> />
        </div>
		<?php

		self::conditional_rules( $data );
		self::validation_rules( $data, $attributes );
	}

	public static function get_css( $data ) {
		$id            = $data['id'];
        $type          = $data['type'];
		$main          = $data['settings']['general']['main'];
		$label         = ( ! empty( $data['settings']['style']['label'] ) ) ? $data['settings']['style']['label'] : array();
		$font          = $data['settings']['style']['font'];

		$border_top    = $data['settings']['style']['border']['border_top'];
		$border_bottom = $data['settings']['style']['border']['border_bottom'];
		$border_left   = $data['settings']['style']['border']['border_left'];
		$border_right  = $data['settings']['style']['border']['border_right'];
		$radius        = $data['settings']['style']['border']['radius'];
		$padding = ( isset( $data['settings']['style']['text_input'] ) && isset( $data['settings']['style']['text_input']['padding'] ) )
			? $data['settings']['style']['text_input']['padding']
			: array();
		$margin        = $data['settings']['advanced']['layout']['margin'];
        $cpo_general_advanced = $data['settings']['cpo_general']['advanced'];

		ob_start();
		?>
        .uni-node-<?php echo esc_attr( $id ); ?> {
            <?php if ( ! empty( $margin['top'] ) ) { ?> margin-top: <?php echo esc_attr( "{$margin['top']}{$margin['unit']}" ) ?>!important; <?php } ?>
            <?php if ( ! empty( $margin['bottom'] ) ) { ?> margin-bottom: <?php echo esc_attr( "{$margin['bottom']}{$margin['unit']}" ) ?>!important; <?php } ?>
            <?php if ( ! empty( $margin['left'] ) ) { ?> margin-left: <?php echo esc_attr( "{$margin['left']}{$margin['unit']}" ) ?>!important; <?php } ?>
            <?php if ( ! empty( $margin['right'] ) ) { ?> margin-right: <?php echo esc_attr( "{$margin['right']}{$margin['unit']}" ) ?>!important; <?php } ?>
        }

        <?php if ( ! empty( $cpo_general_advanced['cpo_label'] ) ) { ?>
            .uni-node-<?php echo esc_attr( $id ); ?> .uni-cpo-module-<?php echo esc_attr( $type ); ?>-label {
                <?php if ( ! empty( $label['color'] ) ) { ?> color: <?php echo esc_attr( $label['color'] ); ?>!important;<?php } ?>
                <?php if ( ! empty( $label['text_align_label'] ) ) { ?> text-align: <?php echo esc_attr( $label['text_align_label'] ); ?>!important; display: block; <?php } ?>
                <?php if ( ! empty( $label['font_family'] ) && $label['font_family'] !== 'inherit' ) { ?> font-family: <?php echo esc_attr( $label['font_family'] ); ?>!important;<?php } ?>
                <?php if ( ! empty( $label['font_weight'] ) ) { ?> font-weight: <?php echo esc_attr( $label['font_weight'] ); ?>!important;<?php } ?>
                <?php if ( ! empty( $label['font_size_label']['value'] ) ) { ?> font-size: <?php echo esc_attr( "{$label['font_size_label']['value']}{$label['font_size_label']['unit']}" ) ?>!important; <?php } ?>
            }
        <?php } ?>

        .uni-node-<?php echo esc_attr( $id ); ?> input[type="text"], .uni-node-<?php echo esc_attr( $id ); ?> input[type="number"] {
			<?php if ( ! empty( $main['width']['value'] ) ) { ?> width: <?php echo esc_attr( "{$main['width']['value']}{$main['width']['unit']}" ) ?>!important; max-width: <?php echo esc_attr( "{$main['width']['value']}{$main['width']['unit']}" ) ?>!important;<?php } ?>
			<?php if ( ! empty( $main['height']['value'] ) ) { ?> height: <?php echo esc_attr( "{$main['height']['value']}{$main['height']['unit']}" ) ?>!important; min-height: <?php echo esc_attr( "{$main['height']['value']}{$main['height']['unit']}" ) ?>!important;<?php } ?>
			<?php if ( ! empty( $font['color'] ) ) { ?> color: <?php echo esc_attr( $font['color'] ); ?>!important;<?php } ?>
			<?php if ( ! empty( $font['text_align'] ) ) { ?> text-align: <?php echo esc_attr( $font['text_align'] ); ?>!important;<?php } ?>
			<?php if ( $font['font_family'] !== 'inherit' ) { ?> font-family: <?php echo esc_attr( $font['font_family'] ); ?>!important;<?php } ?>
			<?php if ( $font['font_style'] !== 'inherit' ) { ?> font-style: <?php echo esc_attr( $font['font_style'] ); ?>!important;<?php } ?>
			<?php if ( ! empty( $font['font_weight'] ) ) { ?> font-weight: <?php echo esc_attr( $font['font_weight'] ); ?>!important;<?php } ?>
			<?php if ( ! empty( $font['font_size']['value'] ) ) { ?> font-size: <?php echo esc_attr( "{$font['font_size']['value']}{$font['font_size']['unit']}" ) ?>!important; <?php } ?>
			<?php if ( ! empty( $font['letter_spacing'] ) ) { ?> letter-spacing: <?php echo esc_attr( $font['letter_spacing'] ); ?>em!important;<?php } ?>
			<?php if ( $border_top['style'] !== 'none' && ! empty( $border_top['color'] ) ) { ?> border-top: <?php echo esc_attr( "{$border_top['width']}px {$border_top['style']} {$border_top['color']}" ) ?>!important; <?php } ?>
			<?php if ( $border_bottom['style'] !== 'none' && ! empty( $border_bottom['color'] ) ) { ?> border-bottom: <?php echo esc_attr( "{$border_bottom['width']}px {$border_bottom['style']} {$border_bottom['color']}" ) ?>!important; <?php } ?>
			<?php if ( $border_left['style'] !== 'none' && ! empty( $border_left['color'] ) ) { ?> border-left: <?php echo esc_attr( "{$border_left['width']}px {$border_left['style']} {$border_left['color']}" ) ?>!important; <?php } ?>
			<?php if ( $border_right['style'] !== 'none' && ! empty( $border_right['color'] ) ) { ?> border-right: <?php echo esc_attr( "{$border_right['width']}px {$border_right['style']} {$border_right['color']}" ) ?>!important; <?php } ?>
			<?php if ( ! empty( $radius['value'] ) ) { ?> border-radius: <?php echo esc_attr( "{$radius['value']}{$radius['unit']}" ) ?>!important; <?php } ?>
			<?php if ( ! empty( $padding['top'] ) ) { ?> padding-top: <?php echo esc_attr( "{$padding['top']}{$padding['unit']}" ) ?>!important; <?php } ?>
			<?php if ( ! empty( $padding['bottom'] ) ) { ?> padding-bottom: <?php echo esc_attr( "{$padding['bottom']}{$padding['unit']}" ) ?>!important; <?php } ?>
			<?php if ( ! empty( $padding['left'] ) ) { ?> padding-left: <?php echo esc_attr( "{$padding['left']}{$padding['unit']}" ) ?>!important; <?php } ?>
			<?php if ( ! empty( $padding['right'] ) ) { ?> padding-right: <?php echo esc_attr( "{$padding['right']}{$padding['unit']}" ) ?>!important; <?php } ?>
        }

		<?php
		return ob_get_clean();
	}

	public function calculate( $form_data ) {
		$post_name = trim( $this->get_slug(), '{}' );

		if ( ! empty( $form_data[ $post_name ] ) ) {
			$price = $this->get_cpo_rate();
			$count = mb_strlen( $form_data[ $post_name ] );
			$count_no_spaces = mb_strlen( preg_replace('/\s+/', '', $form_data[ $post_name ] ) );
			if ( ! empty( $price ) ) {
				return array(
					$post_name                   => array(
						'calc'       => $price,
						'cart_meta'  => $form_data[ $post_name ],
						'order_meta' => $form_data[ $post_name ]
					),
					$post_name . '_count'        => array(
						'calc'       => intval($count),
						'cart_meta'  => intval($count),
						'order_meta' => intval($count)
					),
					$post_name . '_count_spaces' => array(
						'calc'       => intval($count_no_spaces),
						'cart_meta'  => intval($count_no_spaces),
						'order_meta' => intval($count_no_spaces)
					)
				);
			} else {
				return array(
					$post_name                   => array(
						'calc'       => floatval( $form_data[ $post_name ] ),
						'cart_meta'  => $form_data[ $post_name ],
						'order_meta' => $form_data[ $post_name ]
					),
					$post_name . '_count'        => array(
						'calc'       => intval($count),
						'cart_meta'  => intval($count),
						'order_meta' => intval($count)
					),
					$post_name . '_count_spaces' => array(
						'calc'       => intval($count_no_spaces),
						'cart_meta'  => intval($count_no_spaces),
						'order_meta' => intval($count_no_spaces)
					)
				);
			}
		} else {
			return array(
				$post_name                   => array(
					'calc'       => 0,
					'cart_meta'  => '',
					'order_meta' => ''
				),
				$post_name . '_count'        => array(
					'calc'       => 0,
					'cart_meta'  => 0,
					'order_meta' => 0
				),
				$post_name . '_count_spaces' => array(
					'calc'       => 0,
					'cart_meta'  => 0,
					'order_meta' => 0
				)
			);
		}
	}

}
