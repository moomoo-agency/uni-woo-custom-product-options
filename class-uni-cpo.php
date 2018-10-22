<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Uni_Cpo Class
 */
final class Uni_Cpo
{
    /**
     * Uni_Cpo version.
     *
     * @var string
     */
    public  $version = '4.4.1' ;
    /**
     * The single instance of the class.
     *
     * @var Uni_Cpo
     */
    protected static  $_instance = null ;
    /**
     * Option factory instance.
     *
     * @var Uni_Cpo_Option_Factory
     */
    public  $option_factory = null ;
    /**
     * Module factory instance.
     *
     * @var Uni_Cpo_Module_Factory
     */
    public  $module_factory = null ;
    /**
     * Plugin's settings
     *
     * @var Uni_Cpo_Option_Factory
     */
    public  $settings_scheme = array() ;
    /**
     * Is pro
     *
     * @var Uni_Cpo_Option_Factory
     */
    protected  $is_pro = false ;
    protected  $debug_mode = false ;
    /**
     *
     */
    protected  $var_slug ;
    protected  $nov_slug ;
    protected  $builder_id ;
    private static  $plugin_updates = array() ;
    /**
     * Throw error on object clone
     *
     * The whole idea of the singleton design pattern is that there is a single
     * object therefore, we don't want the object to be cloned.
     *
     * @since 1.0.0
     * @return void
     */
    public function __clone()
    {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'uni-cpo' ), '1.0.0' );
    }
    
    /**
     * Disable unserializing of the class
     *
     * @since 1.0.0
     * @return void
     */
    public function __wakeup()
    {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'uni-cpo' ), '1.0.0' );
    }
    
    /**
     * Main Uni_Cpo Instance
     */
    public static function instance()
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Uni_Cpo Constructor.
     */
    public function __construct()
    {
        $this->define_constants();
        $this->is_pro = unicpo_fs()->is__premium_only();
        $this->includes();
        $this->init_hooks();
        add_action( 'activated_plugin', array( $this, 'activation' ) );
        $this->var_slug = 'uni_cpo_';
        $this->nov_slug = 'uni_nov_cpo_';
        $this->builder_id = 'uni_cpo_options';
        if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
            $this->debug_mode = true;
        }
    }
    
    /**
     *  Init hooks
     */
    private function init_hooks()
    {
        add_action( 'init', array( $this, 'init' ), 0 );
    }
    
    /**
     * Define Uni_Cpo Constants.
     */
    private function define_constants()
    {
        $upload_dir = wp_upload_dir();
        $plugin_settings = $this->get_settings();
        $this->define( 'UNI_CPO_PLUGIN_FILE', __FILE__ );
        $this->define( 'UNI_CPO_ABSPATH', dirname( __FILE__ ) . '/' );
        $this->define( 'UNI_CPO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
        $this->define( 'UNI_CPO_VERSION', $this->version );
        $this->define( 'UNI_CPO_CSS_DIR', wp_normalize_path( trailingslashit( $upload_dir['basedir'] ) . 'cpo-css' ) );
        $this->define( 'UNI_CPO_CSS_URI', trailingslashit( $upload_dir['baseurl'] ) . 'cpo-css' );
        $this->define( 'UNI_CPO_TEMP_DIR', wp_normalize_path( trailingslashit( $upload_dir['basedir'] ) . 'cpo_temp' ) );
    }
    
    /**
     * Define constant if not already set.
     *
     * @param  string $name
     * @param  string|bool $value
     */
    private function define( $name, $value )
    {
        if ( !defined( $name ) ) {
            define( $name, $value );
        }
    }
    
    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    private function is_request( $type )
    {
        switch ( $type ) {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined( 'DOING_AJAX' );
            case 'cron':
                return defined( 'DOING_CRON' );
            case 'frontend':
                return (!is_admin() || defined( 'DOING_AJAX' )) && !defined( 'DOING_CRON' );
        }
    }
    
    /**
     *  Includes
     */
    public function includes()
    {
        //
        include_once UNI_CPO_ABSPATH . 'includes/abstracts/abstract-uni-cpo-data.php';
        include_once UNI_CPO_ABSPATH . 'includes/abstracts/abstract-uni-cpo-option.php';
        include_once UNI_CPO_ABSPATH . 'includes/class-uni-cpo-option-factory.php';
        include_once UNI_CPO_ABSPATH . 'includes/abstracts/abstract-uni-cpo-module.php';
        include_once UNI_CPO_ABSPATH . 'includes/class-uni-cpo-module-factory.php';
        include_once UNI_CPO_ABSPATH . 'includes/abstracts/abstract-uni-cpo-setting.php';
        //
        include_once UNI_CPO_ABSPATH . 'includes/interfaces/class-uni-cpo-object-data-store-interface.php';
        include_once UNI_CPO_ABSPATH . 'includes/interfaces/class-uni-cpo-option-data-store-interface.php';
        include_once UNI_CPO_ABSPATH . 'includes/interfaces/class-uni-cpo-option-interface.php';
        include_once UNI_CPO_ABSPATH . 'includes/interfaces/class-uni-cpo-module-data-store-interface.php';
        include_once UNI_CPO_ABSPATH . 'includes/interfaces/class-uni-cpo-module-interface.php';
        include_once UNI_CPO_ABSPATH . 'includes/interfaces/class-uni-cpo-setting-interface.php';
        //
        include_once UNI_CPO_ABSPATH . 'includes/data-stores/class-uni-cpo-data-store.php';
        include_once UNI_CPO_ABSPATH . 'includes/data-stores/class-uni-cpo-data-store-wp.php';
        include_once UNI_CPO_ABSPATH . 'includes/data-stores/class-uni-cpo-option-data-store-cpt.php';
        include_once UNI_CPO_ABSPATH . 'includes/data-stores/class-uni-cpo-module-data-store-cpt.php';
        //
        include_once UNI_CPO_ABSPATH . 'includes/class-uni-cpo-data-exception.php';
        //
        include_once UNI_CPO_ABSPATH . 'includes/uni-cpo-option-functions.php';
        // TODO differentiate inclusion of files when edit mode on or off
        
        if ( $this->is_request( 'frontend' ) || $this->is_request( 'admin' ) ) {
            // row / column / modules
            include_once UNI_CPO_ABSPATH . 'includes/modules/class-uni-cpo-module-row.php';
            include_once UNI_CPO_ABSPATH . 'includes/modules/class-uni-cpo-module-column.php';
            include_once UNI_CPO_ABSPATH . 'includes/modules/class-uni-cpo-module-button.php';
            include_once UNI_CPO_ABSPATH . 'includes/modules/class-uni-cpo-module-text.php';
            include_once UNI_CPO_ABSPATH . 'includes/modules/class-uni-cpo-module-image.php';
            // options
            include_once UNI_CPO_ABSPATH . 'includes/options/class-uni-cpo-option-text-area.php';
            include_once UNI_CPO_ABSPATH . 'includes/options/class-uni-cpo-option-text-input.php';
            include_once UNI_CPO_ABSPATH . 'includes/options/class-uni-cpo-option-radio.php';
            include_once UNI_CPO_ABSPATH . 'includes/options/class-uni-cpo-option-select.php';
            
            if ( $this->is_pro() ) {
                include_once UNI_CPO_ABSPATH . 'includes/options/class-uni-cpo-option-checkbox.php';
                include_once UNI_CPO_ABSPATH . 'includes/options/class-uni-cpo-option-file-upload.php';
                include_once UNI_CPO_ABSPATH . 'includes/options/class-uni-cpo-option-datepicker.php';
                include_once UNI_CPO_ABSPATH . 'includes/options/class-uni-cpo-option-range-slider.php';
                include_once UNI_CPO_ABSPATH . 'includes/options/class-uni-cpo-option-dynamic-notice.php';
                include_once UNI_CPO_ABSPATH . 'includes/options/class-uni-cpo-option-matrix.php';
                include_once UNI_CPO_ABSPATH . 'includes/options/class-uni-cpo-option-extra-cart-button.php';
                include_once UNI_CPO_ABSPATH . 'includes/options/class-uni-cpo-option-google-map.php';
            }
        
        }
        
        
        if ( $this->is_request( 'frontend' ) ) {
            // settings
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-width-type.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-width.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-width-px.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-content-width.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-height-type.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-height.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-height-px.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-vertical-align.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-color.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-color-from.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-color-to.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-color-top.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-color-bottom.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-color-hover.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-color-active.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-text-align.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-text-align-label.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-font-family.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-font-style.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-font-weight.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-font-size.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-font-size-label.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-font-size-desc.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-font-size-px.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-letter-spacing.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-line-height.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-background-type.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-background-color.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-background-hover-color.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-background-image.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-border-top.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-border-bottom.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-border-left.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-border-right.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-border-unit.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-margin.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-padding.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-id-name.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-class-name.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-offset-px.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-gap-px.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-float.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-content.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-align.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-href.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-target.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-rel.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-radius.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-image.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-divider-style.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-sync.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-slug.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-is-required.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-is-datepicker-disabled.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-is-timepicker.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-timepicker-type.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-time-min.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-time-max.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-minute-step.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-type.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-min-val.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-max-val.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-step-val.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-def-val.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-min-chars.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-max-chars.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-rate.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-label.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-label-tag.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-order-label.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-is-tooltip.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-tooltip.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-tooltip-class.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-tooltip-image.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-tooltip-type.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-enable-cartedit.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-select-options.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-radio-options.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-mode-radio.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-geom-radio.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-upload-mode.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-max-filesize.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-mime-types.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-mode-checkbox.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-geom-checkbox.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-is-changeimage.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-order-visibility.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-addtocart-mode.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-samples-mode.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-is-fc.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-fc-default.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-fc-scheme.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-validation-msg.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-vc-extra.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-is-vc.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-vc-scheme.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-is-sc.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-sc-default.php';
            include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-sc-scheme.php';
            
            if ( $this->is_pro() ) {
                // radio
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-encoded-image.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-is-resetbutton.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-resetbutton-text.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-is-imagify.php';
                // date picker
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-date-type.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-day-night.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-date-min.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-date-max.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-date-conjunction.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-disabled-dates.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-date-rules.php';
                // range slider
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-range-type.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-range-grid.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-range-input.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-range-from.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-range-to.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-range-prefix.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-range-postfix.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-custom-values.php';
                // dynamic notice
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-notice-text.php';
                // matrix
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-matrix-data.php';
                // map
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-map-center.php';
                include_once UNI_CPO_ABSPATH . 'includes/settings/class-uni-cpo-setting-cpo-map-zoom.php';
            }
            
            // common js templates
            include_once UNI_CPO_ABSPATH . 'includes/class-uni-cpo-templates.php';
        }
        
        if ( $this->is_request( 'admin' ) || $this->is_request( 'ajax' ) ) {
        }
        if ( $this->is_request( 'ajax' ) ) {
            include_once UNI_CPO_ABSPATH . 'includes/class-uni-cpo-ajax.php';
        }
        include_once UNI_CPO_ABSPATH . 'includes/class-uni-cpo-frontend-scripts.php';
        include_once UNI_CPO_ABSPATH . 'includes/admin/uni-cpo-admin-functions.php';
        include_once UNI_CPO_ABSPATH . 'includes/admin/class-uni-cpo-admin-pointers.php';
        include_once UNI_CPO_ABSPATH . 'includes/admin/class-uni-cpo-plugin-settings.php';
        include_once UNI_CPO_ABSPATH . 'includes/class-eval-math.php';
        include_once UNI_CPO_ABSPATH . 'includes/class-uni-cpo-post-types.php';
        include_once UNI_CPO_ABSPATH . 'includes/class-uni-cpo-product.php';
        include_once UNI_CPO_ABSPATH . 'includes/uni-cpo-core-functions.php';
    }
    
    /**
     * Init
     */
    public function init()
    {
        // Before init action.
        do_action( 'before_uni_cpo_init' );
        $this->check_version();
        // Multilanguage support
        $this->load_plugin_textdomain();
        Uni_Cpo_Product::init();
        //
        $this->option_factory = new Uni_Cpo_Option_Factory();
        $this->module_factory = new Uni_Cpo_Module_Factory();
        $this->settings_scheme = new Uni_Cpo_Plugin_Settings( __FILE__ );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 10 );
        // Init action.
        do_action( 'uni_cpo_init' );
    }
    
    /**
     *  Get the builder container CSS ID
     */
    public function get_builder_id()
    {
        return $this->builder_id;
    }
    
    /**
     *  get_var_slug
     */
    public function get_var_slug()
    {
        return $this->var_slug;
    }
    
    /**
     *  get_nov_slug
     */
    public function get_nov_slug()
    {
        return $this->nov_slug;
    }
    
    /**
     *  is_debug
     */
    public function is_debug()
    {
        return $this->debug_mode;
    }
    
    /**
     * Scripts and styles used in back end
     * @since  1.0.0
     */
    function admin_scripts()
    {
        $screen = get_current_screen();
        $screen_id = ( $screen ? $screen->id : '' );
        $localizations = Uni_Cpo_Frontend_Scripts::get_localizations();
        
        if ( in_array( $screen_id, array( 'product', 'edit-product' ) ) ) {
            wp_enqueue_style(
                'uni-cpo-styles-product',
                $this->plugin_url() . '/assets/css/admin-product.css',
                false,
                UNI_CPO_VERSION,
                'all'
            );
        } elseif ( in_array( str_replace( 'edit-', '', $screen_id ), wc_get_order_types( 'order-meta-boxes' ) ) ) {
            wp_register_script(
                'moment',
                $this->plugin_url() . '/includes/vendors/moment/moment.min.js',
                array(),
                '2.19.1'
            );
            wp_register_script(
                'flatpickr',
                $this->plugin_url() . '/includes/vendors/flatpickr/flatpickr.js',
                array(),
                '4.3.2'
            );
            wp_register_script(
                'parsleyjs',
                $this->plugin_url() . '/includes/vendors/parsleyjs/parsley.min.js',
                array( 'jquery' ),
                '2.8.0'
            );
            wp_register_script(
                'parsley-localization',
                $this->plugin_url() . '/includes/vendors/parsleyjs/i18n/en.js',
                array( 'parsleyjs' ),
                '2.8.0'
            );
            wp_register_script(
                'uni-cpo-scripts-order',
                $this->plugin_url() . '/assets/js/admin-order.js',
                array(
                'wc-admin-order-meta-boxes',
                'moment',
                'flatpickr',
                'parsleyjs'
            ),
                UNI_CPO_VERSION
            );
            wp_enqueue_script( 'uni-cpo-scripts-order' );
            // media uploader
            wp_enqueue_media();
            wp_localize_script( 'parsleyjs', 'uni_parsley_loc', $localizations['parsleyjs'] );
            $uni_cpo_i18n = apply_filters( 'uni_cpo_i18n_admin_strings', array(
                'flatpickr' => $localizations['flatpickr'],
                'no_file'   => __( 'No file uploaded', 'uni-cpo' ),
            ) );
            wp_localize_script( 'uni-cpo-scripts-order', 'unicpo_i18n', $uni_cpo_i18n );
            wp_enqueue_style(
                'flatpickr',
                $this->plugin_url() . '/includes/vendors/flatpickr/flatpickr.css',
                false,
                '4.3.2',
                'all'
            );
            wp_enqueue_style(
                'uni-cpo-font-awesome',
                $this->plugin_url() . '/includes/vendors/font-awesome/css/fontawesome-all.min.css',
                false,
                '5.0.10',
                'all'
            );
            wp_enqueue_style(
                'uni-cpo-styles-order',
                $this->plugin_url() . '/assets/css/admin-order.css',
                false,
                UNI_CPO_VERSION,
                'all'
            );
        } elseif ( in_array( $screen_id, array( 'woocommerce_page_uni-cpo-settings' ) ) ) {
            wp_enqueue_style( 'farbtastic' );
            wp_enqueue_script( 'farbtastic' );
            wp_enqueue_media();
            wp_register_script(
                'uni-cpo-admin-utils',
                $this->plugin_url() . '/assets/js/admin-utils.js',
                array( 'farbtastic', 'jquery' ),
                UNI_CPO_VERSION
            );
            wp_enqueue_script( 'uni-cpo-admin-utils' );
        }
    
    }
    
    /**
     * load_plugin_textdomain()
     */
    public function load_plugin_textdomain()
    {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'uni-cpo' );
        load_textdomain( 'uni-cpo', WP_LANG_DIR . '/uni-woo-custom-product-options/uni-cpo-' . $locale . '.mo' );
        load_plugin_textdomain( 'uni-cpo', false, plugin_basename( dirname( __FILE__ ) ) . "/languages" );
    }
    
    /**
     * check_version()
     */
    public function check_version()
    {
        $current_version = get_option( 'uni_cpo_version', null );
        if ( is_null( $current_version ) ) {
            update_option( 'uni_cpo_version', $this->version );
        }
        
        if ( !defined( 'IFRAME_REQUEST' ) && !empty($plugin_updates) && version_compare( $current_version, max( array_keys( self::$plugin_updates ) ), '<' ) ) {
            $this->update_plugin();
            do_action( 'uni_cpo_updated' );
        }
    
    }
    
    /**
     * is_pro()
     */
    public function is_pro()
    {
        return $this->is_pro;
    }
    
    /**
     * default_settings()
     */
    function default_settings()
    {
        return array(
            'ajax_add_to_cart'             => '',
            'product_price_container'      => '.summary.entry-summary .price > .amount, .summary.entry-summary .price ins .amount',
            'product_image_container'      => 'figure.woocommerce-product-gallery__wrapper',
            'product_image_size'           => 'shop_single',
            'product_thumbnails_container' => 'ol.flex-control-thumbs',
            'gmap_api_key'                 => '',
            'display_weight_in_cart'       => '',
            'display_dimensions_in_cart'   => '',
            'range_slider_style'           => 'html5',
            'max_file_size'                => 2,
            'mime_type'                    => 'jpg,zip',
            'file_storage'                 => 'local',
            'custom_path_enable'           => '',
            'custom_path'                  => '',
            'dropbox_token'                => '',
            'free_sample_enable'           => '',
            'free_samples_limit'           => '',
        );
    }
    
    /**
     * get_settings()
     */
    function get_settings()
    {
        $settings = get_option( 'uni_cpo_settings_general', $this->default_settings() );
        return array_merge( $this->default_settings(), $settings );
    }
    
    /**
     * update_plugin()
     */
    private function update_plugin()
    {
        // Silence
    }
    
    /**
     * plugin_url()
     */
    public function plugin_url()
    {
        return untrailingslashit( plugins_url( '/', __FILE__ ) );
    }
    
    /**
     * plugin_path()
     */
    public function plugin_path()
    {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }
    
    /**
     * Get Ajax URL.
     * @return string
     */
    public function ajax_url()
    {
        return admin_url( 'admin-ajax.php', 'relative' );
    }
    
    /**
     * cpo_activation()
     */
    public function activation( $plugin )
    {
    }

}