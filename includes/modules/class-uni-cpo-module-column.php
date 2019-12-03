<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Column class
*
*/

class Uni_Cpo_Module_Column extends Uni_Cpo_Module implements Uni_Cpo_Module_Interface {

	/**
	 * Constructor gets the post object and sets the ID for the loaded option.
	 *
	 */
	public function __construct( $module = 0 ) {

		parent::__construct( $module );

	}

	public static function get_type(){
		return 'column';
	}

	public static function get_title(){
		return __('Column', 'uni-cpo');
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
                        'width' => array(
                            'value' => '100',
                            'unit' => '%'
                        ),
                        'float' => 'left'
                    )
                ),
                'style' => array(
                    'background' => array(
                        'background_type' => '',
                        'background_color' => '',
                        'background_image' => array(
                            'url' => '',
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
                        ),
                        'radius' => array(
                            'value' => '',
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
		<script id="js-builderius-col-tmpl" type="text/template">
            {{ const { id_name, class_name } = settings.advanced.selectors; }}
            {{ const { width, float } = settings.general.main; }}
            {{ const { background_type, background_color, background_image } = settings.style.background; }}
            {{ const { border_top, border_bottom, border_left, border_right, radius } = settings.style.border; }}
            {{ const { margin, padding } = settings.advanced.layout; }}
			<div id="{{= id_name }}" class="uni-col uni-node-{{= id }} {{= class_name }}" data-node="{{= id }}" data-type="{{= type }}">
                <style>
                    .uni-node-{{= id }} { 
                        {{ if ( float !== 'none' ) { }} float: {{= float }}; {{ } else { }} clear: both; {{ } }}
                        {{ if ( width.value !== '' ) { }} width: {{= width.value + width.unit }}; {{ } }}
                        {{ if ( margin.top !== '' ) { }} margin-top: {{= margin.top + margin.unit }}; {{ } }}
                        {{ if ( margin.bottom !== '' ) { }} margin-bottom: {{= margin.bottom + margin.unit }}; {{ } }}
                        {{ if ( margin.left !== '' ) { }} margin-left: {{= margin.left + margin.unit }}; {{ } }}
                        {{ if ( margin.right !== '' ) { }} margin-right: {{= margin.right + margin.unit }}; {{ } }}
                    }
                    .uni-node-{{= id }} .uni-col-content {
                        {{ if ( background_type == 'color' && background_color !== '' ) { }} background-color: {{= background_color }};
                        {{ } else if ( background_type == 'image' && background_image.url !== '' ) { }}
                            background-image: url({{= background_image.url }}); 
                            background-repeat: {{= background_image.repeat }}; 
                            background-position: {{= background_image.position }}; 
                            background-attachment: {{= background_image.attachment }}; 
                            background-size: {{= background_image.size }};
                        {{ } }}
                        {{ if ( border_top.style !== 'none' && border_top.color !== '' ) { }} border-top: {{= border_top.width + 'px '+ border_top.style +' '+ border_top.color }}; {{ } }}
                        {{ if ( border_bottom.style !== 'none' && border_bottom.color !== '' ) { }} border-bottom: {{= border_bottom.width + 'px '+ border_bottom.style +' '+ border_bottom.color }}; {{ } }}
                        {{ if ( border_left.style !== 'none' && border_left.color !== '' ) { }} border-left: {{= border_left.width + 'px '+ border_left.style +' '+ border_left.color }}; {{ } }}
                        {{ if ( border_right.style !== 'none' && border_right.color !== '' ) { }} border-right: {{= border_right.width + 'px '+ border_right.style +' '+ border_right.color }}; {{ } }}
                        {{ if ( radius.value !== '' ) { }} border-radius: {{= radius.value + radius.unit }}; {{ } }}
                        {{ if ( padding.top !== '' ) { }} padding-top: {{= padding.top + padding.unit }}; {{ } }}
                        {{ if ( padding.bottom !== '' ) { }} padding-bottom: {{= padding.bottom + padding.unit }}; {{ } }}
                        {{ if ( padding.left !== '' ) { }} padding-left: {{= padding.left + padding.unit }}; {{ } }}
                        {{ if ( padding.right !== '' ) { }} padding-right: {{= padding.right + padding.unit }}; {{ } }}
                    }
                </style>
                <div id="js-col-group-{{= id }}" class="uni-col-content uni-node-content"></div>
			</div>
		</script>
		<?php
	}

	public static function template( $data, $post_data = array() ) {
		$id        = $data['id'];
		$selectors = $data['settings']['advanced']['selectors'];

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
            <div class="uni-col-content uni-node-content">
				<?php
				if ( ! empty( $data['modules'] ) ) {
					foreach ( $data['modules'] as $module_key => $module_data ) {
						$module_class = UniCpo()->module_factory::get_classname_from_module_type( $module_data['type'] );
						$option_class = UniCpo()->option_factory::get_classname_from_option_type( $module_data['type'] );
						if ( class_exists( $module_class ) ) {
							call_user_func( $module_class . '::template', $module_data );
						} elseif ( class_exists( $option_class ) ) {
							call_user_func( $option_class . '::template', $module_data, $post_data );
						}
					}
				} ?>
            </div>
        </div>
		<?php
	}

	public static function get_css( $data ) {
		$id            = $data['id'];
		$main          = $data['settings']['general']['main'];
		$background    = $data['settings']['style']['background'];
		$border_top    = $data['settings']['style']['border']['border_top'];
		$border_bottom = $data['settings']['style']['border']['border_bottom'];
		$border_left   = $data['settings']['style']['border']['border_left'];
		$border_right  = $data['settings']['style']['border']['border_right'];
		$radius        = $data['settings']['style']['border']['radius'];
		$margin        = $data['settings']['advanced']['layout']['margin'];
		$padding       = $data['settings']['advanced']['layout']['padding'];

		ob_start();
		?>
        .uni-node-<?php echo esc_attr( $id ); ?> {
		<?php if ( $main['float'] !== '' ) { ?> float: <?php echo esc_attr( $main['float'] ); ?>;<?php } ?>
		<?php if ( $main['width']['value'] !== '' ) { ?> width: <?php echo esc_attr( "{$main['width']['value']}{$main['width']['unit']}" ) ?>; <?php } ?>
		<?php if ( $margin['top'] !== '' ) { ?> margin-top: <?php echo esc_attr( "{$margin['top']}{$margin['unit']}" ) ?>; <?php } ?>
		<?php if ( $margin['bottom'] !== '' ) { ?> margin-bottom: <?php echo esc_attr( "{$margin['bottom']}{$margin['unit']}" ) ?>; <?php } ?>
		<?php if ( $margin['left'] !== '' ) { ?> margin-left: <?php echo esc_attr( "{$margin['left']}{$margin['unit']}" ) ?>; <?php } ?>
		<?php if ( $margin['right'] !== '' ) { ?> margin-right: <?php echo esc_attr( "{$margin['right']}{$margin['unit']}" ) ?>; <?php } ?>
        }
        .uni-node-<?php echo esc_attr( $id ); ?> .uni-col-content {
		<?php if ( $background['background_type'] === 'color' && $background['background_color'] !== '' ) { ?> background-color: <?php echo esc_attr( $background['background_color'] ); ?>; <?php } ?>
		<?php if ( $background['background_type'] === 'image' && $background['background_image']['url'] !== '' ) { ?>
            background-image:url( <?php echo esc_attr( $background['background_image']['url'] ); ?> );
            background-repeat: <?php echo esc_attr( $background['background_image']['repeat'] ); ?>;
            background-position: <?php echo esc_attr( $background['background_image']['position'] ); ?>;
            background-attachment: <?php echo esc_attr( $background['background_image']['attachment'] ); ?>;
            background-size: <?php echo esc_attr( $background['background_image']['size'] ); ?>;
		<?php } ?>
		<?php if ( $border_top['style'] !== 'none' && $border_top['color'] !== '' ) { ?> border-top: <?php echo esc_attr( "{$border_top['width']}px {$border_top['style']} {$border_top['color']}" ) ?>; <?php } ?>
		<?php if ( $border_bottom['style'] !== 'none' && $border_bottom['color'] !== '' ) { ?> border-bottom: <?php echo esc_attr( "{$border_bottom['width']}px {$border_bottom['style']} {$border_bottom['color']}" ) ?>; <?php } ?>
		<?php if ( $border_left['style'] !== 'none' && $border_left['color'] !== '' ) { ?> border-left: <?php echo esc_attr( "{$border_left['width']}px {$border_left['style']} {$border_left['color']}" ) ?>; <?php } ?>
		<?php if ( $border_right['style'] !== 'none' && $border_right['color'] !== '' ) { ?> border-right: <?php echo esc_attr( "{$border_right['width']}px {$border_right['style']} {$border_right['color']}" ) ?>; <?php } ?>
		<?php if ( $radius['value'] !== '' ) { ?> border-radius: <?php echo esc_attr( "{$radius['value']}{$radius['unit']}" ) ?>; <?php } ?>
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