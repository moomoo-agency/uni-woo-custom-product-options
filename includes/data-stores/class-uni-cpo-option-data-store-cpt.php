<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Uni Cpo Option Data Store: Stored in CPT.
 *
 * @category Class
 */
class Uni_Cpo_Option_Data_Store_CPT extends Uni_Cpo_Data_Store_WP
	implements Uni_Cpo_Object_Data_Store_Interface, Uni_Cpo_Option_Data_Store_Interface {

	/**
	 * Data stored in meta keys, but not considered "meta".
	 *
	 * @since 4.0.0
	 * @var array
	 */
	protected $internal_meta_keys = array();

	/**
	 * If we have already saved our extra data, don't do automatic / default handling.
	 */
	protected $extra_data_saved = false;

	/**
	 * Stores updated props.
	 * @var array
	 */
	protected $updated_props = array();

	/*
	|--------------------------------------------------------------------------
	| CRUD Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Method to create a new option in the database.
	 *
	 * @param Uni_Cpo_Option $option
	 */
	public function create( &$option ) {
		$data_changes = $option->get_changes();
		$unique_slug  = UniCpo()->get_var_slug() . $data_changes['slug'];

		$id = wp_insert_post( apply_filters( 'uni_cpo_new_option_data', array(
			'post_type'      => 'uni_cpo_option',
			'post_status'    => $option->get_status() ? $option->get_status() : 'publish',
			'post_author'    => get_current_user_id(),
			'post_title'     => $unique_slug,
			'post_name'      => $unique_slug,
			'post_content'   => '',
			'comment_status' => 'closed'
		) ), true );

		if ( $id && ! is_wp_error( $id ) ) {
			$option->set_id( $id );
			update_post_meta( $id, '_module_type', $option::get_type() );

			$this->update_post_meta( $option, true );
			$this->update_version_and_type( $option );
			$this->handle_updated_props( $option );

			$option->save_meta_data();
			$option->apply_changes();

			do_action( 'uni_cpo_new_option', $id );
		}
	}

	/**
	 * Method to read an option from the database.
	 *
	 * @param Uni_Cpo_Option $option
	 *
	 * @throws Exception
	 */
	public function read( &$option ) {
		$option->set_defaults();

		if ( ! $option->get_id() || ! ( $post_object = get_post( $option->get_id() ) ) || 'uni_cpo_option' !== $post_object->post_type ) {
			throw new Exception( __( 'Invalid option.', 'uni-cpo' ) );
		}

		$option->set_props( array(
			'name'   => $post_object->post_title,
			'status' => $post_object->post_status,
			'slug'   => $post_object->post_name,
		) );

		$this->read_option_data( $option );
		$this->read_extra_data( $option );
		$option->set_object_read( true );
	}

	/**
	 * Method to update an option in the database.
	 *
	 * @param Uni_Cpo_Option $option
	 */
	public function update( &$option ) {
		$option->save_meta_data();
		$changes = $option->get_changes();

		// Only update the post when the post data changes.
		if ( array_intersect( array( 'status', 'slug' ), array_keys( $changes ) ) ) {
			$post_data = array(
				'post_content' => '',
				'post_title'   => UniCpo()->get_var_slug() . $option->get_slug( 'edit' ),
				'post_status'  => $option->get_status( 'edit' ) ? $option->get_status( 'edit' ) : 'publish',
				'post_name'    => UniCpo()->get_var_slug() . $option->get_slug( 'edit' ),
				'post_type'    => 'uni_cpo_option',
			);

			if ( doing_action( 'save_post' ) ) {
				$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $option->get_id() ) );
				clean_post_cache( $option->get_id() );
			} else {
				wp_update_post( array_merge( array( 'ID' => $option->get_id() ), $post_data ) );
			}
			$option->read_meta_data( true );
		}

		$this->update_post_meta( $option );
		$this->update_version_and_type( $option );
		$this->handle_updated_props( $option );

		$option->apply_changes();

		do_action( 'uni_cpo_update_product', $option->get_id() );
	}

	/**
	 * Method to delete an option from the database.
	 *
	 * @param Uni_Cpo_Option
	 * @param array $args Array of args to pass to the delete method.
	 */
	public function delete( &$option, $args = array() ) {
		$id = $option->get_id();

		$args = wp_parse_args( $args, array(
			'force_delete' => false,
		) );

		if ( ! $id ) {
			return;
		}

		if ( $args['force_delete'] ) {
			wp_delete_post( $id );
			$option->set_id( 0 );
			do_action( 'uni_cpo_delete_' . $post_type, $id );
		} else {
			wp_trash_post( $id );
			$option->set_status( 'trash' );
			do_action( 'uni_cpo_trash_' . $post_type, $id );
		}
	}

	/**
	 * Returns an array of meta for an object.
	 *
	 * @param  Uni_Cpo_Data
	 *
	 * @return array
	 */
	public function read_meta( &$data ) {
		// TODO check where and how this data are used
	}

	/**
	 * Deletes meta based on meta ID.
	 *
	 * @param  Uni_Cpo_Data
	 * @param  object $meta (containing at least ->id)
	 *
	 * @return array
	 */
	public function delete_meta( &$object, $meta ) {
		delete_metadata_by_mid( $this->meta_type, $meta->id );
	}

	/**
	 * Add new piece of meta.
	 *
	 * @param  Uni_Cpo_Data
	 * @param  object $meta (containing ->key and ->value)
	 *
	 * @return int meta ID
	 */
	public function add_meta( &$object, $meta ) {
		return add_metadata( $this->meta_type, $object->get_id(), $meta->key, is_string( $meta->value ) ? wp_slash( $meta->value ) : $meta->value, false );
	}

	/**
	 * Update meta.
	 *
	 * @param  Uni_Cpo_Data
	 * @param  object $meta (containing ->id, ->key and ->value)
	 */
	public function update_meta( &$object, $meta ) {
		update_metadata_by_mid( $this->meta_type, $meta->id, $meta->value, $meta->key );
	}

	/*
	|--------------------------------------------------------------------------
	| Additional Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Read option data. Can be overridden by child classes to load other props.
	 *
	 * @param Uni_Cpo_Option
	 */
	protected function read_option_data( &$option ) {
		$id = $option->get_id();

		$option->set_props( array(
			'general'         => get_post_meta( $id, '_general', true ),
			'style'           => get_post_meta( $id, '_style', true ),
			'advanced'        => get_post_meta( $id, '_advanced', true ),
			'cpo_general'     => get_post_meta( $id, '_cpo_general', true ),
			'cpo_conditional' => get_post_meta( $id, '_cpo_conditional', true ),
			'cpo_validation'  => get_post_meta( $id, '_cpo_validation', true ),
		) );
	}

	/**
	 * Read extra data associated with the option.
	 *
	 * @param Uni_Cpo_Option
	 */
	protected function read_extra_data( &$option ) {
		foreach ( $option->get_extra_data_keys() as $key ) {
			$function = 'set_' . $key;
			if ( is_callable( array( $option, $function ) ) ) {
				$option->{$function}( get_post_meta( $option->get_id(), '_' . $key, true ) );
			}
		}
	}

	/**
	 * Helper method that updates all the post meta for an option based on it's settings in the Uni_Cpo_Option class.
	 *
	 * @param Uni_Cpo_Option
	 * @param bool Force update. Used during create.
	 */
	protected function update_post_meta( &$option, $force = false ) {
		$meta_key_to_props = array(
			'_general'         => 'general',
			'_style'           => 'style',
			'_advanced'        => 'advanced',
			'_cpo_general'     => 'cpo_general',
			'_cpo_conditional' => 'cpo_conditional',
			'_cpo_validation'  => 'cpo_validation',
		);

		// Make sure to take extra data (like product url or text for external products) into account.
		$extra_data_keys = $option->get_extra_data_keys();

		foreach ( $extra_data_keys as $key ) {
			$meta_key_to_props[ '_' . $key ] = $key;
		}

		$props_to_update = $force ? $meta_key_to_props : $this->get_props_to_update( $option, $meta_key_to_props );

		foreach ( $props_to_update as $meta_key => $prop ) {
			$value   = $option->{"get_$prop"}( 'edit' );

			$updated = update_post_meta( $option->get_id(), $meta_key, $value );
			if ( $updated ) {
				$this->updated_props[] = $prop;
			}
		}

		// Update extra data associated with the option
		if ( ! $this->extra_data_saved ) {
			foreach ( $extra_data_keys as $key ) {
				if ( ! array_key_exists( $key, $props_to_update ) ) {
					continue;
				}

				$function = 'get_' . $key;
				if ( is_callable( array( $option, $function ) ) ) {
					if ( update_post_meta( $option->get_id(), '_' . $key, $option->{$function}( 'edit' ) ) ) {
						$this->updated_props[] = $key;
					}
				}
			}
		}
	}

	/**
	 * Handle updated meta props after updating meta data.
	 *
	 * @param  Uni_Cpo_Option $option
	 */
	protected function handle_updated_props( &$option ) {
		// Trigger action so 3rd parties can deal with updated props.
		do_action( 'uni_cpo_option_object_updated_props', $option, $this->updated_props );

		// After handling, we can reset the props array.
		$this->updated_props = array();
	}

	/**
	 * Make sure we store the option type and version (to track data changes).
	 *
	 * @param Uni_Cpo_Option
	 */
	protected function update_version_and_type( &$option ) {
		$type = $option::get_type();

		wp_set_object_terms( $option->get_id(), $type, 'option_type' );
		update_post_meta( $option->get_id(), '_option_version', UNI_CPO_VERSION );
	}

	/**
	 * Get the option type based on option ID.
	 *
	 * @param int $option_id
	 *
	 * @return bool|string
	 */
	public function get_option_type( $option_id ) {
		$type_in_meta = get_post_meta( $option_id, '_module_type', true );
		if ( $type_in_meta ) {
			return $type_in_meta;
		} else {
			return false;
		}
	}
}
