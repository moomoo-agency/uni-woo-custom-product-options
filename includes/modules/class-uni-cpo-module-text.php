<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Module_Text class
*
*/

class Uni_Cpo_Module_Text extends Uni_Cpo_Module implements Uni_Cpo_Module_Interface {

	/**
	 * Constructor gets the post object and sets the ID for the loaded option.
	 *
	 */
	public function __construct( $module = 0 ) {

		parent::__construct( $module );

	}

	public static function get_type(){
		return 'text';
	}

	public static function get_title(){
		return __('Paragraph', 'uni-cpo');
	}

	public static function get_settings() {
		return array(
			'settings' => array(
				'general' => array(
					'status' =>array(
						'sync' => array(
							'type' => 'none'
						),
					),
					'main' =>array(
						'content' => __('Some text here', 'uni-cpo'),
					)
				),
				'style' => array(
					'text' => array(
                        'color' => '',
                        'text_align' => ''
                    ),
                    'font' => array(
                        'font_family' => 'inherit',
                        'font_style' => 'inherit',
                        'font_weight' => '',
                        'font_size' => array(
                            'value' => '14',
                            'unit' => 'px'
                        ),
                        'letter_spacing' => '',
                        'line_height' => ''
                    ),
				),
				'advanced' => array(
                    'layout' => array(
                        'margin' => array(
                            'top' => 0,
                            'right' => 0,
                            'bottom' => 0,
                            'left' => 0,
                            'unit' => 'px'
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
                        'id_name' => '',
                        'class_name' => ''
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
            {{ const { content } = data.settings.general.main; }}
            {{ const { color, text_align } = data.settings.style.text; }}
            {{ const { font_family, font_style, font_weight, font_size, letter_spacing, line_height } = data.settings.style.font; }}
            {{ const { margin, padding } = data.settings.advanced.layout; }}
            <div class="uni-module uni-module-{{= type }}" data-node="{{= id }}" data-type="{{= type }}">
            	<style>
                    .uni-node-{{= id }} {
                        {{ if ( color !== '' ) { }} color: {{= color }}; {{ } }}
                        {{ if ( text_align !== '' ) { }} text-align: {{= text_align }}; {{ } }}
                        {{ if ( font_family !== 'inherit' ) { }} font-family: {{= font_family }}; {{ } }}
                        {{ if ( font_style !== 'inherit' ) { }} font-style: {{= font_style }}; {{ } }}
                        {{ if ( font_size.value !== '' ) { }} font-size: {{= font_size.value+font_size.unit }}; {{ } }}
                        {{ if ( font_weight !== '' ) { }} font-weight: {{= font_weight }}; {{ } }}
                        {{ if ( letter_spacing !== '' ) { }} letter-spacing: {{= letter_spacing+'em' }}; {{ } }}
                        {{ if ( line_height !== '' ) { }} line-height: {{= line_height+'px' }}; {{ } }}
                        {{ if ( margin.top !== '' ) { }} margin-top: {{= margin.top + margin.unit }}; {{ } }}
                        {{ if ( margin.bottom !== '' ) { }} margin-bottom: {{= margin.bottom + margin.unit }}; {{ } }}
                        {{ if ( margin.left !== '' ) { }} margin-left: {{= margin.left + margin.unit }}; {{ } }}
                        {{ if ( margin.right !== '' ) { }} margin-right: {{= margin.right + margin.unit }}; {{ } }}
                        {{ if ( padding.top !== '' ) { }} padding-top: {{= padding.top + padding.unit }}; {{ } }}
                        {{ if ( padding.bottom !== '' ) { }} padding-bottom: {{= padding.bottom + padding.unit }}; {{ } }}
                        {{ if ( padding.left !== '' ) { }} padding-left: {{= padding.left + padding.unit }}; {{ } }}
                        {{ if ( padding.right !== '' ) { }} padding-right: {{= padding.right + padding.unit }}; {{ } }}
                    }
                </style>
                <div {{ if ( id_name !== '' ) { }} id="{{- id_name }}" {{ } }} class="uni-node-{{- id }} {{ if ( class_name !== '' ) { }}{{- class_name }}{{ } }}">{{= content }}</div>
            </div>
        </script>
		<?php
	}

	public static function template( $data ) {
		$id        = $data['id'];
		$type      = $data['type'];
		$content   = $data['settings']['general']['main']['content'];
		$selectors = $data['settings']['advanced']['selectors'];

		$css_id    = array();
		$css_class         = array(
			'uni-module',
			'uni-module-' . $type,
			'uni-node-' . $id
		);
		if ( ! empty( $selectors['id_name'] ) ) {
			array_push( $css_id, $selectors['id_name'] );
		}
		if ( ! empty( $selectors['class_name'] ) ) {
			array_push( $css_class, $selectors['class_name'] );
		}
		?>
        <div
                id="<?php echo implode( ' ', array_map( function ( $el ) {
					return esc_attr( $el );
				}, $css_id ) ); ?>"
                class="<?php echo implode( ' ', array_map( function ( $el ) {
					return esc_attr( $el );
				}, $css_class ) ); ?>"><?php echo uni_cpo_sanitize_text( $content ); ?></div>
		<?php
	}

	public static function get_css( $data ) {
		$id      = $data['id'];
		$text    = $data['settings']['style']['text'];
		$font    = $data['settings']['style']['font'];
		$margin  = $data['settings']['advanced']['layout']['margin'];
		$padding = $data['settings']['advanced']['layout']['padding'];

		ob_start();
		?>
        .uni-node-<?php echo esc_attr( $id ); ?> {
			<?php if ( $text['color'] !== '' ) { ?> color: <?php echo esc_attr( $text['color'] ); ?>;<?php } ?>
			<?php if ( $text['text_align'] !== '' ) { ?> text-align: <?php echo esc_attr( $text['text_align'] ); ?>;<?php } ?>
			<?php if ( $font['font_family'] !== 'inherit' ) { ?> font-family: <?php echo esc_attr( $font['font_family'] ); ?>;<?php } ?>
			<?php if ( $font['font_style'] !== 'inherit' ) { ?> font-style: <?php echo esc_attr( $font['font_style'] ); ?>;<?php } ?>
			<?php if ( $font['font_weight'] !== '' ) { ?> font-weight: <?php echo esc_attr( $font['font_weight'] ); ?>;<?php } ?>
			<?php if ( $font['font_size']['value'] !== '' ) { ?> font-size: <?php echo esc_attr( "{$font['font_size']['value']}{$font['font_size']['unit']}" ) ?>; <?php } ?>
			<?php if ( $font['letter_spacing'] !== '' ) { ?> letter-spacing: <?php echo esc_attr( $font['letter_spacing'] ); ?>em;<?php } ?>
			<?php if ( $font['line_height'] !== '' ) { ?> line-height: <?php echo esc_attr( $font['line_height'] ); ?>px;<?php } ?>
			<?php if ( $margin['top'] !== '' ) { ?> margin-top: <?php echo esc_attr( "{$margin['top']}{$margin['unit']}" ) ?>; <?php } ?>
			<?php if ( $margin['bottom'] !== '' ) { ?> margin-bottom: <?php echo esc_attr( "{$margin['bottom']}{$margin['unit']}" ) ?>; <?php } ?>
			<?php if ( $margin['left'] !== '' ) { ?> margin-left: <?php echo esc_attr( "{$margin['left']}{$margin['unit']}" ) ?>; <?php } ?>
			<?php if ( $margin['right'] !== '' ) { ?> margin-right: <?php echo esc_attr( "{$margin['right']}{$margin['unit']}" ) ?>; <?php } ?>
			<?php if ( $padding['top'] !== '' ) { ?> padding-top: <?php echo esc_attr( "{$padding['top']}{$padding['unit']}" ) ?>; <?php } ?>
			<?php if ( $padding['bottom'] !== '' ) { ?> padding-bottom: <?php echo esc_attr( "{$padding['bottom']}{$padding['unit']}" ) ?>; <?php } ?>
			<?php if ( $padding['left'] !== '' ) { ?> padding-left: <?php echo esc_attr( "{$padding['left']}{$padding['unit']}" ) ?>; <?php } ?>
			<?php if ( $padding['right'] !== '' ) { ?> padding-right: <?php echo esc_attr( "{$padding['right']}{$padding['unit']}" ) ?>; <?php } ?>
        }

		<?php
		return ob_get_clean();
	}

}

?>
