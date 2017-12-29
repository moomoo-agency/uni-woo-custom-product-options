<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Uni_Cpo_Setting_Sync class
*
*/

class Uni_Cpo_Setting_Sync extends Uni_Cpo_Setting implements Uni_Cpo_Setting_Interface {

	/**
	 * Init
	 *
	 */
	public function __construct() {
		$this->setting_key  = 'sync';
		$this->setting_data = array(
			'title' => esc_html__( 'Sync', 'uni-cpo' )
		);
		add_action( 'wp_footer', array( $this, 'js_template' ), 10 );
	}

	/**
	 * JS template for the setting
	 * Attention: this and only this setting receives the whole model's data!
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public function js_template() {
		?>
        <script id="js-builderius-setting-<?php echo $this->setting_key; ?>-tmpl" type="text/template">
            <div class="uni-modal-row uni-clear">
				<?php echo $this->generate_field_label_html() ?>
                {{ if (data.obj_type === 'option') { }}
                <div class="uni-modal-row-second">
                    <div class="uni-setting-fields-wrap-2 uni-clear">
                        {{ if (parseInt(data.pid) > 0) { }}
                        <div class="uni-sync-status">
							<?php esc_html_e( 'Synced, ID#{{- data.pid }}', 'uni-cpo' ) ?>
                            <button
                                    id="js-unsync-module-btn"
                                    class="uni-btn-1 uni-unsync-module-btn">
                                <?php esc_html_e( 'Unsync', 'uni-cpo' ) ?></button>
                        </div>
                        {{ } else { }}
                        <div class="uni-sync-status">
							<?php esc_html_e( 'Not synced', 'uni-cpo' ) ?>
                        </div>
                        {{ } }}
						<?php
						echo $this->generate_radio_html(
							'sync[type]',
							array(
								'options' => array(
									'none'      => __( 'None', 'uni-cpo' ),
									'connect'   => __( 'Connect', 'uni-cpo' ),
									'duplicate' => __( 'Duplicate', 'uni-cpo' )
								),
								'class'   => array( 'js-sync-methods' ),
								'js_var'  => 'data.settings.general.status.sync.type'
							)
						);
						?>
                    </div>
                    <div class="uni-fetch-wrap">
                        <button
                                id="js-fetch-similar-modules"
                                title="<?php esc_attr_e( 'Fetch data', 'uni-cpo' ) ?>"
                                class="uni-fetch-data"></button>
						<?php
						echo $this->generate_select_html( // #builderius-setting-sync[pid]
							'sync[pid]',
							array(
								'options' => array(
									'0' => esc_html__( '- None -', 'uni-cpo' ),
								),
								'class'   => array( 'js-sync-posts' ),
								'js_var'  => 0
							)
						)
						?>
                        <button
                                id="js-sync-module-btn"
                                title="<?php esc_attr_e( 'Save data', 'uni-cpo' ) ?>"
                                class="uni-btn-1 uni-save-data"><?php esc_attr_e( 'Submit', 'uni-cpo' ) ?></button>
                    </div>
                </div>
                {{ } }}
            </div>
        </script>
		<?php
	}

}
