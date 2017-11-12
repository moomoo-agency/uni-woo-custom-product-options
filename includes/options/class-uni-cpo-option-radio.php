<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Option_Radio class
*
*/

class Uni_Cpo_Option_Radio extends Uni_Cpo_Option implements Uni_Cpo_Option_Interface {

	/**
	 * Stores extra (specific to this) option data.
	 *
	 * @var array
	 */
	protected $extra_data = array(
		'cpo_suboptions' => array()
	);

	/**
	 * Constructor gets the post object and sets the ID for the loaded option.
	 *
	 */
	public function __construct( $option = 0 ) {

		parent::__construct( $option );

	}

	public static function get_type() {
		return 'radio';
	}

	public static function get_title() {
		return __( 'Radio Inputs', 'uni-cpo' );
	}

	/**
	 * Returns an array of special vars associated with the option
	 *
	 * @return array
	 */
	public static function get_special_vars() {
		return array();
	}

	/**
	 * Returns an array of data used in js query builder
	 *
	 * @return array
	 */
	public static function get_filter_data() {
		$operators = array(
			'equal',
			'not_equal',
			'is_empty',
			'is_not_empty'
		);

		return array(
			'input'        => 'select',
			'operators'    => $operators,
			'special_vars' => array()
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
	public function get_cpo_suboptions( $context = 'view' ) {
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
	public function set_cpo_suboptions( $options ) {
		$this->set_prop( 'cpo_suboptions', $options );
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
		$model['settings']['cpo_suboptions']                  = $this->get_cpo_suboptions();
		$model['settings']['cpo_conditional']                 = $this->get_cpo_conditional();

		return $model;
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
					)
				),
				'style'           => array(
					'font' => array(
						'color'          => '#333333',
						'text_align'     => '',
						'font_family'    => 'inherit',
						'font_style'     => 'inherit',
						'font_weight'    => '400',
						'font_size'      => array(
							'value' => '14',
							'unit'  => 'px'
						),
						'letter_spacing' => ''
					)
				),
				'advanced'        => array(
					'selectors' => array(
						'id_name'    => '',
						'class_name' => ''
					)
				),
				'cpo_general'     => array(
					'main'     => array(
						'cpo_slug'        => '',
						'cpo_is_required' => 'no'
					),
					'advanced' => array(
						'cpo_label'        => '',
						'cpo_label_tag'    => 'label',
						'cpo_order_label'  => '',
						'cpo_is_tooltip'   => 'no',
						'cpo_tooltip'      => '',
						'cpo_tooltip_type' => 'classic'
					)
				),
				'cpo_suboptions'  => array(
					'data' => array(
						'cpo_radio_options' => array()
					)
				),
				'cpo_conditional' => array(
					'main' => array(
						'cpo_is_fc'      => 'no',
						'cpo_fc_default' => 'hide',
						'cpo_fc_scheme'  => ''
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
            {{ const { color, text_align, font_family, font_style, font_weight, font_size, letter_spacing } = data.settings.style.font; }}
            {{ const { cpo_slug, cpo_is_required } = data.settings.cpo_general.main; }}
            {{ let radioOptions = []; }}
            {{ if (typeof data.settings.cpo_suboptions.data !== 'undefined') { }}
            {{ radioOptions = data.settings.cpo_suboptions.data.cpo_radio_options; }}
            {{ } }}
            {{ const { cpo_label_tag, cpo_label, cpo_is_tooltip, cpo_tooltip } = data.settings.cpo_general.advanced; }}
            <div
                    id="{{- id_name }}"
                    class="uni-module uni-module-{{- type }} uni-node-{{- id }} {{- class_name }}"
                    data-node="{{- id }}"
                    data-type="{{- type }}">
            <style>
                .uni-node-{{= id }} .uni-cpo-option-label__text {
                    {{ if ( color !== '' ) { }} color: {{= color }}; {{ } }}
                    {{ if ( text_align !== '' ) { }} text-align: {{= text_align }}; {{ } }}
                    {{ if ( font_family !== 'inherit' ) { }} font-family: {{= font_family }}; {{ } }}
                    {{ if ( font_style !== 'inherit' ) { }} font-style: {{= font_style }}; {{ } }}
                    {{ if ( font_size.value !== '' ) { }} font-size: {{= font_size.value+font_size.unit }}; {{ } }}
                    {{ if ( font_weight !== '' ) { }} font-weight: {{= font_weight }}; {{ } }}
                    {{ if ( letter_spacing !== '' ) { }} letter-spacing: {{= letter_spacing+'em' }}; {{ } }}
                }
            </style>
            {{ if ( cpo_label_tag && cpo_label !== '' ) { }}
                <{{- cpo_label_tag }}{{ if ( cpo_is_required === 'yes' ) { }} class="uni_cpo_field_required"{{ } }}>
                	{{- cpo_label }}
                	{{ if ( cpo_is_tooltip === 'yes' && cpo_tooltip !== '' ) { }} <span class="uni-cpo-tooltip" data-tip="{{- cpo_tooltip }}"></span> {{ } }}
            	</{{- cpo_label_tag }}>
        	{{ } }}

            {{ if (radioOptions) { }}
            {{ _.each(radioOptions, function(option) { }}
                <input
                    class="uni_cpo_{{- cpo_slug }}-field js-uni-cpo-field-{{- type }}"
                    {{ if ( option.def === 'checked' ) { }} checked="checked" {{ } }}
                    id="uni_cpo_{{- cpo_slug }}-field-{{- option.slug }}"
                    name="{{- cpo_slug }}"
                    type="radio"
                    value="{{- option.slug }}"/>
                <label for="uni_cpo_{{- cpo_slug }}-field-{{- option.slug }}" class="uni-cpo-option-label uni-cpo-radio-option-label">
                    <span class="uni-cpo-option-label__radio"></span>
                    <span 
                        class="uni-cpo-option-label__text">
                        {{- option.label }}
                    </span>
                    {{ if ( option.attach_uri !== '' ) { }}
                        <img
                            class="uni-cpo-option-label__image"
                            src="{{- option.attach_uri }}"
                            alt="{{- option.attach_name }}" />
                    {{ } }}
                </label>
            {{ }); }}
            {{ } }}
            </div>
        </script>
		<?php
	}

	public static function template( $data ) {
		$id                   = $data['id'];
		$type                 = $data['type'];
		$selectors            = $data['settings']['advanced']['selectors'];
		$suboptions           = ( isset( $data['settings']['cpo_suboptions']['data']['cpo_radio_options'] ) )
			? $data['settings']['cpo_suboptions']['data']['cpo_radio_options']
			: array();
		$cpo_general_main     = $data['settings']['cpo_general']['main'];
		$cpo_general_advanced = $data['settings']['cpo_general']['advanced'];
		$cpo_label_tag        = $cpo_general_advanced['cpo_label_tag'];
		$attributes           = array(
			'data-parsley-trigger'          => 'change focusout submit',
			'data-parsley-errors-container' => '.uni-node-' . $id
		);
		$wrapper_attributes   = array();
		$option               = false;
		$rules_data           = $data['settings']['cpo_conditional']['main'];
		$is_required          = ( 'yes' === $cpo_general_main['cpo_is_required'] ) ? true : false;
		$is_tooltip           = ( 'yes' === $cpo_general_advanced['cpo_is_tooltip'] ) ? true : false;
		$is_enabled           = ( 'yes' === $rules_data['cpo_is_fc'] ) ? true : false;
		$is_hidden            = ( 'hide' === $rules_data['cpo_fc_default'] ) ? true : false;

		if ( ! empty( $data['pid'] ) ) {
			$option = uni_cpo_get_option( $data['pid'] );
		}

		$slug              = ( ! empty( $data['pid'] ) && is_object( $option ) ) ? $option->get_slug() : '';
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

		if ( 'yes' === $cpo_general_main['cpo_is_required'] ) {
			$attributes['data-parsley-required'] = 'true';
		}

		if ( $is_enabled && $is_hidden ) {
			$wrapper_attributes['style'] = 'display:none;';
			$input_css_class[]           = 'uni-cpo-excluded-field';
		}
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
            <<?php esc_attr_e( $cpo_label_tag ); ?><?php if ( $is_required ) { ?> class="uni_cpo_field_required" <?php } ?>>
			<?php esc_html_e( $cpo_general_advanced['cpo_label'] ); ?>
			<?php if ( $is_tooltip && $cpo_general_advanced['cpo_tooltip'] !== '' ) { ?>
                <span class="uni-cpo-tooltip"
                      data-tip="<?php esc_html_e( $cpo_general_advanced['cpo_tooltip'] ); ?>"></span>
			<?php } ?>
            </<?php esc_attr_e( $cpo_label_tag ); ?>>
		<?php } ?>
		<?php
		foreach ( $suboptions as $suboption ) : ?>
            <input
                    class="<?php echo implode( ' ', array_map( function ( $el ) {
						return esc_attr( $el );
					}, $input_css_class ) ); ?>"
                    id="<?php esc_attr_e( $slug ); ?>-field-<?php esc_attr_e( $suboption['slug'] ); ?>"
                    name="<?php esc_attr_e( $slug ); ?>"
                    type="radio"
                    value="<?php esc_attr_e( $suboption['slug'] ); ?>"
				<?php echo self::get_custom_attribute_html( $attributes ); ?>
				<?php checked( $suboption['def'], 'checked' ) ?> />
            <label
                    for="<?php esc_attr_e( $slug ); ?>-field-<?php esc_attr_e( $suboption['slug'] ); ?>"
                    class="uni-cpo-option-label uni-cpo-radio-option-label">
                <span class="uni-cpo-option-label__radio"></span>
                <span class="uni-cpo-option-label__text"><?php esc_html_e( $suboption['label'] ); ?></span>
				<?php if ( ! empty( $suboption['attach_uri'] ) ) { ?>
                    <img
                            class="uni-cpo-option-label__image"
                            src="<?php echo esc_url( $suboption['attach_uri'] ); ?>"
                            alt="<?php esc_attr_e( $suboption['label'] ); ?>"/>
				<?php } ?>
            </label>
		<?php endforeach; ?>
        </div>
		<?php

		self::conditional_rules( $data );
	}

	public static function get_css( $data ) {
		$id   = $data['id'];
		$font = $data['settings']['style']['font'];


		ob_start();
		?>
        .uni-node-<?php esc_attr_e( $id ); ?> .uni-cpo-option-label__text {
		<?php if ( $font['color'] !== '' ) { ?> color: <?php esc_attr_e( $font['color'] ); ?>;<?php } ?>
		<?php if ( $font['text_align'] !== '' ) { ?> text-align: <?php esc_attr_e( $font['text_align'] ); ?>;<?php } ?>
		<?php if ( $font['font_family'] !== 'inherit' ) { ?> font-family: <?php esc_attr_e( $font['font_family'] ); ?>;<?php } ?>
		<?php if ( $font['font_style'] !== 'inherit' ) { ?> font-style: <?php esc_attr_e( $font['font_style'] ); ?>;<?php } ?>
		<?php if ( $font['font_weight'] !== '' ) { ?> font-weight: <?php esc_attr_e( $font['font_weight'] ); ?>;<?php } ?>
		<?php if ( $font['font_size']['value'] !== '' ) { ?> font-size: <?php esc_attr_e( "{$font['font_size']['value']}{$font['font_size']['unit']}" ) ?>; <?php } ?>
		<?php if ( $font['letter_spacing'] !== '' ) { ?> letter-spacing: <?php esc_attr_e( $font['letter_spacing'] ); ?>em;<?php } ?>
        }

		<?php
		return ob_get_clean();
	}

	public function calculate( $form_data ) {
		$post_name  = trim( $this->get_slug(), '{}' );
		$suboptions = $this->get_cpo_suboptions();

		if ( ! empty( $form_data[ $post_name ] ) ) {
			if ( isset( $suboptions['data']['cpo_radio_options'] ) && ! empty( $suboptions['data']['cpo_radio_options'] ) ) {
				foreach ( $suboptions['data']['cpo_radio_options'] as $k => $v ) {
					if ( ( ( ! empty( $v['slug'] ) ) ? $v['slug'] : '' ) === $form_data[ $post_name ] ) {
						return array(
							$post_name => array(
								'calc'       => ( ! empty( $v['rate'] ) ) ? floatval( $v['rate'] ) : 0,
								'cart_meta'  => $v['slug'],
								'order_meta' => $v['slug']
							)
						);
					}
				}
			}
		} else {
			return array(
				$post_name => array(
					'calc'       => 0,
					'cart_meta'  => '',
					'order_meta' => ''
				)
			);
		}
	}

}
