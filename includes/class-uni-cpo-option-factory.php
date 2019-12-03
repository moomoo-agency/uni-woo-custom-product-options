<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Uni_Cpo Option Factory Class
 *
 * The Uni CPO Option factory creates the right option.
 *
 * @class 		Uni_Cpo_Option_Factory
 */
class Uni_Cpo_Option_Factory {

	/**
	 * Get an option.
	 *
	 * @since 4.0.0
	 * @param mixed $option_id (default: false)
	 * @return Uni_Cpo_Option|bool Option object or null if the option cannot be loaded.
	 */
	public function get_option( $option_id = false, $option_type = false ) {
		if ( $option_id ) {
			$option_id = $this->get_option_id( $option_id );
			$option_type = $this->get_option_type( $option_id );
			$classname = $this->get_option_classname( $option_id, $option_type );
		} else if ( ! $option_id && $option_type ) {
			$classname = $this->get_option_classname( false, $option_type );
		} else {
			$classname = '';
		}

		if ( ! $classname ) {
			return false;
		}

		try {
			return new $classname( $option_id );
		} catch ( Exception $e ) {
			return false;
		}

	}

	/**
	 * Get the option type for an option.
	 *
	 * @since 4.0.0
	 * @param  int $option_id
	 * @return string|false
	 */
	public static function get_option_type( $option_id ) {
		// Allow the overriding of the lookup in this function. Return the option type here.
		$override = apply_filters( 'uni_cpo_option_type_query', false, $option_id );
		if ( ! $override ) {
			return Uni_Cpo_Data_Store::load( 'option' )->get_option_type( $option_id );
		} else {
			return $override;
		}
	}

	/**
	 * Gets an option classname
	 *
	 * @since 4.0.0
	 * @param  int    $option_id
	 * @param  string $option_type
	 * @return string
	 */
	public static function get_option_classname( $option_id = false, $option_type ) {
		$classname = apply_filters( 'uni_cpo_option_class', self::get_classname_from_option_type( $option_type ), $option_type, $option_id );

		if ( $classname && class_exists( $classname ) ) {
			return $classname;
		} else {
			return false;
		}

	}

	/**
	 * Create a Uni Cpo coding standards compliant class name.
	 *
	 * @since 4.0.0
	 * @param  string $option_type
	 * @return string|false
	 */
	public static function get_classname_from_option_type( $option_type ) {
		return $option_type ? 'Uni_Cpo_Option_' . implode( '_', array_map( 'ucfirst', explode( '-', $option_type ) ) ) : false;
	}

	/**
	 * Get the option ID depending on what was passed.
	 *
	 * @since 4.0.0
	 * @param  mixed $option
	 * @return int|bool false on failure
	 */
	//
	private function get_option_id( $option ) {
		if ( is_numeric( $option ) ) {
			return $option;
		} elseif ( $option instanceof Uni_Cpo_option ) {
			return $option->get_id();
		} else {
			return false;
		}
	}

}
