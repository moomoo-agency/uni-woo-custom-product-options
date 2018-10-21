<?php
/**
 * Uni Cpo Formatting
 *
 * Functions for formatting data.
 *
 * @author        MooMoo
 * @category    Core
 * @package    UniCpo/Functions
 * @version     4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var
 *
 * @return string|array
 */
function uni_cpo_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'uni_cpo_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * Run uni_cpo_clean over posted textarea but maintain line breaks.
 * @since  4.0.0
 *
 * @param string $var
 *
 * @return string
 */
function uni_cpo_sanitize_textarea( $var ) {
	return implode( "\n", array_map( 'uni_cpo_clean', explode( "\n", $var ) ) );
}

/**
 * Array of allowed tags for different purposes
 *
 * @since 4.0.0
 * @return array
 */
function uni_cpo_get_allowed_tags() {
	return array(
		'tooltip' => array(
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'small'  => array(),
			'span'   => array(),
			'ul'     => array(),
			'li'     => array(),
			'ol'     => array(),
			'p'      => array(),
			'img'    => array(
				'alt'      => true,
				'align'    => false,
				'border'   => false,
				'height'   => true,
				'hspace'   => false,
				'longdesc' => false,
				'vspace'   => false,
				'src'      => true,
				'usemap'   => false,
				'width'    => true,
			)
		),
		'text'    => array(
			'address'    => array(),
			'a'          => array(
				'class'  => true,
				'id'     => true,
				'href'   => true,
				'rel'    => true,
				'rev'    => true,
				'name'   => true,
				'target' => true,
				'title'  => true
			),
			'abbr'       => array(),
			'acronym'    => array(),
			'audio'      => array(
				'autoplay' => true,
				'controls' => true,
				'loop'     => true,
				'muted'    => true,
				'preload'  => true,
				'src'      => true,
			),
			'b'          => array(),
			'bdo'        => array(
				'dir' => true,
			),
			'big'        => array(),
			'blockquote' => array(
				'cite'     => true,
				'lang'     => true,
				'xml:lang' => true,
			),
			'br'         => array(),
			'button'     => array(
				'disabled' => true,
				'name'     => true,
				'type'     => true,
				'value'    => true,
			),
			'caption'    => array(
				'align' => true,
			),
			'cite'       => array(
				'dir'  => true,
				'lang' => true,
			),
			'code'       => array(),
			'col'        => array(
				'align'   => true,
				'char'    => true,
				'charoff' => true,
				'span'    => true,
				'dir'     => true,
				'valign'  => true,
				'width'   => true,
			),
			'colgroup'   => array(
				'align'   => true,
				'char'    => true,
				'charoff' => true,
				'span'    => true,
				'valign'  => true,
				'width'   => true,
			),
			'del'        => array(
				'datetime' => true,
			),
			'dd'         => array(),
			'dfn'        => array(),
			'details'    => array(
				'align'    => true,
				'dir'      => true,
				'lang'     => true,
				'open'     => true,
				'xml:lang' => true,
			),
			'dl'         => array(),
			'dt'         => array(),
			'em'         => array(),
			'figure'     => array(
				'align'    => true,
				'dir'      => true,
				'lang'     => true,
				'xml:lang' => true,
			),
			'figcaption' => array(
				'align'    => true,
				'dir'      => true,
				'lang'     => true,
				'xml:lang' => true,
			),
			'font'       => array(
				'color' => true,
				'face'  => true,
				'size'  => true,
			),
			'h1'         => array(
				'align' => true,
				'class' => true,
				'id'    => true,
			),
			'h2'         => array(
				'align' => true,
				'class' => true,
				'id'    => true,
			),
			'h3'         => array(
				'align' => true,
				'class' => true,
				'id'    => true,
			),
			'h4'         => array(
				'align' => true,
				'class' => true,
				'id'    => true,
			),
			'h5'         => array(
				'align' => true,
				'class' => true,
				'id'    => true,
			),
			'h6'         => array(
				'align' => true,
				'class' => true,
				'id'    => true,
			),
			'hr'         => array(
				'align'   => true,
				'noshade' => true,
				'size'    => true,
				'width'   => true,
			),
			'i'          => array(),
			'img'        => array(
				'alt'      => true,
				'align'    => true,
				'border'   => true,
				'height'   => true,
				'hspace'   => true,
				'longdesc' => true,
				'vspace'   => true,
				'src'      => true,
				'usemap'   => true,
				'width'    => true,
				'class'    => true,
				'id'       => true,
			),
			'ins'        => array(
				'datetime' => true,
				'cite'     => true,
			),
			'input'        => array(
				'class' => true,
				'id' => true,
				'name' => true,
				'value' => true,
				'data-parsley-trigger' => true,
				'data-parsley-type' => true,
				'data-parsley-min' => true,
				'step' => true,
				'data-parsley-id' => true,
				'type' => true
			),
			'kbd'        => array(),
			'li'         => array(
				'align' => true,
				'value' => true,
			),
			'mark'       => array(),
			'q'          => array(
				'cite' => true,
			),
			'p'          => array(
				'class' => array()
			),
			's'          => array(),
			'samp'       => array(),
			'span'       => array(
				'dir'      => true,
				'align'    => true,
				'lang'     => true,
				'xml:lang' => true,
			),
			'small'      => array(),
			'strike'     => array(),
			'strong'     => array(),
			'sub'        => array(),
			'summary'    => array(
				'align'    => true,
				'dir'      => true,
				'lang'     => true,
				'xml:lang' => true,
			),
			'sup'        => array(),
			'table'      => array(
				'align'       => true,
				'bgcolor'     => true,
				'border'      => true,
				'cellpadding' => true,
				'cellspacing' => true,
				'dir'         => true,
				'rules'       => true,
				'summary'     => true,
				'width'       => true,
				'class'       => true,
				'id'          => true,
			),
			'tbody'      => array(
				'align'   => true,
				'char'    => true,
				'charoff' => true,
				'valign'  => true,
			),
			'td'         => array(
				'abbr'    => true,
				'align'   => true,
				'axis'    => true,
				'bgcolor' => true,
				'char'    => true,
				'charoff' => true,
				'colspan' => true,
				'dir'     => true,
				'headers' => true,
				'height'  => true,
				'nowrap'  => true,
				'rowspan' => true,
				'scope'   => true,
				'valign'  => true,
				'width'   => true,
				'class'   => true,
				'id'      => true,
			),
			'tfoot'      => array(
				'align'   => true,
				'char'    => true,
				'charoff' => true,
				'valign'  => true,
			),
			'th'         => array(
				'abbr'    => true,
				'align'   => true,
				'axis'    => true,
				'bgcolor' => true,
				'char'    => true,
				'charoff' => true,
				'colspan' => true,
				'headers' => true,
				'height'  => true,
				'nowrap'  => true,
				'rowspan' => true,
				'scope'   => true,
				'valign'  => true,
				'width'   => true,
				'class'   => true,
				'id'      => true,
			),
			'thead'      => array(
				'align'   => true,
				'char'    => true,
				'charoff' => true,
				'valign'  => true,
			),
			'title'      => array(),
			'tr'         => array(
				'align'   => true,
				'bgcolor' => true,
				'char'    => true,
				'charoff' => true,
				'valign'  => true,
			),
			'track'      => array(
				'default' => true,
				'kind'    => true,
				'label'   => true,
				'src'     => true,
				'srclang' => true,
			),
			'tt'         => array(),
			'u'          => array(),
			'ul'         => array(
				'type' => true,
			),
			'ol'         => array(
				'start'    => true,
				'type'     => true,
				'reversed' => true,
			),
			'var'        => array(),
			'video'      => array(
				'autoplay' => true,
				'controls' => true,
				'height'   => true,
				'loop'     => true,
				'muted'    => true,
				'poster'   => true,
				'preload'  => true,
				'src'      => true,
				'width'    => true,
			)
		)
	);
}

/**
 * Sanitize a string destined to be a tooltip.
 *
 * @since 4.0.0 Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
 *
 * @param string $var
 *
 * @return string
 */
function uni_cpo_sanitize_tooltip( $var ) {
	$allowed_tags = uni_cpo_get_allowed_tags();
	$var          = str_replace( '"', "'", $var );

	return wp_kses( html_entity_decode( $var ), $allowed_tags['tooltip'] );
}

/**
 * Sanitize a text destined to be outputted from Text module.
 *
 * @since 4.0.0
 *
 * @param string $var
 *
 * @return string
 */
function uni_cpo_sanitize_text( $var ) {
	$allowed_tags = uni_cpo_get_allowed_tags();

	return wp_kses( html_entity_decode( $var ), $allowed_tags['text'] );
}

/**
 * Sanitize a cart label.
 *
 * @since 4.0.0
 *
 * @param string $var
 *
 * @return string
 */
function uni_cpo_sanitize_label( $var ) {
	return htmlspecialchars( wp_kses( html_entity_decode( $var ), array() ) );
}

/**
 * Normalise dimensions, unify to cm then convert to wanted unit value.
 *
 * Usage:
 * uni_cpo_get_dimension(55, 'in');
 * uni_cpo_get_dimension(55, 'in', 'm');
 *
 * @param int|float $dimension
 * @param string $from_unit 'mm', 'm', 'in', 'ft', 'yd'
 * @param string $to_unit (optional) 'mm', 'm', 'in', 'ft', 'yd'
 *
 * @return float
 */
function uni_cpo_get_dimension( $dimension, $from_unit, $to_unit = '' ) {
	$from_unit = strtolower( $from_unit );

	if ( empty( $to_unit ) ) {
		$to_unit = strtolower( get_option( 'woocommerce_dimension_unit' ) );
	}

	// Unify all units to cm first.
	if ( $from_unit !== $to_unit ) {
		switch ( $from_unit ) {
			case 'in' :
				$dimension *= 2.54;
				break;
			case 'm' :
				$dimension *= 100;
				break;
			case 'mm' :
				$dimension *= 0.1;
				break;
			case 'ft' :
				$dimension *= 30.48;
				break;
			case 'yd' :
				$dimension *= 91.44;
				break;
		}

		// Output desired unit.
		switch ( $to_unit ) {
			case 'in' :
				$dimension *= 0.3937;
				break;
			case 'm' :
				$dimension *= 0.01;
				break;
			case 'mm' :
				$dimension *= 10;
				break;
			case 'ft' :
				$dimension *= 0.032808399;
				break;
			case 'yd' :
				$dimension *= 0.010936133;
				break;
		}
	}

	return ( $dimension < 0 ) ? 0 : $dimension;
}
