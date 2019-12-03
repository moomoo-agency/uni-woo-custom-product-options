<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Module_Button class
*
*/

class Uni_Cpo_Module_Button extends Uni_Cpo_Module implements Uni_Cpo_Module_Interface {

	/**
	 * Constructor gets the post object and sets the ID for the loaded option.
	 *
	 */
	public function __construct( $module = 0 ) {

		parent::__construct( $module );

	}

	public static function get_type(){
		return 'button';
	}

	public static function get_title(){
		return __('Button', 'uni-cpo');
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
                    'main' => array(
                        'content' => __('Click here', 'uni-cpo'),
                        'width_type' => '',
                        'width' => array(
                            'value' => '',
                            'unit' => 'px'
                        ),
                        'height' => array(
                            'value' => '20',
                            'unit' => 'px'
                        )
                    ),
                    'link' => array(
                        'href' => '',
                        'target' => '_self',
                        'rel' => 'yes'
                    )
                ),
                'style' => array(
                    'text' => array(
                        'color' => '#ffffff',
                        'color_hover' => '',
                        'text_align' => 'center',
                    ),
                    'font' => array(
                        'font_family' => 'inherit',
                        'font_style' => 'inherit',
                        'font_weight' => '',
                        'font_size' => array(
                            'value' => '16',
                            'unit' => 'px'
                        ),
                        'letter_spacing' => ''
                    ),
                    'background' => array(
                        'background_color' => '#3bc5b6',
                        'background_hover_color' => ''
                    ),
                    'border' => array(
                        'border_unit' => 'px',
                        'border_top' => array(
                            'style' => 'none',
                            'width' => '',
                            'color' => ''
                        ),
                        'border_bottom' => array(
                            'style' => 'none',
                            'width' => '',
                            'color' => ''
                        ),
                        'border_left' => array(
                            'style' => 'none',
                            'width' => '',
                            'color' => ''
                        ),
                        'border_right' => array(
                            'style' => 'none',
                            'width' => '',
                            'color' => ''
                        ),
                        'radius' => array(
                            'value' => '4',
                            'unit' => 'px'
                        ),
                    )
                ),
                'advanced' => array(
                    'layout' => array(
                        'margin' => array(
                            'top' => '',
                            'right' => '',
                            'bottom' => '',
                            'left' => '',
                            'unit' => 'px'
                        ),
                        'padding' => array(
                            'top' => 10,
                            'right' => 20,
                            'bottom' => 10,
                            'left' => 20,
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
            {{ const { general, style, advanced, cpo_general, cpo_suboptions, cpo_conditional } = data.settings; }}
            {{ const { id_name, class_name } = data.settings.advanced.selectors; }}
            {{ const { content, width_type, width, height } = data.settings.general.main; }}
            {{ const { href, target, rel } = data.settings.general.link; }}
            {{ const { color, text_align } = data.settings.style.text; }}
            
            {{ const color_hover = uniGet(style, 'text.color_hover', ''); }}

            {{ const { font_family, font_style, font_weight, font_size, letter_spacing, line_height } = data.settings.style.font; }}
            {{ const { background_color, background_hover_color } = data.settings.style.background; }}
            {{ const { border_top, border_bottom, border_left, border_right, radius } = data.settings.style.border; }}
            {{ const { margin, padding } = data.settings.advanced.layout; }}
            <div class="uni-module uni-module-{{= type }}" data-node="{{= id }}" data-type="{{= type }}">
                <style type="text/css">
                    .uni-node-{{= id }}, .uni-node-{{= id }}:active, .uni-node-{{= id }}:focus {
                        {{ if ( width_type == 'custom' ) { }} width: {{= width.value+width.unit }}; {{ } }}
                        {{ if ( height.value !== '' ) { }} line-height: {{= height.value+height.unit }}; {{ } }}
                        {{ if ( color !== '' ) { }} color: {{= color }}; {{ } }}
                        {{ if ( text_align !== '' ) { }} text-align: {{= text_align }}; {{ } }}
                        {{ if ( font_family !== 'inherit' ) { }} font-family: {{= font_family }}; {{ } }}
                        {{ if ( font_style !== 'inherit' ) { }} font-style: {{= font_style }}; {{ } }}
                        {{ if ( font_size.value !== '' ) { }} font-size: {{= font_size.value+font_size.unit }}; {{ } }}
                        {{ if ( font_weight !== '' ) { }} font-weight: {{= font_weight }}; {{ } }}
                        {{ if ( letter_spacing !== '' ) { }} letter-spacing: {{= letter_spacing+'em' }}; {{ } }}
                        {{ if ( background_color !== '' ) { }} background-color: {{= background_color }}; {{ } }}
                        {{ if ( border_top.style !== 'none' && border_top.color !== '' ) { }} border-top: {{= border_top.width + 'px '+ border_top.style +' '+ border_top.color }}; {{ } }}
                        {{ if ( border_bottom.style !== 'none' && border_bottom.color !== '' ) { }} border-bottom: {{= border_bottom.width + 'px '+ border_bottom.style +' '+ border_bottom.color }}; {{ } }}
                        {{ if ( border_left.style !== 'none' && border_left.color !== '' ) { }} border-left: {{= border_left.width + 'px '+ border_left.style +' '+ border_left.color }}; {{ } }}
                        {{ if ( border_right.style !== 'none' && border_right.color !== '' ) { }} border-right: {{= border_right.width + 'px '+ border_right.style +' '+ border_right.color }}; {{ } }}
                        {{ if ( radius.value !== '' ) { }} border-radius: {{= radius.value + radius.unit }}; {{ } }}
                        {{ if ( margin.top !== '' ) { }} margin-top: {{= margin.top + margin.unit }}; {{ } }}
                        {{ if ( margin.bottom !== '' ) { }} margin-bottom: {{= margin.bottom + margin.unit }}; {{ } }}
                        {{ if ( margin.left !== '' ) { }} margin-left: {{= margin.left + margin.unit }}; {{ } }}
                        {{ if ( margin.right !== '' ) { }} margin-right: {{= margin.right + margin.unit }}; {{ } }}
                        {{ if ( padding.top !== '' ) { }} padding-top: {{= padding.top + padding.unit }}; {{ } }}
                        {{ if ( padding.bottom !== '' ) { }} padding-bottom: {{= padding.bottom + padding.unit }}; {{ } }}
                        {{ if ( padding.left !== '' ) { }} padding-left: {{= padding.left + padding.unit }}; {{ } }}
                        {{ if ( padding.right !== '' ) { }} padding-right: {{= padding.right + padding.unit }}; {{ } }}
                    }
                    {{ if ( color_hover !== '' || background_hover_color !== '' ) { }}
                        .uni-node-{{= id }}:hover { color: {{= color_hover }}!important; background-color: {{= background_hover_color }}; }
                    {{ } }}
                </style>
                <a {{ if ( id_name !== '' ) { }} id="{{- id_name }}" {{ } }} class="uni-link-button uni-node-{{- id }} {{ if ( class_name !== '' ) { }}{{- class_name }}{{ } }}" target="{{= target }}" href="{{= href }}" rel="{{= rel }}">
                    <span class="uni-button-text">{{= content }}</span>
                </a>
            </div>
        </script>
		<?php
	}

	public static function template( $data ) {
		$id        = $data['id'];
        $type      = $data['type'];
		$content   = $data['settings']['general']['main']['content'];
		$link      = $data['settings']['general']['link'];
		$selectors = $data['settings']['advanced']['selectors'];

		$css_id    = array();
		$css_class = array(
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
        <a
                id="<?php echo implode( ' ', array_map( function ( $el ) {
					return esc_attr( $el );
				}, $css_id ) ); ?>"
                class="<?php echo implode( ' ', array_map( function ( $el ) {
					return esc_attr( $el );
				}, $css_class ) ); ?>"
                target="<?php echo esc_attr( $link['target'] ) ?>"
                href="<?php echo esc_attr( $link['href'] ) ?>"
                rel="<?php echo esc_attr( $link['rel'] ) ?>">
            <span class="uni-button-text">
                <?php esc_html_e( $content ) ?>
            </span>
        </a>
		<?php
	}

	public static function get_css( $data ) {
		$id            = $data['id'];
		$main          = $data['settings']['general']['main'];
		$text          = $data['settings']['style']['text'];
		$font          = $data['settings']['style']['font'];
		$background    = $data['settings']['style']['background'];
		$border_unit   = $data['settings']['style']['border']['border_unit'];
		$border_top    = $data['settings']['style']['border']['border_top'];
		$border_bottom = $data['settings']['style']['border']['border_bottom'];
		$border_left   = $data['settings']['style']['border']['border_left'];
		$border_right  = $data['settings']['style']['border']['border_right'];
		$radius        = $data['settings']['style']['border']['radius'];
		$margin        = $data['settings']['advanced']['layout']['margin'];
		$padding       = $data['settings']['advanced']['layout']['padding'];

		ob_start();
		?>
        .uni-node-<?php echo esc_attr( $id ); ?>, .uni-node-<?php echo esc_attr( $id ); ?>:active, .uni-node-<?php echo esc_attr( $id ); ?>:focus {
		<?php if ( $main['width_type'] === 'custom' ) { ?> width: <?php echo esc_attr( "{$main['width']['value']}{$main['width']['unit']}" ) ?>; <?php } ?>
		<?php if ( $main['height']['value'] !== '' ) { ?> line-height: <?php echo esc_attr( "{$main['height']['value']}{$main['height']['unit']}" ) ?>;<?php } ?>
		<?php if ( $text['color'] !== '' ) { ?> color: <?php echo esc_attr( $text['color'] ); ?>;<?php } ?>
		<?php if ( $text['text_align'] !== '' ) { ?> text-align: <?php echo esc_attr( $text['text_align'] ); ?>;<?php } ?>
		<?php if ( $font['font_family'] !== 'inherit' ) { ?> font-family: <?php echo esc_attr( $font['font_family'] ); ?>;<?php } ?>
		<?php if ( $font['font_style'] !== 'inherit' ) { ?> font-style: <?php echo esc_attr( $font['font_style'] ); ?>;<?php } ?>
		<?php if ( $font['font_weight'] !== '' ) { ?> font-weight: <?php echo esc_attr( $font['font_weight'] ); ?>;<?php } ?>
		<?php if ( $font['font_size']['value'] !== '' ) { ?> font-size: <?php echo esc_attr( "{$font['font_size']['value']}{$font['font_size']['unit']}" ) ?>; <?php } ?>
		<?php if ( $font['letter_spacing'] !== '' ) { ?> letter-spacing: <?php echo esc_attr( $font['letter_spacing'] ); ?>em;<?php } ?>
		<?php if ( $background['background_color'] !== '' ) { ?> background-color: <?php echo esc_attr( $background['background_color'] ); ?>;<?php } ?>
		<?php if ( $border_top['style'] !== 'none' && $border_top['color'] !== '' ) { ?> border-top: <?php echo esc_attr( "{$border_top['width']}px {$border_top['style']} {$border_top['color']}" ) ?>; <?php } ?>
		<?php if ( $border_bottom['style'] !== 'none' && $border_bottom['color'] !== '' ) { ?> border-bottom: <?php echo esc_attr( "{$border_bottom['width']}px {$border_bottom['style']} {$border_bottom['color']}" ) ?>; <?php } ?>
		<?php if ( $border_left['style'] !== 'none' && $border_left['color'] !== '' ) { ?> border-left: <?php echo esc_attr( "{$border_left['width']}px {$border_left['style']} {$border_left['color']}" ) ?>; <?php } ?>
		<?php if ( $border_right['style'] !== 'none' && $border_right['color'] !== '' ) { ?> border-right: <?php echo esc_attr( "{$border_right['width']}px {$border_right['style']} {$border_right['color']}" ) ?>; <?php } ?>
		<?php if ( $radius['value'] !== '' ) { ?> border-radius: <?php echo esc_attr( "{$radius['value']}{$radius['unit']}" ) ?>; <?php } ?>
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
		if ( $text['color_hover'] !== '' || $background['background_hover_color'] ) { ?>
            .uni-node-<?php echo esc_attr( $id ); ?>:hover {
            color: <?php echo esc_attr( $text['color_hover'] ); ?>!important;
            background-color: <?php echo esc_attr( $background['background_hover_color'] ); ?>;
            }
		<?php } ?>

		<?php
		return ob_get_clean();
	}
}

?>