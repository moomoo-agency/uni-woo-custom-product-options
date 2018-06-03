<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Setting Abstract class
*
*/

abstract class Uni_Cpo_Setting {

	/**
	 * Type of setting and its name
	 *
	 * @var string
	 */
    public $setting_key;

	/**
	 * Setting data
	 *
	 * @var array
	 */
    public $setting_data;

	/**
	 * Get custom attributes.
	 *
	 * @param  array $data
	 * @return string
	 */
	public function get_custom_attribute_html( $data = array() ) {
	    if ( empty( $data ) ) {
	        $data = $this->setting_data;
        }
		$custom_attributes = array();

		if ( $data['is_required'] ) {
			$data['custom_attributes']['data-parsley-required'] = true;
			$data['custom_attributes']['data-parsley-trigger'] = 'change focusout submit';
		}

		$data['custom_attributes'] = array_unique( $data['custom_attributes'] );

		if ( ! empty( $data['custom_attributes'] ) && is_array( $data['custom_attributes'] ) ) {
			foreach ( $data['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		return implode( ' ', $custom_attributes );
	}

	/**
	 * Generate Text Input HTML.
	 *
	 * @param  mixed $key
	 * @param  mixed $data
	 * @since  4.0.0
	 * @return string
	 */
	public function generate_text_html( $key = '', $data = array() ) {
        if ( empty( $key ) ) {
            $key = $this->setting_key;
        }
        if ( empty( $data ) ) {
	        $data = $this->setting_data;
        }
		$defaults  = array(
			'disabled'          => false,
			'class'             => array(),
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'custom_attributes' => array(),
            'value'             => '',
			'is_required'       => false,
            'no_init_class'     => false
		);

		$data = wp_parse_args( $data, $defaults );

		if ( true !== $data['no_init_class'] ) {
		    $data['class'][] = 'builderius-setting-field';
		}
		if ( $data['is_required'] ) {
			$data['class'][] = 'builderius-field-required';
		}
		ob_start();
		?>
		<input
                class="<?php echo implode( ' ', array_map( function($el){ return esc_attr( $el ); }, $data['class'] ) ); ?>"
                type="<?php echo esc_attr( $data['type'] ); ?>"
                name="<?php echo esc_attr( $key ); ?>"
                id="builderius-setting-<?php echo esc_attr( $key ); ?>"
                style="<?php echo esc_attr( $data['css'] ); ?>"
                value="<?php echo esc_attr( $data['value'] ); ?>"
                placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>"
                <?php disabled( $data['disabled'], true ); ?>
                <?php echo $this->get_custom_attribute_html( $data ); ?> />
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Color Picker Input HTML.
	 *
	 * @param  mixed $key
	 * @param  mixed $data
	 * @since  4.0.0
	 * @return string
	 */
	public function generate_color_html( $key = '', $data = array() ) {
	    if ( empty( $key ) ) {
            $key = $this->setting_key;
        }
        if ( empty( $data ) ) {
	        $data = $this->setting_data;
        }
		$defaults  = array(
			'disabled'          => false,
			'class'             => array(),
			'css'               => '',
			'placeholder'       => '',
			'custom_attributes' => array(),
			'value'             => '',
			'is_required'       => false,
            'no_init_class'     => false
		);

		$data = wp_parse_args( $data, $defaults );

		if ( true !== $data['no_init_class'] ) {
		    $data['class'][] = 'builderius-setting-field';
		}
		if ( $data['is_required'] ) {
			$data['class'][] = 'builderius-field-required';
		}
		$data['class'][] = 'builderius-setting-colorpick';

		ob_start();
		?>
		<input
		    class="<?php echo implode( ' ', array_map( function($el){ return esc_attr( $el ); }, $data['class'] ) ); ?>"
		    type="text"
		    name="<?php echo esc_attr( $key ); ?>"
		    id="builderius-setting-<?php echo esc_attr( $key ); ?>"
		    style="<?php echo esc_attr( $data['css'] ); ?>"
		    value=""
		    placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>"
		    <?php disabled( $data['disabled'], true ); ?>
		    <?php echo $this->get_custom_attribute_html( $data ); ?> />
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Datepicker HTML.
	 *
	 * @param  mixed $key
	 * @param  mixed $data
	 * @since  4.0.0
	 * @return string
	 */
	public function generate_datepicker_html( $key = '', $data = array() ) {
        if ( empty( $key ) ) {
            $key = $this->setting_key;
        }
        if ( empty( $data ) ) {
	        $data = $this->setting_data;
        }
		$defaults  = array(
			'disabled'          => false,
			'class'             => array(),
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'single',
            'calendar'          => 'enabled',
            'timepicker'        => 'disabled',
			'custom_attributes' => array(),
            'value'             => '',
			'is_required'       => false,
            'no_init_class'     => false
		);

		$data = wp_parse_args( $data, $defaults );

		if ( true !== $data['no_init_class'] ) {
		    $data['class'][] = 'builderius-setting-field';
		}
		if ( $data['is_required'] ) {
			$data['class'][] = 'builderius-field-required';
		}
		$data['class'][] = 'builderius-setting-datepicker';
		$data['class'][] = 'datepicker-mode-' . $data['type'];
        $data['class'][] = 'datepicker-calendar-' . $data['calendar'];
        $data['class'][] = 'timepicker-' . $data['timepicker'];
		ob_start();
		?>
		<input
                class="<?php echo implode( ' ', array_map( function($el){ return esc_attr( $el ); }, $data['class'] ) ); ?>"
                type="text"
                name="<?php echo esc_attr( $key ); ?>"
                id="builderius-setting-<?php echo esc_attr( $key ); ?>"
                style="<?php echo esc_attr( $data['css'] ); ?>"
                value="<?php echo esc_attr( $data['value'] ); ?>"
                placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>"
                <?php disabled( $data['disabled'], true ); ?>
                <?php echo $this->get_custom_attribute_html( $data ); ?> />
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Textarea HTML.
	 *
	 * @param  mixed $key
	 * @param  mixed $data
	 * @since  4.0.0
	 * @return string
	 */
	public function generate_textarea_html( $key = '', $data = array() ) {
	    if ( empty( $key ) ) {
            $key = $this->setting_key;
        }
        if ( empty( $data ) ) {
	        $data = $this->setting_data;
        }
		$defaults  = array(
			'disabled'          => false,
			'class'             => array(),
			'css'               => '',
			'placeholder'       => '',
			'custom_attributes' => array(),
			'value'             => '',
			'is_required'       => false,
            'no_init_class'     => false
		);

		$data = wp_parse_args( $data, $defaults );

		if ( true !== $data['no_init_class'] ) {
		    $data['class'][] = 'builderius-setting-field';
		}
		if ( $data['is_required'] ) {
			$data['class'][] = 'builderius-field-required';
		}

		ob_start();
		?>
		<textarea
                rows="3"
                cols="20"
                class="<?php echo implode( ' ', array_map( function($el){ return esc_attr( $el ); }, $data['class'] ) ); ?>"
                name="<?php echo esc_attr( $key ); ?>"
                id="builderius-setting-<?php echo esc_attr( $key ); ?>"
                style="<?php echo esc_attr( $data['css'] ); ?>"
                placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>"
                <?php disabled( $data['disabled'], true ); ?>
                <?php echo $this->get_custom_attribute_html( $data ); ?>><?php echo esc_attr( $data['value'] ); ?></textarea>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Checkbox HTML.
	 *
	 * @param  mixed $key
	 * @param  mixed $data
	 * @since  4.0.0
	 * @return string
	 */
	public function generate_checkbox_html( $key = '', $data = array() ) {
	    if ( empty( $key ) ) {
            $key = $this->setting_key;
        }
        if ( empty( $data ) ) {
	        $data = $this->setting_data;
        }
		$defaults  = array(
			'label'             => '',
			'disabled'          => false,
			'class'             => array(),
			'css'               => '',
			'type'              => 'text',
			'custom_attributes' => array(),
			'value'             => '',
			'is_required'       => false,
            'no_init_class'     => false
		);

		$data = wp_parse_args( $data, $defaults );

		if ( true !== $data['no_init_class'] ) {
		    $data['class'][] = 'builderius-setting-field';
		}
		if ( $data['is_required'] ) {
			$data['class'][] = 'builderius-field-required';
		}

		ob_start();

        if ( ! empty( $data['label'] ) ) {
            ?>
            <label><?php esc_html_e( $data['label'] ) ?></label>
            <?php
        }
        ?>
		<input
                class="<?php echo implode( ' ', array_map( function($el){ return esc_attr( $el ); }, $data['class'] ) ); ?>"
                type="checkbox"
                name="<?php echo esc_attr( $key ); ?>"
                id="builderius-setting-<?php echo esc_attr( $key ); ?>"
                style="<?php echo esc_attr( $data['css'] ); ?>"
                value="<?php echo ( ! empty( $data['value'] ) ) ? esc_attr( $data['value'] ) : '1' ?>"
                <?php if ( ! empty( $data['js_var'] ) ) {
                    $js_var = esc_attr( $data['js_var'] );
                    ?>
                    {{ if (typeof <?php echo $js_var; ?> !== 'undefined' && <?php echo $js_var; ?>.constructor === Array && <?php echo $js_var; ?>.length > 0) { print(' checked') } }}
                <?php } ?>
			    <?php disabled( $data['disabled'], true ); ?>
                <?php echo $this->get_custom_attribute_html( $data ); ?> />
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Linked Checkbox HTML.
	 *
	 * @param  mixed $key
	 * @since  4.0.0
	 * @return string
	 */
	public function generate_linked_checkbox_html( $key = '' ) {
		if ( empty( $key ) ) {
			$key = $this->setting_key;
		}

		ob_start();
		?>
        <div class="uni-setting-fields-linked">
            <input
                    data-linked-checkbox="<?php echo esc_attr( $key ); ?>"
                    id="uni-setting-<?php echo esc_attr( $key ); ?>-linked"
                    type="checkbox"
                    name=""
                    value=""
                    checked="checked">
            <label for="uni-setting-<?php echo esc_attr( $key ); ?>-linked"></label>
        </div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Radio HTML.
	 *
	 * @param  mixed $key
	 * @param  mixed $data
	 * @since  4.0.0
	 * @return string
	 */
	public function generate_radio_html( $key = '', $data = array() ) {
		if ( empty( $key ) ) {
			$key = $this->setting_key;
		}
		if ( empty( $data ) ) {
			$data = $this->setting_data;
		}
		$defaults  = array(
			'label'             => '',
			'disabled'          => false,
			'class'             => array(),
			'css'               => '',
			'type'              => 'text',
			'custom_attributes' => array(),
			'value'             => '',
			'is_required'       => false,
            'no_init_class'     => false
		);

		$data = wp_parse_args( $data, $defaults );

		if ( true !== $data['no_init_class'] ) {
		    $data['class'][] = 'builderius-setting-field';
		}
		if ( $data['is_required'] ) {
			$data['class'][] = 'builderius-field-required';
		}

		ob_start();
		?>
        <div class="uni-setting-radio-inputs uni-clear">
            <?php if ( ! empty( $data['js_var'] ) ) : ?>
	        <?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
                <div class="uni-setting-radio-item">
                    <input
                            id="uni-setting-<?php echo esc_attr( $key ); ?>-<?php echo esc_attr( $option_key ); ?>"
                            type="radio"
                            class="<?php echo implode( ' ', array_map( function($el){ return esc_attr( $el ); }, $data['class'] ) ); ?>"
                            name="<?php echo esc_attr( $key ); ?>"
                            value="<?php echo esc_attr( $option_key ); ?>"
	                        <?php echo $this->get_custom_attribute_html( $data ); ?>
                            {{ if (typeof <?php echo esc_attr( $data['js_var'] ); ?> !== 'undefined' && '<?php echo esc_attr( $option_key ); ?>' === <?php echo esc_attr( $data['js_var'] ); ?>) { print(' checked') } }}>
                    <label for="uni-setting-<?php echo esc_attr( $key ); ?>-<?php echo esc_attr( $option_key ); ?>"><?php echo esc_attr( $option_value ); ?></label>
                </div>
	        <?php endforeach; ?>
            <?php endif; ?>
        </div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Select HTML.
	 *
	 * @param  mixed $key
	 * @param  mixed $data
	 * @since  4.0.0
	 * @return string
	 */
	public function generate_select_html( $key = '', $data = array() ) {
	    if ( empty( $key ) ) {
            $key = $this->setting_key;
        }
        if ( empty( $data ) ) {
	        $data = $this->setting_data;
        }
		$defaults  = array(
			'disabled'          => false,
			'class'             => array(),
			'css'               => '',
			'placeholder'       => '',
			'custom_attributes' => array(),
			'options'           => array(),
			'value'             => '',
			'is_required'       => false,
            'no_init_class'     => false
		);

		$data = wp_parse_args( $data, $defaults );

		if ( true !== $data['no_init_class'] ) {
		    $data['class'][] = 'builderius-setting-field';
		}
		$data['class'][] = 'uni-modal-select';
		if ( $data['is_required'] ) {
			$data['class'][] = 'builderius-field-required';
		}

		ob_start();
		?>
		<select
                class="<?php echo implode( ' ', array_map( function($el){ return esc_attr( $el ); }, $data['class'] ) ); ?>"
                name="<?php echo esc_attr( $key ); ?>"
                id="builderius-setting-<?php echo esc_attr( $key ); ?>"
                style="<?php echo esc_attr( $data['css'] ); ?>"
                <?php disabled( $data['disabled'], true ); ?>
                <?php echo $this->get_custom_attribute_html( $data ); ?>>
		    <?php if ( ! empty( $data['js_var'] ) ) : ?>
            <?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
			<option
                    value="<?php echo esc_attr( $option_key ); ?>"
                    {{ if ('<?php echo esc_js( $option_key ); ?>' === <?php echo esc_js( $data['js_var'] ); ?>) { print(' selected') } }}>
            <?php echo esc_attr( $option_value ); ?>
            </option>
		    <?php endforeach; ?>
            <?php endif; ?>
		</select>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Media Upload HTML.
	 *
	 * @param  mixed $key
	 * @param  mixed $data
	 * @since  4.0.0
	 * @return string
	 */
	public function generate_media_upload_html( $key = '', $data = array() ) {
	    if ( empty( $key ) ) {
            $key = $this->setting_key;
        }
        if ( empty( $data ) ) {
	        $data = $this->setting_data;
        }
		$defaults  = array(
			'disabled'          => false,
			'class'             => array(),
			'css'               => '',
			'placeholder'       => '',
			'custom_attributes' => array(),
			'options'           => array(),
			'value'             => '',
			'is_required'       => false,
            'no_init_class'     => false,
            'additional_fields' => array(),
            'preview'           => '',
            'alt'               => ''
		);

		$data = wp_parse_args( $data, $defaults );

		if ( true !== $data['no_init_class'] ) {
		    $data['class'][] = 'builderius-setting-field';
		}
		$data['class'][] = 'cpo_suboption_attach_id';
		if ( $data['is_required'] ) {
			$data['class'][] = 'builderius-field-required';
		}

		ob_start();
		?>
		<input
		    type="hidden"
		    class="<?php echo implode( ' ', array_map( function($el){ return esc_attr( $el ); }, $data['class'] ) ); ?>"
            name="<?php echo esc_attr( $key ); ?>"
            value="<?php echo esc_attr( $data['value'] ); ?>"
            <?php echo $this->get_custom_attribute_html( $data ); ?> />
        <?php
        if ( ! empty( $data['additional_fields'] ) ) {
            if(($key = array_search('cpo_suboption_attach_id', $data['class'])) !== false) {
                unset($data['class'][$key]);
            }
            $original_classes = $data['class'];
            foreach( $data['additional_fields'] as $additional_key => $additional_data ) {
                $additional_class = $additional_data['class'];
                $original_classes[] = $additional_class;
            ?>
                <input
                type="hidden"
                class="<?php echo implode( ' ', array_map( function($el){ return esc_attr( $el ); }, $original_classes ) ); ?>"
                name="<?php echo esc_attr( $additional_key ); ?>"
                value="<?php echo esc_attr( $additional_data['value'] ); ?>" />
            <?php
                unset($data['class'][$additional_class]);
            }
        }
        ?>
        <button
            type="button"
            class="cpo-upload-attachment"
            data-tip="<?php esc_attr_e('Add/Change attachment', 'uni-cpo') ?>">
            <i class="fas fa-pencil-alt"></i>
        </button>
        <button
            type="button"
            class="cpo-remove-attachment"
            <?php if ( ! empty( $data['js_var'] ) ) { ?>
            {{ if (<?php echo esc_attr( $data['js_var'] ); ?>) { print(' style="display:block;"') } }}
            <?php } ?>
            data-tip="<?php esc_attr_e('Remove attachment', 'uni-cpo') ?>">
            <i class="fas fa-times"></i>
        </button>
        <div class="cpo-image-preview">
        	<?php
        	if ( ! empty( $data['preview'] ) ) {
            	echo '<img src="' . esc_attr( $data['preview'] ) . '" />';
            }
            ?>
        </div>
        <div class="cpo-image-title">
        	<?php
        	if ( ! empty( $data['alt'] ) ) {
            	echo esc_html( $data['alt'] );
            }
            ?>
        </div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Generate Field Label HTML.
	 *
	 * @param  mixed $key
	 * @param  mixed $data
	 * @since  1.0.0
	 * @return string
	 */
	public function generate_field_label_html( $key = '', $data = array() ) {
	    if ( empty( $key ) ) {
            $key = $this->setting_key;
        }
        if ( empty( $data ) ) {
	        $data = $this->setting_data;
        }
		$defaults  = array(
			'title' => '',
            'is_required' => false
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
        <div class="uni-modal-row-first">
            <label for="<?php echo esc_attr( $key ); ?>">
                <?php echo wp_kses_post( $data['title'] ); ?>
                <?php if ( $data['is_required'] ) { ?>
                <span class="uni-marked-required">*</span>
                <?php } ?>
                <?php
                if ( ! empty( $data['is_tooltip_warning'] ) ) {
                    echo $this->get_warning_tooltip_html();
                }
                if ( ! empty( $data['is_tooltip'] ) ) {
                    echo $this->get_tooltip_html();
                }
                ?>
            </label>
        </div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Get HTML for tooltips.
	 *
	 * @param  array $data
	 * @return string
	 */
	public function get_tooltip_html( $data = array() ) {
        if ( empty( $data ) ) {
	        $data = $this->setting_data;
        }
		if ( $data['desc_tip'] === true ) {
			$tip = $data['description'];
		} elseif ( ! empty( $data['desc_tip'] ) ) {
			$tip = $data['desc_tip'];
		} else {
			$tip = '';
		}
		return $tip ? uni_cpo_help_tip( $tip, true ) : '';
	}

    /**
	 * Get HTML for special warning tooltips.
	 *
	 * @param  array $data
	 * @return string
	 */
	public function get_warning_tooltip_html( $data = array() ) {
        if ( empty( $data ) ) {
	        $data = $this->setting_data;
        }
		if ( ! empty( $data['desc_tip_warning'] ) ) {
			$tip = $data['desc_tip_warning'];
		} else {
			$tip = '';
		}
		return $tip ? uni_cpo_help_tip( $tip, true, array( 'type' => 'warning' ) ) : '';
	}

}
