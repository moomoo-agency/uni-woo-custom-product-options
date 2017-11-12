<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
*   Uni_Cpo_Module Abstract class
*
*/

class Uni_Cpo_Module extends Uni_Cpo_Data {

	/**
	 * This is the name of this object type.
	 * @var string
	 */
	protected $object_type = 'module';

	/**
	 * Post type.
	 * @var string
	 */
	protected $post_type = 'uni_module';

	/**
	 * Cache group.
	 * @var string
	 */
	protected $cache_group = 'modules';

	/**
	 * Stores module data.
	 *
	 * @var array
	 */
	protected $data = array(
		'width_type' => 'auto',
		'width'       => array(
			'value' => '',
			'unit'  => 'px'
		),
		'color'      => '',
		'text_align' => '',
		'font_family'    => '',
		'font_style'     => '',
		'font_weight'    => '',
		'font_size'      => array(
			'value' => 0,
			'unit'  => 'px'
		),
		'letter_spacing' => '',
		'line_height'    => '',
	);

	/**
	 * Get the module if ID is passed, otherwise the module is new and empty.
	 * This class should NOT be instantiated, but the uni_cpo_get_module() function
	 * should be used.
	 *
	 * @param int|Uni_Cpo_Module|object $module Module to init.
	 */
	public function __construct( $module = 0 ) {
		parent::__construct( $module );
		if ( is_numeric( $module ) && $module > 0 ) {
			$this->set_id( $module );
		} elseif ( $module instanceof self ) {
			$this->set_id( absint( $module->get_id() ) );
		} elseif ( ! empty( $module->ID ) ) {
			$this->set_id( absint( $module->ID ) );
		} else {
			$this->set_object_read( true );
		}

		$this->data_store = Uni_Cpo_Data_Store::load( 'module' );
		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
		}
	}

	/**
	 * Get internal type. Should return string and *should be overridden* by child classes.
	 *
	 * @return string
	 */
	public static function get_type(){
		return '';
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Methods for getting data from the module object.
	*/

	/**
	 * Get module slug.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_slug( $context = 'view' ) {
		return $this->get_prop( 'name', $context );
	}

	// TODO add more getters


	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting module data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	*/

	/**
	 * Set module slug.
	 *
	 * @param string $slug Module slug.
	 */
	public function set_slug( $slug ) {
		$this->set_prop( 'slug', $slug );
	}

	// TODO add more setters

	/*
	|--------------------------------------------------------------------------
	| Other Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Save data (either create or update depending on if we are working on an existing module).
	 *
	 */
	public function save() {
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

	public static function template( $data ){}

	public static function get_css( $data ){}

}
