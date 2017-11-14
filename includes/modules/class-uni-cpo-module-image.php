<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Module_Image class
*
*/

class Uni_Cpo_Module_Image extends Uni_Cpo_Module implements Uni_Cpo_Module_Interface {

	/**
	 * Constructor gets the post object and sets the ID for the loaded option.
	 *
	 */
	public function __construct( $module = 0 ) {

		parent::__construct( $module );

	}

	public static function get_type(){
		return 'image';
	}

	public static function get_title(){
		return __('Image', 'uni-cpo');
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
						'image' => array(
							'url' => '',
							'id' => 0,
							'alt' => ''
						)
					)
				),
				'style' => array(
                    'border' => array(
                        'radius' => array(
                            'value' => '0',
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
            {{ const { url, alt } = data.settings.general.main.image; }}
            {{ const { radius } = data.settings.style.border; }}
            {{ const { margin } = data.settings.advanced.layout; }}
            <div class="uni-module uni-module-{{- type }}" data-node="{{- id }}" data-type="{{- type }}">
            	<style>
                    .uni-node-{{= id }} {
                    	{{ if ( margin.top !== '' ) { }} margin-top: {{= margin.top + margin.unit }}; {{ } }}
                        {{ if ( margin.bottom !== '' ) { }} margin-bottom: {{= margin.bottom + margin.unit }}; {{ } }}
                        {{ if ( margin.left !== '' ) { }} margin-left: {{= margin.left + margin.unit }}; {{ } }}
                        {{ if ( margin.right !== '' ) { }} margin-right: {{= margin.right + margin.unit }}; {{ } }}
                        {{ if ( radius.value !== '' ) { }} border-radius: {{= radius.value + radius.unit }}; {{ } }}
                    }
                </style>
                <img
                    {{ if ( id_name !== '' ) { }} id="{{- id_name }}" {{ } }} 
                    class="uni-node-{{- id }} {{- class_name }}"
                    src="{{- url }}"
                    {{ if ( alt !== '' ) { }} alt="{{- alt }}" {{ } }} />
            </div>
        </script>
		<?php
	}

	public static function template( $data ) {
		$id        = $data['id'];
		$image     = $data['settings']['general']['main']['image'];
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
        <img
                id="<?php echo implode( ' ', array_map( function ( $el ) {
					return esc_attr( $el );
				}, $css_id ) ); ?>"
                class="<?php echo implode( ' ', array_map( function ( $el ) {
					return esc_attr( $el );
				}, $css_class ) ); ?>"
                src="<?php echo esc_attr( $image['url'] ) ?>"
			<?php if ( $image['alt'] !== '' ) { ?>
                alt="<?php echo esc_attr( $image['alt'] ) ?>"
			<?php } ?>>
		<?php
	}

	public static function get_css( $data ) {
		$id     = $data['id'];
		$radius = $data['settings']['style']['border']['radius'];
		$margin = $data['settings']['advanced']['layout']['margin'];

		ob_start();
		?>
        .uni-node-<?php esc_attr_e( $id ); ?> {
		<?php if ( $margin['top'] !== '' ) { ?> margin-top: <?php esc_attr_e( "{$margin['top']}{$margin['unit']}" ) ?>; <?php } ?>
		<?php if ( $margin['bottom'] !== '' ) { ?> margin-bottom: <?php esc_attr_e( "{$margin['bottom']}{$margin['unit']}" ) ?>; <?php } ?>
		<?php if ( $margin['left'] !== '' ) { ?> margin-left: <?php esc_attr_e( "{$margin['left']}{$margin['unit']}" ) ?>; <?php } ?>
		<?php if ( $margin['right'] !== '' ) { ?> margin-right: <?php esc_attr_e( "{$margin['right']}{$margin['unit']}" ) ?>; <?php } ?>
		<?php if ( $radius['value'] !== '' ) { ?> border-radius: <?php esc_attr_e( "{$radius['value']}{$radius['unit']}" ) ?>; <?php } ?>
        }

		<?php
		return ob_get_clean();
	}

}

?>