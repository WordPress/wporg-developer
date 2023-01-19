<?php
namespace WordPressdotorg\Theme\Developer_2023;

use function DevHub\get_current_version_term;

/**
 * Get the current wordpress version.
 */
add_shortcode(
	'wordpress_version',
	function() {
		$version = get_current_version_term();
		return $version && ! is_wp_error( $version ) ? substr( $version->name, 0, -2 ) : '';
	}
);

/**
 * Get the current wordpress version link.
 */
add_shortcode(
	'wordpress_version_link',
	function() {
		$version = get_current_version_term();
		return $version && ! is_wp_error( $version ) ? esc_attr( get_term_link( $version, 'wp-parser-since' ) ) : '';
	}
);


