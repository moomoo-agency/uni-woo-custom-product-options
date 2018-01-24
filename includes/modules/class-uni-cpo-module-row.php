<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Row class
*
*/

class Uni_Cpo_Module_Row extends Uni_Cpo_Module implements Uni_Cpo_Module_Interface {

	/**
	 * Constructor gets the post object and sets the ID for the loaded option.
	 *
	 */
	public function __construct( $module = 0 ) {

		parent::__construct( $module );

	}

	public static function get_type(){
		return 'row';
	}

	public static function get_title(){
		return __('Row', 'uni-cpo');
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
                        'width_type' => 'auto',
                        'width' => array(
                            'value' => '',
                            'unit' => 'px'
                        ),
                        /*'content_width' => 'fixed-width',
                        'height_type' => 'auto',
                        'height' => array(
                            'value' => '',
                            'unit' => 'px'
                        ),
                        'vertical_align' => ''*/
                    )
                ),
                'style' => array(
                    'text' => array(
                        'color' => '#333333',
                        'text_align' => '',
                    ),
                    'font' => array(
                        'font_family' => 'inherit',
                        'font_style' => 'inherit',
                        'font_weight' => '',
                        'font_size' => array(
                            'value' => '',
                            'unit' => 'px'
                        ),
                        'letter_spacing' => '',
                        'line_height' => ''
                    ),
                    'links' => array(
                        'color' => '',
                        'color_hover' => ''
                    ),
                    'background' => array(
                        'background_type' => '',
                        'background_color' => '',
                        'background_image' => array(
                            'url' => '',
                            'id' => 0,
                            'repeat' => '',
                            'position' => '',
                            'attachment' => '',
                            'size' => ''
                        )
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
                        )
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
		<script id="js-builderius-row-tmpl" type="text/template">
            {{ const { id_name, class_name } = settings.advanced.selectors; }}
            {{ const { general, style, advanced, cpo_general, cpo_suboptions, cpo_conditional } = settings; }}
            {{ const { width_type, width, content_width } = general.main; }}
            {{ const { font_family, font_style, font_weight, font_size, letter_spacing, line_height } = style.font; }}
            {{ const { color, text_align } = style.text; }}
            
            {{ const color_hover = uniGet(style, 'links.color_hover', ''); }}

            {{ const { background_type, background_color, background_image } = style.background; }}
            {{ const { border_top, border_bottom, border_left, border_right } = style.border; }}
            {{ const { margin, padding } = advanced.layout; }}
            <div id="{{- id_name }}" class="uni-row uni-node-{{= id }} {{- class_name }}" data-node="{{= id }}" data-type="{{= type }}">
                <style>
                    .uni-node-{{= id }} .uni-row-content-wrap {
                        {{ if ( width_type == 'custom' ) { }} width: {{= width.value+width.unit }}; {{ } }}
                        {{ if ( color ) { }} color: {{= color }}; {{ } }}
                        {{ if ( text_align ) { }} text-align: {{= text_align }}; {{ } }}
                        {{ if ( font_family !== 'inherit' ) { }} font-family: {{= font_family }}; {{ } }}
                        {{ if ( font_style !== 'inherit' ) { }} font-style: {{= font_style }}; {{ } }}
                        {{ if ( font_size.value ) { }} font-size: {{= font_size.value+font_size.unit }}; {{ } }}
                        {{ if ( font_weight ) { }} font-weight: {{= font_weight }}; {{ } }}
                        {{ if ( letter_spacing ) { }} letter-spacing: {{= letter_spacing+'em' }}; {{ } }}
                        {{ if ( line_height ) { }} line-height: {{= line_height+'px' }}; {{ } }} 

                        {{ if ( background_type == 'color' && background_color ) { }} background-color: {{= background_color }}; {{ } }} 
                        {{ if ( background_type == 'image' && background_image.url ) { }} 
                            background-image: url({{= background_image.url }}); 
                            background-repeat: {{= background_image.repeat }}; 
                            background-position: {{= background_image.position }}; 
                            background-attachment: {{= background_image.attachment }}; 
                            background-size: {{= background_image.size }};
                        {{ } }} 

                        {{ if ( border_top.style !== 'none' && border_top.color ) { }} border-top: {{= border_top.width + 'px '+ border_top.style +' '+ border_top.color }}; {{ } }}
                        {{ if ( border_bottom.style !== 'none' && border_bottom.color ) { }} border-bottom: {{= border_bottom.width + 'px '+ border_bottom.style +' '+ border_bottom.color }}; {{ } }}
                        {{ if ( border_left.style !== 'none' && border_left.color ) { }} border-left: {{= border_left.width + 'px '+ border_left.style +' '+ border_left.color }}; {{ } }}
                        {{ if ( border_right.style !== 'none' && border_right.color ) { }} border-right: {{= border_right.width + 'px '+ border_right.style +' '+ border_right.color }}; {{ } }}
                        {{ if ( margin.top ) { }} margin-top: {{= margin.top + margin.unit }}; {{ } }}
                        {{ if ( margin.bottom ) { }} margin-bottom: {{= margin.bottom + margin.unit }}; {{ } }}
                        {{ if ( margin.left ) { }} margin-left: {{= margin.left + margin.unit }}; {{ } }}
                        {{ if ( margin.right ) { }} margin-right: {{= margin.right + margin.unit }}; {{ } }}

                        {{ if ( padding.top ) { }} padding-top: {{= padding.top + padding.unit }}; {{ } }}
                        {{ if ( padding.bottom ) { }} padding-bottom: {{= padding.bottom + padding.unit }}; {{ } }}
                        {{ if ( padding.left ) { }} padding-left: {{= padding.left + padding.unit }}; {{ } }}
                        {{ if ( padding.right ) { }} padding-right: {{= padding.right + padding.unit }}; {{ } }}
                    }
                    {{ if ( settings.style.links.color ) { }}
                        .uni-node-{{= id }} .uni-row-content-wrap a, .uni-node-{{= id }} .uni-row-content-wrap a:focus, .uni-node-{{= id }} .uni-row-content-wrap a:active { color: {{= settings.style.links.color }}; }
                    {{ } }}
                    {{ if ( color_hover ) { }}
                        .uni-node-{{= id }} .uni-row-content-wrap a:hover { color: {{= color_hover }}; }
                    {{ } }}
                </style>
                <div class="uni-row-content-wrap">
                    <div id="js-row-group-{{= id }}" class="uni-row-content uni-row-content-{{= content_width }} uni-node-content"></div>
                </div>
            </div>
		</script>
		<?php
	}

	public static function template( $data, $post_data = array() ) {
		$id        = $data['id'];
		$selectors = $data['settings']['advanced']['selectors'];
		$main      = $data['settings']['general']['main'];

		$css_id    = array();
		$css_class = array(
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
				}, $css_class ) ); ?>">
            <div class="uni-row-content-wrap">
                <!--<?php if ( isset( $main['content_width'] ) ) { ?>  uni-row-content-<?php echo esc_attr( $main['content_width'] ) ?><?php } ?> -->
                <div class="uni-row-content uni-node-content">
					<?php
					if ( ! empty( $data['columns'] ) ) {
						foreach ( $data['columns'] as $column_key => $column_data ) {
							$column_class = UniCpo()->module_factory::get_classname_from_module_type( $column_data['type'] );
							call_user_func( $column_class . '::template', $column_data, $post_data );
						}
					}
					?>
                </div>
            </div>
        </div>
		<?php
	}

	public static function get_css( $data ) {
		$id            = $data['id'];
		$main          = $data['settings']['general']['main'];
		$text          = $data['settings']['style']['text'];
		$font          = $data['settings']['style']['font'];
		$links         = $data['settings']['style']['links'];
		$background    = $data['settings']['style']['background'];
		$border_top    = $data['settings']['style']['border']['border_top'];
		$border_bottom = $data['settings']['style']['border']['border_bottom'];
		$border_left   = $data['settings']['style']['border']['border_left'];
		$border_right  = $data['settings']['style']['border']['border_right'];
		$margin        = $data['settings']['advanced']['layout']['margin'];
		$padding       = $data['settings']['advanced']['layout']['padding'];

		ob_start();
		?>
        .uni-node-<?php echo esc_attr( $id ); ?> .uni-row-content-wrap {
		<?php if ( $main['width_type'] === 'custom' ) { ?> width: <?php echo esc_attr( "{$main['width']['value']}{$main['width']['unit']}" ) ?>; <?php } ?>
		<?php if ( $text['color'] ) { ?> color: <?php echo esc_attr( $text['color'] ); ?>;<?php } ?>
		<?php if ( $text['text_align'] ) { ?> text-align: <?php echo esc_attr( $text['text_align'] ); ?>;<?php } ?>
		<?php if ( $font['font_family'] !== 'inherit' ) { ?> font-family: <?php echo esc_attr( $font['font_family'] ); ?>;<?php } ?>
		<?php if ( $font['font_style'] !== 'inherit' ) { ?> font-style: <?php echo esc_attr( $font['font_style'] ); ?>;<?php } ?>
		<?php if ( $font['font_weight'] ) { ?> font-weight: <?php echo esc_attr( $font['font_weight'] ); ?>;<?php } ?>
		<?php if ( $font['font_size']['value'] ) { ?> font-size: <?php echo esc_attr( "{$font['font_size']['value']}{$font['font_size']['unit']}" ) ?>; <?php } ?>
		<?php if ( $font['letter_spacing'] ) { ?> letter-spacing: <?php echo esc_attr( $font['letter_spacing'] ); ?>em;<?php } ?>
		<?php if ( $font['line_height'] ) { ?> line-height: <?php echo esc_attr( $font['line_height'] ); ?>px;<?php } ?>
		<?php if ( $background['background_type'] === 'color' && $background['background_color'] ) { ?> background-color: <?php echo esc_attr( $background['background_color'] ); ?>; <?php } ?>
		<?php if ( $background['background_type'] === 'image' && $background['background_image']['url'] ) { ?>
            background-image:url( <?php echo esc_attr( $background['background_image']['url'] ); ?> );
            background-repeat: <?php echo esc_attr( $background['background_image']['repeat'] ); ?>;
            background-position: <?php echo esc_attr( $background['background_image']['position'] ); ?>;
            background-attachment: <?php echo esc_attr( $background['background_image']['attachment'] ); ?>;
            background-size: <?php echo esc_attr( $background['background_image']['size'] ); ?>;
		<?php } ?>
		<?php if ( $border_top['style'] !== 'none' && $border_top['color'] ) { ?> border-top: <?php echo esc_attr( "{$border_top['width']}px {$border_top['style']} {$border_top['color']}" ) ?>; <?php } ?>
		<?php if ( $border_bottom['style'] !== 'none' && $border_bottom['color'] ) { ?> border-bottom: <?php echo esc_attr( "{$border_bottom['width']}px {$border_bottom['style']} {$border_bottom['color']}" ) ?>; <?php } ?>
		<?php if ( $border_left['style'] !== 'none' && $border_left['color'] ) { ?> border-left: <?php echo esc_attr( "{$border_left['width']}px {$border_left['style']} {$border_left['color']}" ) ?>; <?php } ?>
		<?php if ( $border_right['style'] !== 'none' && $border_right['color'] ) { ?> border-right: <?php echo esc_attr( "{$border_right['width']}px {$border_right['style']} {$border_right['color']}" ) ?>; <?php } ?>
		<?php if ( $margin['top'] ) { ?> margin-top: <?php echo esc_attr( "{$margin['top']}{$margin['unit']}" ) ?>; <?php } ?>
		<?php if ( $margin['bottom'] ) { ?> margin-bottom: <?php echo esc_attr( "{$margin['bottom']}{$margin['unit']}" ) ?>; <?php } ?>
		<?php if ( $margin['left'] ) { ?> margin-left: <?php echo esc_attr( "{$margin['left']}{$margin['unit']}" ) ?>; <?php } ?>
		<?php if ( $margin['right'] ) { ?> margin-right: <?php echo esc_attr( "{$margin['right']}{$margin['unit']}" ) ?>; <?php } ?>
		<?php if ( $padding['top'] ) { ?> padding-top: <?php echo esc_attr( "{$padding['top']}{$padding['unit']}" ) ?>; <?php } ?>
		<?php if ( $padding['bottom'] ) { ?> padding-bottom: <?php echo esc_attr( "{$padding['bottom']}{$padding['unit']}" ) ?>; <?php } ?>
		<?php if ( $padding['left'] ) { ?> padding-left: <?php echo esc_attr( "{$padding['left']}{$padding['unit']}" ) ?>; <?php } ?>
		<?php if ( $padding['right'] ) { ?> padding-right: <?php echo esc_attr( "{$padding['right']}{$padding['unit']}" ) ?>; <?php } ?>
        }
		<?php if ( $links['color'] ) { ?>
            .uni-node-<?php echo esc_attr( $id ); ?> .uni-row-content-wrap a, .uni-node-<?php echo esc_attr( $id ); ?> .uni-row-content-wrap a:focus, .uni-node-<?php echo esc_attr( $id ); ?> .uni-row-content-wrap a:active {
            color:<?php echo esc_attr( $links['color'] ); ?>;
            }
		<?php } ?>
		<?php if ( isset( $links['color_hover'] ) ) { ?>
            .uni-node-<?php echo esc_attr( $id ); ?> .uni-row-content-wrap a:hover {
            color:<?php echo esc_attr( $links['color_hover'] ); ?>;
            }
		<?php } ?>

		<?php

		return ob_get_clean();
	}

}

?>