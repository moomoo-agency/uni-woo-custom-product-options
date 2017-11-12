<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Uni_Cpo Module Factory Class
 *
 * The Uni CPO Module factory creates the right module.
 *
 * @class 		Uni_Cpo_Module_Factory
 */
class Uni_Cpo_Module_Factory {

	/**
	 * Get a module.
	 *
	 * @since 4.0.0
	 * @param mixed $module_id (default: false)
	 * @return Uni_Cpo_Module|bool module object or null if the module cannot be loaded.
	 */
	public function get_module( $module_id = false, $module_type = false ) {
		if ( $module_id ) {
			$module_id = $this->get_module_id( $module_id );
			$module_type = $this->get_module_type( $module_id );
			$classname = $this->get_module_classname( $module_id, $module_type );
		} else if ( ! $module_id && $module_type ) {
			$classname = $this->get_module_classname( false, $module_type );
		} else {
			$classname = '';
		}

		if ( ! $classname ) {
			return false;
		}

		try {
			return new $classname( $module_id );
		} catch ( Exception $e ) {
			return false;
		}

	}

	/**
	 * Get the module type for an module.
	 *
	 * @since 4.0.0
	 * @param  int $module_id
	 * @return string|false
	 */
	public static function get_module_type( $module_id ) {
		// Allow the overriding of the lookup in this function. Return the module type here.
		$override = apply_filters( 'uni_cpo_module_type_query', false, $module_id );
		if ( ! $override ) {
			return Uni_Cpo_Data_Store::load( 'module' )->get_module_type( $module_id );
		} else {
			return $override;
		}
	}

	/**
	 * Gets an module classname
	 *
	 * @since 4.0.0
	 * @param  int    $module_id
	 * @param  string $module_type
	 * @return string
	 */
	public static function get_module_classname( $module_id = false, $module_type ) {
		$classname = apply_filters( 'uni_cpo_module_class', self::get_classname_from_module_type( $module_type ), $module_type, $module_id );

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
	 * @param  string $module_type
	 * @return string|false
	 */
	public static function get_classname_from_module_type( $module_type ) {
		return $module_type ? 'Uni_Cpo_Module_' . implode( '_', array_map( 'ucfirst', explode( '-', $module_type ) ) ) : false;
	}

	/**
	 * Get the module ID depending on what was passed.
	 *
	 * @since 4.0.0
	 * @param  mixed $module
	 * @return int|bool false on failure
	 */
	//
	private function get_module_id( $module ) {
		if ( is_numeric( $module ) ) {
			return $module;
		} elseif ( $module instanceof Uni_Cpo_module ) {
			return $module->get_id();
		} else {
			return false;
		}
	}

}
