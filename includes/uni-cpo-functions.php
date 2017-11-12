<?php

//
function uni_cpo_get_decimals_count( $value ) {
	if ( (int) $value == $value ) {
		return 0;
	} elseif ( ! is_numeric( $value ) ) {
		return false;
	}

	return strlen( $value ) - strrpos( $value, '.' ) - 1;
}

//
function uni_cpo_get_all_roles() {
	global $wp_roles;
	$all_roles  = $wp_roles->roles;
	$role_names = array();
	foreach ( $all_roles as $role_name => $role_data ) {
		$role_names[ $role_name ] = $role_data['name'];
	}

	return $role_names;
}
