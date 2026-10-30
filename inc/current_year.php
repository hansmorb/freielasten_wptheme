<?php
/**
 * Ersetzt {current_year} im Neve-Footer durch das aktuelle Jahr.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'theme_mod_footer_copyright_content', function ( $value ) {
	if ( ! is_string( $value ) ) {
		return $value;
	}

	return str_replace(
		'{current_year}',
		wp_date( 'Y' ),
		$value
	);
} );