<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/*
*   Uni_Cpo_Option Abstract class
*
*/
class Uni_Cpo_Option extends Uni_Cpo_Data
{
    /**
     * This is the name of this object type.
     * @var string
     */
    protected  $object_type = 'option' ;
    /**
     * Post type.
     * @var string
     */
    protected  $post_type = 'uni_cpo_option' ;
    /**
     * Cache group.
     * @var string
     */
    protected  $cache_group = 'options' ;
    /**
     * Stores option data.
     *
     * @var array
     */
    protected  $data = array(
        'name'            => '',
        'status'          => false,
        'slug'            => '',
        'type'            => '',
        'general'         => array(),
        'style'           => array(),
        'advanced'        => array(),
        'cpo_general'     => array(),
        'cpo_conditional' => array(),
        'cpo_validation'  => array(),
    ) ;
    /**
     * Get the option if ID is passed, otherwise the option is new and empty.
     * This class should NOT be instantiated, but the uni_cpo_get_option() function
     * should be used.
     *
     * @param int|Uni_Cpo_Option|object $option Option to init.
     */
    public function __construct( $option = 0 )
    {
        parent::__construct( $option );
        
        if ( is_numeric( $option ) && $option > 0 ) {
            $this->set_id( $option );
        } elseif ( $option instanceof self ) {
            $this->set_id( absint( $option->get_id() ) );
        } elseif ( !empty($option->ID) ) {
            $this->set_id( absint( $option->ID ) );
        } else {
            $this->set_object_read( true );
        }
        
        $this->data_store = Uni_Cpo_Data_Store::load( 'option' );
        if ( $this->get_id() > 0 ) {
            $this->data_store->read( $this );
        }
    }
    
    /**
     * Get internal type. Should return string and *should be overridden* by child classes.
     *
     * @return string
     */
    public static function get_type()
    {
        return '';
    }
    
    /**
     * Returns an array of special vars associated with the option
     *
     * @return array
     */
    public static function get_special_vars()
    {
        return array();
    }
    
    /**
     * Returns an array of data used in js query builder
     *
     * @return array
     */
    public static function get_filter_data()
    {
        return array();
    }
    
    /*
    |--------------------------------------------------------------------------
    | Getters
    |--------------------------------------------------------------------------
    |
    | Methods for getting data from the option object.
    */
    /**
     * Get product name.
     *
     * @param  string $context
     *
     * @return string
     */
    public function get_name( $context = 'view' )
    {
        return $this->get_prop( 'name', $context );
    }
    
    /**
     * Get option status.
     *
     * @param  string $context
     *
     * @return string
     */
    public function get_status( $context = 'view' )
    {
        return $this->get_prop( 'status', $context );
    }
    
    /**
     * Get option slug.
     *
     * @param  string $context
     *
     * @return string
     */
    public function get_slug( $context = 'view' )
    {
        return $this->get_prop( 'slug', $context );
    }
    
    public function get_general( $context = 'view' )
    {
        return $this->get_prop( 'general', $context );
    }
    
    public function get_style( $context = 'view' )
    {
        return $this->get_prop( 'style', $context );
    }
    
    public function get_advanced( $context = 'view' )
    {
        return $this->get_prop( 'advanced', $context );
    }
    
    public function get_cpo_general( $context = 'view' )
    {
        return $this->get_prop( 'cpo_general', $context );
    }
    
    public function get_cpo_conditional( $context = 'view' )
    {
        return $this->get_prop( 'cpo_conditional', $context );
    }
    
    public function get_cpo_validation( $context = 'view' )
    {
        return $this->get_prop( 'cpo_validation', $context );
    }
    
    public function get_slug_ending()
    {
        return preg_replace( '/' . UniCpo()->get_var_slug() . '/', '', $this->get_slug() );
    }
    
    /*
    |--------------------------------------------------------------------------
    | Setters
    |--------------------------------------------------------------------------
    |
    | Functions for setting option data. These should not update anything in the
    | database itself and should only change what is stored in the class
    | object.
    */
    /**
     * Set product name.
     *
     * @since 3.0.0
     *
     * @param string $name Product name.
     */
    public function set_name( $name )
    {
        $this->set_prop( 'name', $name );
    }
    
    /**
     * Set option status.
     *
     * @param string $status Product status.
     */
    public function set_status( $status )
    {
        $this->set_prop( 'status', $status );
    }
    
    /**
     * Set option slug.
     *
     * @param string $slug Option slug.
     */
    public function set_slug( $slug )
    {
        $this->set_prop( 'slug', $slug );
    }
    
    public function set_general( $value )
    {
        $this->set_prop( 'general', $value );
    }
    
    public function set_style( $value )
    {
        $this->set_prop( 'style', $value );
    }
    
    public function set_advanced( $value )
    {
        $this->set_prop( 'advanced', $value );
    }
    
    public function set_cpo_general( $value )
    {
        $this->set_prop( 'cpo_general', $value );
    }
    
    public function set_cpo_conditional( $value )
    {
        $this->set_prop( 'cpo_conditional', $value );
    }
    
    public function set_cpo_validation( $value )
    {
        $this->set_prop( 'cpo_validation', $value );
    }
    
    /*
    |--------------------------------------------------------------------------
    | Other Methods
    |--------------------------------------------------------------------------
    */
    /**
     * Save data (either create or update depending on if we are working on an existing option).
     *
     */
    public function save()
    {
        
        if ( $this->data_store ) {
            // Trigger action before saving to the DB. Use a pointer to adjust object props before save.
            do_action( 'uni_cpo_before_' . $this->object_type . '_object_save', $this, $this->data_store );
            
            if ( $this->get_id() ) {
                $this->data_store->update( $this );
            } else {
                $this->data_store->create( $this );
            }
            
            return $this->get_id();
        }
    
    }
    
    /*
    |--------------------------------------------------------------------------
    | Other Actions
    |--------------------------------------------------------------------------
    */
    public function cpo_order_label()
    {
        $cpo_general = $this->get_cpo_general();
        $cpo_order_label = ( !empty($cpo_general['advanced']['cpo_order_label']) ? $cpo_general['advanced']['cpo_order_label'] : 0 );
        
        if ( !empty($cpo_order_label) ) {
            return $cpo_order_label;
        } else {
            return ( !empty($cpo_general['advanced']['cpo_label']) ? $cpo_general['advanced']['cpo_label'] : $this->get_slug() );
        }
    
    }
    
    public function cpo_order_visibility()
    {
        $cpo_general = $this->get_cpo_general();
        // hide?
        return ( !empty($cpo_general['advanced']['cpo_order_visibility']) && 'yes' === $cpo_general['advanced']['cpo_order_visibility'] ? true : false );
    }
    
    public function formatted_model_data()
    {
    }
    
    public function get_edit_field( $data, $value, $context = 'cart' )
    {
    }
    
    public static function template( $data, $post_data )
    {
    }
    
    public static function get_css( $data )
    {
    }
    
    public static function conditional_rules( $data )
    {
        $rules_data = $data['settings']['cpo_conditional']['main'];
        $is_enabled = ( 'yes' === $rules_data['cpo_is_fc'] ? true : false );
        if ( !$is_enabled ) {
            return;
        }
        $is_hidden = ( 'hide' === $rules_data['cpo_fc_default'] ? true : false );
        $scheme = stripslashes_deep( stripslashes_deep( $rules_data['cpo_fc_scheme'] ) );
        $scheme = json_decode( $scheme, true );
        $slug = UniCpo()->get_var_slug() . $data['settings']['cpo_general']['main']['cpo_slug'];
        
        if ( !empty($data['pid']) ) {
            $option = uni_cpo_get_option( $data['pid'] );
            if ( is_object( $option ) ) {
                $slug = $option->get_slug();
            }
        }
        
        
        if ( is_array( $scheme ) && !empty($scheme) ) {
            $condition = uni_cpo_option_js_condition_prepare( $scheme );
            $slide_down = '$' . esc_attr( $slug ) . '.slideDown(300, function(){ window.UniCpo.position($' . esc_attr( $slug ) . '); if ("' . esc_attr( $data['type'] ) . '" === "file_upload" ) { var id = $' . esc_attr( $slug ) . '.find(".js-uni-cpo-field-file_upload-el").attr("id"); window.UniCpo.fileUploadEl[id].refresh(); } }).addClass("cpo-visible-field");' . "\n";
            $slide_up = '$' . esc_attr( $slug ) . '.slideUp(300).removeClass("cpo-visible-field");' . "\n";
            $add_class = '$' . esc_attr( $slug ) . '_fields.each(function( index ) {' . "\n";
            $add_class .= '$(this).addClass( extraClass );' . "\n";
            $add_class .= '});' . "\n";
            $remove_class = '$' . esc_attr( $slug ) . '_fields.each(function( index ) {' . "\n";
            $remove_class .= '$(this).removeClass( extraClass );' . "\n";
            $remove_class .= '});' . "\n";
            $final_statement = 'if ' . $condition . ' {' . "\n";
            
            if ( $is_hidden ) {
                $final_statement .= $slide_down;
                $final_statement .= $remove_class;
            } else {
                $final_statement .= $slide_up;
                $final_statement .= $add_class;
            }
            
            $final_statement .= '} else {' . "\n";
            
            if ( $is_hidden ) {
                $final_statement .= $slide_up;
                $final_statement .= $add_class;
            } else {
                $final_statement .= $slide_down;
                $final_statement .= $remove_class;
            }
            
            $final_statement .= '}' . "\n";
            ?>
			<script>
                jQuery(document).ready(function($) {
                    'use strict';

                    $(document.body).on('uni_cpo_options_data_ajax_success', function() {
						<?php 
            echo  esc_attr( $slug ) ;
            ?>_fields_conditional_func(unicpo.formatted_vars);
                    });
                    $(document.body).on('uni_cpo_options_data_for_conditional', function(e, fields) {
                        var variables = $.extend({}, unicpo.formatted_vars, fields);
						<?php 
            echo  esc_attr( $slug ) ;
            ?>_fields_conditional_func(variables);
                    });

                    function <?php 
            echo  esc_attr( $slug ) ;
            ?>_fields_conditional_func(formData) {
                        try {
                            var $<?php 
            echo  esc_attr( $slug ) ;
            ?>          = $('#<?php 
            echo  esc_attr( $slug ) ;
            ?>');
                            var $<?php 
            echo  esc_attr( $slug ) ;
            ?>_fields = $<?php 
            echo  esc_attr( $slug ) ;
            ?>.find('input, select, textarea');
                            var extraClass = 'uni-cpo-excluded-field';

							<?php 
            
            if ( $is_hidden ) {
                $is_hidden_html = 'if ( ! $' . esc_attr( $slug ) . '.hasClass("cpo-visible-field") ) {' . "\n";
                $is_hidden_html .= '$' . $slug . '.hide();' . "\n";
                $is_hidden_html .= $add_class;
                $is_hidden_html .= '}' . "\n";
                echo  $is_hidden_html ;
            }
            
            ?>

							<?php 
            echo  $final_statement ;
            ?>
                        } catch (e) {
                            console.error(e);
                        }
                    }
                });
			</script>
			<?php 
        }
    
    }
    
    public static function suboptions_conditional_rules( $data, $attributes = array() )
    {
    }
    
    public static function validation_rules( $data, $attributes = array() )
    {
    }
    
    public static function get_custom_attribute_html( $attributes = array() )
    {
        $custom_attributes = array();
        if ( !empty($attributes) && is_array( $attributes ) ) {
            foreach ( $attributes as $attribute => $attribute_value ) {
                $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
            }
        }
        return implode( ' ', $custom_attributes );
    }

}