<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Option_Range_Slider class
*
*/

class Uni_Cpo_Option_Range_Slider extends Uni_Cpo_Option implements Uni_Cpo_Option_Interface {

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
		return 'range_slider';
	}

	public static function get_title() {
		return __( 'Range Slider', 'uni-cpo' );
	}

	/**
	 * Returns an array of special vars associated with the option
	 *
	 * @return array
	 */
	public static function get_special_vars() {
		return array( 'from', 'to' );
	}

	/**
	 * Returns an array of data used in js query builder
	 *
	 * @return array
	 */
	public static function get_filter_data() {
		$operators_main = array(
			'equal',
			'not_equal',
			'is_empty',
			'is_not_empty'
		);

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

		return array(
			'input'        => 'text',
			'operators'    => $operators_main,
			'special_vars' => array(
				'from'        => array(
					'type'      => 'integer',
					'input'     => 'text',
					'operators' => $operators
				),
				'to'        => array(
					'type'      => 'integer',
					'input'     => 'text',
					'operators' => $operators
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
		if ( ! empty( $cpo_general_main['cpo_min_val'] ) ) {
			$attributes['data-parsley-min'] = $cpo_general_main['cpo_min_val'];
		}
		if ( ! empty( $cpo_general_main['cpo_max_val'] ) ) {
			$attributes['data-parsley-max'] = $cpo_general_main['cpo_max_val'];
		}

		if ( empty( $main['width']['value'] ) ) {
			$attributes['size'] = 2;
		}
		if ( ! empty( $cpo_general_main['cpo_step_val'] ) ) {
			$attributes['data-parsley-type-step'] = $cpo_general_main['cpo_step_val'];
			$decimals_count                       = uni_cpo_get_decimals_count( $cpo_general_main['cpo_step_val'] );
			$attributes['data-parsley-pattern']   = '/^(\d+(?:[\.]\d{0,' . $decimals_count . '})?)$/';
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

		ob_start();
		?>
        <div class="cpo-cart-item-option-wrapper uni-node-<?php esc_attr_e( $id ) ?> <?php if ( 'order' === $context ) {
			echo esc_attr( "uni-admin-order-item-option-wrapper" );
		} ?>">
            <label><?php echo uni_cpo_sanitize_label( $this->cpo_order_label() ) ?></label>
			<?php if ( ( 'order' === $context && 'single' === $cpo_general_main['cpo_range_type'] ) || ( 'cart' === $context && $is_cart_edit && 'single' === $cpo_general_main['cpo_range_type'] ) ) { ?>
                <input
                        class="<?php echo implode( ' ', array_map( function ( $el ) {
							return esc_attr( $el );
						}, $input_css_class ) ); ?>"
                        name="<?php esc_attr_e( $slug ) ?>"
                        value="<?php esc_attr_e( $value ) ?>"
                        type="<?php esc_attr_e( $input_type ); ?>"
					<?php echo $this::get_custom_attribute_html( $attributes ); ?> />
			<?php } else { ?>
                <input
                        class="<?php echo implode( ' ', array_map( function ( $el ) {
							return esc_attr( $el );
						}, $input_css_class ) ); ?>"
                        name="<?php esc_attr_e( $slug ) ?>"
                        value="<?php esc_attr_e( $value ) ?>"
                        type="<?php esc_attr_e( $input_type ); ?>"
                        disabled/>
                <input
                        class="cpo-cart-item-option"
                        name="<?php esc_attr_e( $slug ) ?>"
                        value="<?php esc_attr_e( $value ) ?>"
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
				),
				'style'           => array(
					'text' => array(
						'color'       => '#ffffff'
					),
					'background' => array(
						'color_from'  => '#7cc0e6',
						'color_to'    => '#428bca',
						'color_hover' => '#ff0404',
					),
					'border' => array(
						'color_top'   => '#428bca',
						'color_bottom'=> '#428bca',
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
						),
						'padding' => array(
                            'top' => '',
                            'right' => '',
                            'bottom' => '',
                            'left' => '',
                            'unit' => 'px'
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
						'cpo_range_type'  => 'single',
						'cpo_min_val'     => '',
						'cpo_max_val'     => '',
						'cpo_step_val'    => '',
						'cpo_range_from'     => '',
						'cpo_range_to'     => '',
						'cpo_range_grid'    => 'no',
						'cpo_range_prefix'  => '',
						'cpo_range_postfix'  => '',
						'cpo_rate'        => ''
					),
					'advanced' => array(
						'cpo_label'        => '',
						'cpo_label_tag'    => 'label',
						'cpo_order_label'  => '',
						'cpo_is_tooltip'   => 'no',
						'cpo_tooltip'      => '',
						'cpo_enable_cartedit' => 'no'
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
                            'custom' => ''
                        )
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
			{{ const { color_from, color_to } = data.settings.style.background; }}
			{{ const { color_top, color_bottom } = data.settings.style.border; }}
			{{ const { color } = data.settings.style.text; }}
            {{ const { margin, padding } = data.settings.advanced.layout; }}

            {{ const { cpo_slug, cpo_is_required, cpo_range_type, cpo_min_val, cpo_max_val, cpo_step_val, cpo_range_from, cpo_range_to, cpo_range_grid, cpo_range_prefix, cpo_range_postfix  } = data.settings.cpo_general.main; }}
            {{ const { cpo_label_tag, cpo_label, cpo_is_tooltip, cpo_tooltip } = data.settings.cpo_general.advanced; }}
            <div
                id="{{- id_name }}"
                class="uni-module uni-module-{{- type }} uni-node-{{- id }} {{- class_name }} uni-range-slider-skin-{{- builderiusCfg.range_slider_style }}"
                data-node="{{- id }}"
                data-type="{{- type }}">
            <style>
				.uni-node-{{= id }} {
					{{ if ( margin.top !== '' ) { }} margin-top: {{= margin.top + margin.unit }}; {{ } }}
                    {{ if ( margin.bottom !== '' ) { }} margin-bottom: {{= margin.bottom + margin.unit }}; {{ } }}
                    {{ if ( margin.left !== '' ) { }} margin-left: {{= margin.left + margin.unit }}; {{ } }}
                    {{ if ( margin.right !== '' ) { }} margin-right: {{= margin.right + margin.unit }}; {{ } }}
				}
				.uni-node-{{= id }} > .irs {
					{{ if ( padding.top !== '' ) { }} margin-top: {{= padding.top + padding.unit }}; {{ } }}
					{{ if ( padding.bottom !== '' ) { }} margin-bottom: {{= padding.bottom + padding.unit }}; {{ } }}
					{{ if ( padding.left !== '' ) { }} margin-left: {{= padding.left + padding.unit }}; {{ } }}
					{{ if ( padding.right !== '' ) { }} margin-right: {{= padding.right + padding.unit }}; {{ } }}
				}
            	.uni-node-{{= id }} .irs-from,
				.uni-node-{{= id }} .irs-to,
				.uni-node-{{= id }} .irs-single,
				.uni-node-{{= id }} .irs-bar,
				.uni-node-{{= id }} .irs-bar-edge,
				.uni-node-{{= id }}.uni-range-slider-skin-flat .irs-slider.from:before,
				.uni-node-{{= id }}.uni-range-slider-skin-flat .irs-slider.to:before,
				.uni-node-{{= id }}.uni-range-slider-skin-flat .irs-slider.single:before {
					{{ if ( color_from !== '' && color_to === '' ) { }} background: {{= color_from }}; {{ } else if ( color_from === '' && color_to !== '' ) { }} background: {{= color_to }}; {{ } else { }} background: linear-gradient({{= color_from }}, {{= color_to }}); {{ } }}
					{{ if ( color !== '' ) { }} color: {{= color }}; {{ } }}
            	}
				.uni-node-{{= id }}.uni-range-slider-skin-html5 .irs-bar,
				.uni-node-{{= id }}.uni-range-slider-skin-html5 .irs-bar-edge {
					{{ if ( color_top !== '' ) { }} border-top-color: {{= color_top }}; {{ } }}
					{{ if ( color_bottom !== '' ) { }} border-bottom-color: {{= color_bottom }}; {{ } }}
				}
				.uni-node-{{= id }}.uni-range-slider-skin-html5 .irs-bar-edge {
					{{ if ( color_top !== '' ) { }} border-left-color: {{= color_top }}; {{ } }}
				}
				.uni-node-{{= id }} .irs-from:after, .uni-node-{{= id }} .irs-to:after, .uni-node-{{= id }} .irs-single:after {
					{{ if ( color_to !== '' ) { }} border-top-color: {{= color_to }}; {{ } else { }} border-top-color: {{= color_from }}; {{ } }}
				}
        	</style>
            {{ if ( cpo_label_tag && cpo_label !== '' ) { }}
                <{{- cpo_label_tag }} class="uni-cpo-module-{{- type }}-label {{ if ( cpo_is_required === 'yes' ) { }} uni_cpo_field_required {{ } }}">
                	{{- cpo_label }}
                	{{ if ( cpo_is_tooltip === 'yes' && cpo_tooltip !== '' ) { }} <span class="uni-cpo-tooltip" data-tip="{{- cpo_tooltip }}"></span> {{ } }}
            	</{{- cpo_label_tag }}>
        	{{ } }}
            <input
                class="{{- cpo_slug }}-field js-uni-cpo-field-{{- type }}"
                id="{{- cpo_slug }}-field"
                name="{{- cpo_slug }}"
                type="text"
                value=""
				data-slider-type="{{- cpo_range_type }}"
				data-slider-min="{{- cpo_min_val }}"
				data-slider-max="{{- cpo_max_val }}"
				data-slider-step="{{- cpo_step_val }}"
				data-slider-from="{{- cpo_range_from }}"
				data-slider-to="{{- cpo_range_to }}"
				data-slider-grid="{{- cpo_range_grid }}"
				data-slider-prefix="{{- cpo_range_prefix }}"
				data-slider-postfix="{{- cpo_range_postfix }}"
				/>
            </div>
        </script>
		<?php
	}

	public static function template( $data, $post_data = array() ) {
		$id                   = $data['id'];
		$type                 = $data['type'];
		$selectors            = $data['settings']['advanced']['selectors'];
		$cpo_general_main     = $data['settings']['cpo_general']['main'];
		$cpo_general_advanced = $data['settings']['cpo_general']['advanced'];
		$cpo_validation_main  = ( isset( $data['settings']['cpo_validation']['main'] ) )
			? $data['settings']['cpo_validation']['main']
			: array();
		$cpo_validation_logic = ( isset( $data['settings']['cpo_validation']['logic'] ) )
			? $data['settings']['cpo_validation']['logic']
			: array();
		$cpo_label_tag        = $cpo_general_advanced['cpo_label_tag'];
		$attributes           = array( 'data-parsley-trigger' => 'change submit' );

		$wrapper_attributes = array();
		$option             = false;
		$rules_data         = $data['settings']['cpo_conditional']['main'];
		$is_required        = ( 'yes' === $cpo_general_main['cpo_is_required'] ) ? true : false;
		$is_tooltip         = ( 'yes' === $cpo_general_advanced['cpo_is_tooltip'] ) ? true : false;
		$is_enabled         = ( 'yes' === $rules_data['cpo_is_fc'] ) ? true : false;
		$is_hidden          = ( 'hide' === $rules_data['cpo_fc_default'] ) ? true : false;

		$plugin_settings = UniCpo()->get_settings();
		$range_slider_style = $plugin_settings['range_slider_style'];

		if ( ! empty( $data['pid'] ) ) {
			$option = uni_cpo_get_option( $data['pid'] );
		}

		$slug              = ( ! empty( $data['pid'] ) && is_object( $option ) ) ? $option->get_slug() : '';
		$css_id[]          = $slug;
		$css_class         = array(
			'uni-module',
			'uni-module-' . $type,
			'uni-node-' . $id,
			'uni-range-slider-skin-' . $range_slider_style
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

		if ( $is_enabled && $is_hidden ) {
			$wrapper_attributes['style'] = 'display:none;';
			$input_css_class[]           = 'uni-cpo-excluded-field';
		}

		$default_value = ( ! empty( $post_data ) && ! empty( $slug ) && ! empty( $post_data[$slug] ) )
			? $post_data[$slug]
			: $cpo_general_main['cpo_range_from'];

		$attributes['data-type']    = $cpo_general_main['cpo_range_type'];
		$attributes['data-min']     = $cpo_general_main['cpo_min_val'];
		$attributes['data-max']     = $cpo_general_main['cpo_max_val'];
		$attributes['data-step']    = $cpo_general_main['cpo_step_val'];
		$attributes['data-from']    = $default_value;
		$attributes['data-to']      = $cpo_general_main['cpo_range_to'];
		$attributes['data-grid']    = ( ! empty( $cpo_general_main['cpo_range_grid'] ) && 'yes' === $cpo_general_main['cpo_range_grid'] ) ? true : false;
		$attributes['data-prefix']  = $cpo_general_main['cpo_range_prefix'];
		$attributes['data-postfix'] = $cpo_general_main['cpo_range_postfix'];
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
			<?php esc_html_e( $cpo_general_advanced['cpo_label'] ); ?>
			<?php if ( $is_tooltip && $cpo_general_advanced['cpo_tooltip'] !== '' ) { ?>
                <span class="uni-cpo-tooltip"
                      data-tip="<?php echo uni_cpo_sanitize_tooltip( $cpo_general_advanced['cpo_tooltip'] ); ?>"></span>
			<?php } ?>
            </<?php echo esc_attr( $cpo_label_tag ); ?>>
		<?php } ?>
        <input
                class="<?php echo implode( ' ', array_map( function ( $el ) {
					return esc_attr( $el );
				}, $input_css_class ) ); ?>"
                id="<?php echo esc_attr( $slug ); ?>-field"
                name="<?php echo esc_attr( $slug ); ?>"
                type="text"
                value=""
			<?php echo self::get_custom_attribute_html( $attributes ); ?> />
        </div>
		<?php

		self::conditional_rules( $data );
		self::validation_rules( $data, $attributes );
	}

	public static function get_css( $data ) {
		$id         = $data['id'];
		$margin     = $data['settings']['advanced']['layout']['margin'];
		$padding    = $data['settings']['advanced']['layout']['padding'];
		$color      = $data['settings']['style']['text']['color'];
		$background = $data['settings']['style']['background'];
		$border     = $data['settings']['style']['border'];

		ob_start();
		?>
        .uni-node-<?php echo esc_attr( $id ); ?> {
		<?php if ( ! empty( $margin['top'] ) ) { ?> margin-top: <?php echo esc_attr( "{$margin['top']}{$margin['unit']}" ) ?>; <?php } ?>
		<?php if ( ! empty( $margin['bottom'] ) ) { ?> margin-bottom: <?php echo esc_attr( "{$margin['bottom']}{$margin['unit']}" ) ?>; <?php } ?>
		<?php if ( ! empty( $margin['left'] ) ) { ?> margin-left: <?php echo esc_attr( "{$margin['left']}{$margin['unit']}" ) ?>; <?php } ?>
		<?php if ( ! empty( $margin['right'] ) ) { ?> margin-right: <?php echo esc_attr( "{$margin['right']}{$margin['unit']}" ) ?>; <?php } ?>
        }
        .uni-node-<?php echo esc_attr( $id ); ?> > .irs  {
		<?php if ( ! empty( $padding['top'] ) ) { ?> margin-top: <?php echo esc_attr( "{$padding['top']}{$padding['unit']}" ) ?>; <?php } ?>
		<?php if ( ! empty( $padding['bottom'] ) ) { ?> margin-bottom: <?php echo esc_attr( "{$padding['bottom']}{$padding['unit']}" ) ?>; <?php } ?>
		<?php if ( ! empty( $padding['left'] ) ) { ?> margin-left: <?php echo esc_attr( "{$padding['left']}{$padding['unit']}" ) ?>; <?php } ?>
		<?php if ( ! empty( $padding['right'] ) ) { ?> margin-right: <?php echo esc_attr( "{$padding['right']}{$padding['unit']}" ) ?>; <?php } ?>
        }

        .uni-node-<?php echo esc_attr( $id ); ?> .irs-from,
        .uni-node-<?php echo esc_attr( $id ); ?> .irs-to,
        .uni-node-<?php echo esc_attr( $id ); ?> .irs-single,
        .uni-node-<?php echo esc_attr( $id ); ?> .irs-bar,
        .uni-node-<?php echo esc_attr( $id ); ?> .irs-bar-edge,
        .uni-node-<?php echo esc_attr( $id ); ?>.uni-range-slider-skin-flat .irs-slider.from:before,
        .uni-node-<?php echo esc_attr( $id ); ?>.uni-range-slider-skin-flat .irs-slider.to:before,
        .uni-node-<?php echo esc_attr( $id ); ?>.uni-range-slider-skin-flat .irs-slider.single:before {
		<?php if ( ! empty( $background['color_from'] ) && empty( $background['color_to'] ) ) { ?> background: <?php esc_attr_e( $background['color_from'] ) ?>; <?php } elseif ( empty( $background['color_from'] ) && ! empty( $background['color_to'] ) ) { ?> background: <?php echo esc_attr( $background['color_to'] ) ?>;  <?php } else { ?> background: linear-gradient(<?php echo esc_attr( $background['color_from'] ) ?>, <?php echo esc_attr( $background['color_to'] ) ?>); <?php } ?>
		<?php if ( ! empty( $color ) ) { ?> color: <?php echo esc_attr( $color ) ?>; <?php } ?>
        }
        .uni-node-<?php echo esc_attr( $id ); ?>.uni-range-slider-skin-nice .irs-slider.from:hover:before,
        .uni-node-<?php echo esc_attr( $id ); ?>.uni-range-slider-skin-nice .irs-slider.to:hover:before,
        .uni-node-<?php echo esc_attr( $id ); ?>.uni-range-slider-skin-nice .irs-slider.single:hover:before,
        .uni-node-<?php echo esc_attr( $id ); ?>.uni-range-slider-skin-nice .irs-slider.from.state_hover:before,
        .uni-node-<?php echo esc_attr( $id ); ?>.uni-range-slider-skin-nice .irs-slider.to.state_hover:before,
        .uni-node-<?php echo esc_attr( $id ); ?>.uni-range-slider-skin-nice .irs-slider.single.state_hover:before {
		<?php if ( ! empty( $background['color_hover'] ) ) { ?> background: <?php echo esc_attr( $background['color_hover'] ) ?>; <?php } ?>
        }
        .uni-node-<?php echo esc_attr( $id ); ?>.uni-range-slider-skin-html5 .irs-bar,
        .uni-node-<?php echo esc_attr( $id ); ?>.uni-range-slider-skin-html5 .irs-bar-edge {
		<?php if ( ! empty( $border['color_top'] ) ) { ?> border-top-color: <?php echo esc_attr( $border['color_top'] ) ?>; <?php } ?>
		<?php if ( ! empty( $border['color_bottom'] ) ) { ?> border-bottom-color <?php echo esc_attr( $border['color_bottom'] ) ?>; <?php } ?>
        }
        .uni-node-<?php echo esc_attr( $id ); ?>.uni-range-slider-skin-html5 .irs-bar-edge {
		<?php if ( ! empty( $border['color_top'] ) ) { ?> border-left-color: <?php echo esc_attr( $border['color_top'] ) ?>; <?php } ?>
        }
        .uni-node-<?php echo esc_attr( $id ); ?> .irs-from:after,
        .uni-node-<?php echo esc_attr( $id ); ?> .irs-to:after,
        .uni-node-<?php echo esc_attr( $id ); ?> .irs-single:after {
		<?php if ( ! empty( $background['color_to'] ) ) { ?> border-top-color: <?php echo esc_attr( $background['color_to'] ) ?>; <?php } else { ?> border-top-color: <?php echo esc_attr( $background['color_from'] ) ?>; <?php } ?>
        }
		<?php
		return ob_get_clean();
	}

	public function calculate( $form_data ) {
		$post_name = trim( $this->get_slug(), '{}' );

		if ( ! empty( $form_data[ $post_name ] ) ) {

			if ( ! isset( $form_data[ $post_name . '_to' ] ) ) {
				$cpo_general = $this->get_cpo_general();
				$range_type  = $cpo_general['main']['cpo_range_type'];

				if ( 'double' === $range_type ) {
					$values = explode( '-', $form_data[ $post_name ] );
					$from   = $values[0];
					$to     = ( isset( $values[1] ) ) ? $values[1] : $values[0];
				} else {
					$from = $form_data[ $post_name ];
					$to   = 0;
				}
			} else {
				if ( isset( $form_data[ $post_name . '_to' ] ) ) {
					$from = $form_data[ $post_name . '_from' ];
					$to   = $form_data[ $post_name . '_to' ];
				} else {
					$from = $form_data[ $post_name . '_from' ];
					$to   = $form_data[ $post_name . '_from' ];
                }
			}

			$price = $this->get_cpo_rate();
			if ( ! empty( $price ) ) {
				return array(
					$post_name           => array(
						'calc'       => $price,
						'cart_meta'  => $form_data[ $post_name ],
						'order_meta' => $form_data[ $post_name ]
					),
					$post_name . '_from' => array(
						'calc'       => $from,
						'cart_meta'  => $from,
						'order_meta' => $from
					),
					$post_name . '_to'   => array(
						'calc'       => $to,
						'cart_meta'  => $to,
						'order_meta' => $to
					)
				);
			} else {
				return array(
					$post_name           => array(
						'calc'       => floatval( $form_data[ $post_name ] ),
						'cart_meta'  => $form_data[ $post_name ],
						'order_meta' => $form_data[ $post_name ]
					),
					$post_name . '_from' => array(
						'calc'       => $from,
						'cart_meta'  => $from,
						'order_meta' => $from
					),
					$post_name . '_to'   => array(
						'calc'       => $to,
						'cart_meta'  => $to,
						'order_meta' => $to
					)
				);
			}
		} else {
			return array(
				$post_name           => array(
					'calc'       => 0,
					'cart_meta'  => '',
					'order_meta' => ''
				),
				$post_name . '_from' => array(
					'calc'       => 0,
					'cart_meta'  => '',
					'order_meta' => ''
				),
				$post_name . '_to'   => array(
					'calc'       => 0,
					'cart_meta'  => '',
					'order_meta' => ''
				)
			);
		}
	}

}
