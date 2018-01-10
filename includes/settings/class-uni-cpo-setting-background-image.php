<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Background_Image class
*
*/

class Uni_Cpo_Setting_Background_Image extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'background_image';
		$this->setting_data = array();
		add_action( 'wp_footer', array( $this, 'js_template' ), 10 );
	}

	/**
	 * A template for the module
	 *
	 * @since 1.0
	 * @return string
	 */
	public function js_template() {
		?>
        <script id="js-builderius-setting-<?php echo $this->setting_key; ?>-tmpl" type="text/template">
            <div class="uni-modal-row-wrap" data-uni-constrained="select[name=background_type]"
                 data-uni-constvalue="image">
                <div class="uni-modal-row uni-clear">
					<?php
					echo $this->generate_field_label_html(
						'background_image[url]',
						array(
							'title' => esc_html__( 'Image', 'uni-cpo' ),
						)
					)
					?>
                    <div class="uni-modal-row-second uni-modal-row_background_image">
                        <?php
		                echo $this->generate_media_upload_html(
			                $this->setting_key . '[id]',
			                array(
				                'additional_fields' => array(
					                $this->setting_key . '[url]'  => array(
						                'class' => 'cpo_suboption_attach_uri',
						                'value' => '{{- data.url }}'
					                ),
					                $this->setting_key . '[alt]' => array(
						                'class' => 'cpo_suboption_attach_name',
						                'value' => '{{- data.alt }}'
					                )
				                ),
				                'preview'           => '{{- data.url }}',
				                'alt'               => '{{- data.alt }}',
				                'value'             => '{{- data.id }}'
			                )
		                );
		                ?>
                    </div>
                </div>
                <div class="uni-modal-row uni-clear">
					<?php
					echo $this->generate_field_label_html(
						'background_image[repeat]',
						array(
							'title' => esc_html__( 'Repeat', 'uni-cpo' ),
						)
					)
					?>
                    <div class="uni-modal-row-second">
						<?php
						echo $this->generate_select_html(
							'background_image[repeat]',
							array(
								'options' => array(
									'no-repeat' => esc_html__( 'None', 'builderius' ),
									'repeat'    => esc_html__( 'Repeat', 'builderius' ),
									'repeat-x'  => esc_html__( 'Repeat-X', 'builderius' ),
									'repeat-y'  => esc_html__( 'Repeat-Y', 'builderius' )
								),
								'js_var'  => 'data.repeat'
							)
						)
						?>
                    </div>
                </div>
                <div class="uni-modal-row uni-clear">
					<?php
					echo $this->generate_field_label_html(
						'background_image[position]',
						array(
							'title' => esc_html__( 'Position', 'uni-cpo' ),
						)
					)
					?>
                    <div class="uni-modal-row-second">
						<?php
						echo $this->generate_select_html(
							'background_image[position]',
							array(
								'options' => array(
									'center top'    => esc_html__( 'Center Top', 'builderius' ),
									'center center' => esc_html__( 'Center Center', 'builderius' ),
									'center bottom' => esc_html__( 'Center Bottom', 'builderius' ),
									'left top'      => esc_html__( 'Left Top', 'builderius' ),
									'left center'   => esc_html__( 'Left Center', 'builderius' ),
									'left bottom'   => esc_html__( 'Left Bottom', 'builderius' ),
									'right top'     => esc_html__( 'Right Top', 'builderius' ),
									'right center'  => esc_html__( 'Right Center', 'builderius' ),
									'right bottom'  => esc_html__( 'Right Bottom', 'builderius' )
								),
								'js_var'  => 'data.position'
							)
						)
						?>
                    </div>
                </div>
                <div class="uni-modal-row uni-clear">
					<?php
					echo $this->generate_field_label_html(
						'background_image[attachment]',
						array(
							'title' => esc_html__( 'Attachment', 'uni-cpo' ),
						)
					)
					?>
                    <div class="uni-modal-row-second">
						<?php
						echo $this->generate_select_html(
							'background_image[attachment]',
							array(
								'options' => array(
									'scroll' => esc_html__( 'Scroll', 'builderius' ),
									'fixed'  => esc_html__( 'Fixed', 'builderius' )
								),
								'js_var'  => 'data.attachment'
							)
						)
						?>
                    </div>
                </div>
                <div class="uni-modal-row uni-clear">
					<?php
					echo $this->generate_field_label_html(
						'background_image[size]',
						array(
							'title' => esc_html__( 'Size', 'uni-cpo' ),
						)
					)
					?>
                    <div class="uni-modal-row-second">
						<?php
						echo $this->generate_select_html(
							'background_image[size]',
							array(
								'options' => array(
									'none'    => esc_html__( 'None', 'builderius' ),
									'contain' => esc_html__( 'Contain', 'builderius' ),
									'cover'   => esc_html__( 'Cover', 'builderius' )
								),
								'js_var'  => 'data.size'
							)
						)
						?>
                    </div>
                </div>
            </div>
        </script>
		<?php
	}
}
