<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Uni Cpo Data Store.
 *
 * @category Class
 * @author   WooThemes
 */
class Uni_Cpo_Data_Store {

	/**
	 * Contains an instance of the data store class that we are working with.
	 */
	private $instance = null;

	/**
	 * Contains an array of default supported data stores.
	 */
	private $stores = array(
		'module' => 'Uni_Cpo_Module_Data_Store_CPT',
		'option' => 'Uni_Cpo_Option_Data_Store_CPT',
	);

	/**
	 * Contains the name of the current data store's class name.
	 */
	private $current_class_name = '';

	/**
	 * The object type this store works with.
	 * @var string
	 */
	private $object_type = '';


	/**
	 * Tells Uni_Cpo_Data_Store which object (setting, module, option or product)
	 * store we want to work with.
	 *
	 * @param string $object_type Name of object.
	 *
	 * @throws Exception
	 */
	public function __construct( $object_type ) {
		$this->object_type = $object_type;

		if ( array_key_exists( $object_type, $this->stores ) ) {
			$store = $this->stores[ $object_type ];
			if ( is_object( $store ) ) {
				if ( ! $store instanceof Uni_Cpo_Object_Data_Store_Interface ) {
					throw new Exception( __( 'Invalid data store.', 'uni-cpo' ) );
				}
				$this->current_class_name = get_class( $store );
				$this->instance = $store;
			} else {
				if ( ! class_exists( $store ) ) {
					throw new Exception( __( 'Invalid data store.', 'uni-cpo' ) );
				}
				$this->current_class_name = $store;
				$this->instance = new $store;
			}
		} else {
			throw new Exception( __( 'Invalid data store.', 'uni-cpo' ) );
		}
	}

	/**
	 * Only store the object type to avoid serializing the data store instance.
	 *
	 * @return array
	 */
	public function __sleep() {
		return array( 'object_type' );
	}

	/**
	 * Re-run the constructor with the object type.
	 */
	public function __wakeup() {
		$this->__construct( $this->object_type );
	}

	/**
	 * Loads a data store.
	 *
	 * @param string $object_type Name of object.
	 *
	 * @return Uni_Cpo_Data_Store
	 */
	public static function load( $object_type ) {
		return new Uni_Cpo_Data_Store( $object_type );
	}

	/**
	 * Returns the class name of the current data store.
	 *
	 * @return string
	 */
	public function get_current_class_name() {
		return $this->current_class_name;
	}

	/**
	 * Reads an object from the data store.
	 *
	 * @param Uni_Cpo_Data
	 */
	public function read( &$data ) {
		$this->instance->read( $data );
	}

	/**
	 * Create an object in the data store.
	 *
	 * @param Uni_Cpo_Data
	 */
	public function create( &$data ) {
		$this->instance->create( $data );
	}

	/**
	 * Update an object in the data store.
	 *
	 * @param Uni_Cpo_Data
	 */
	public function update( &$data ) {
		$this->instance->update( $data );
	}

	/**
	 * Delete an object from the data store.
	 *
	 * @param Uni_Cpo_Data
	 * @param array $args Array of args to pass to the delete method.
	 */
	public function delete( &$data, $args = array() ) {
		$this->instance->delete( $data, $args );
	}

	/**
	 * Data stores can define additional functions
	 *
	 *
	 * @param $method
	 * @param $parameters
	 *
	 * @return mixed
	 */
	public function __call( $method, $parameters ) {
		if ( is_callable( array( $this->instance, $method ) ) ) {
			$object = array_shift( $parameters );
			return call_user_func_array( array( $this->instance, $method ), array_merge( array( &$object ), $parameters ) );
		}
	}

}
