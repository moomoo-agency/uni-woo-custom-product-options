<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Uni Cpo Data Store Interface
 *
 * @category Interface
 */
interface Uni_Cpo_Object_Data_Store_Interface {
	/**
	 * Method to create a new record of a Uni_Cpo_Data based object.
	 * @param WC_Data &$data
	 */
	public function create( &$data );

	/**
	 * Method to read a record. Creates a new Uni_Cpo_Data based object.
	 * @param WC_Data &$data
	 */
	public function read( &$data );

	/**
	 * Updates a record in the database.
	 * @param WC_Data &$data
	 */
	public function update( &$data );

	/**
	 * Deletes a record from the database.
	 * @param  Uni_Cpo_Data &$data
	 * @param  array $args Array of args to pass to the delete method.
	 * @return bool result
	 */
	public function delete( &$data, $args = array() );

	/**
	 * Returns an array of meta for an object.
	 * @param  Uni_Cpo_Data &$data
	 * @return array
	 */
	public function read_meta( &$data );

	/**
	 * Deletes meta based on meta ID.
	 * @param  Uni_Cpo_Data &$data
	 * @param  object $meta (containing at least ->id)
	 * @return array
	 */
	public function delete_meta( &$data, $meta );

	/**
	 * Add new piece of meta.
	 * @param  Uni_Cpo_Data &$data
	 * @param  object $meta (containing ->key and ->value)
	 * @return int meta ID
	 */
	public function add_meta( &$data, $meta );

	/**
	 * Update meta.
	 * @param  Uni_Cpo_Data &$data
	 * @param  object $meta (containing ->id, ->key and ->value)
	 */
	public function update_meta( &$data, $meta );
}
