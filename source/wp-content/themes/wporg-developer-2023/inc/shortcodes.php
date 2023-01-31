<?php
namespace WordPressdotorg\Theme\Developer_2023;

use function DevHub\get_current_version_term;

/**
 * Get the current WordPress version.
 */
add_shortcode(
	'wordpress_version',
	function() {
		$version = get_current_version_term();
		return $version && ! is_wp_error( $version ) ? substr( $version->name, 0, -2 ) : '';
	}
);

/**
 * Get the current WordPress version link.
 */
add_shortcode(
	'wordpress_version_link',
	function() {
		$version = get_current_version_term();
		return $version && ! is_wp_error( $version ) ? esc_attr( get_term_link( $version, 'wp-parser-since' ) ) : '';
	}
);

/**
 * Get the link to edit the page.
 *
 * The handbook code automatically swaps out the edit link for the github URL,
 * if this is a github handbook.
 */
add_shortcode(
	'article_edit_link',
	function() {
		return get_edit_post_link();
	}
);

/**
 * Get the link to the GH commit history.
 */
add_shortcode(
	'article_changelog_link',
	function() {
		// If this is a github page, use the edit URL to generate the
		// commit history URL
		$edit_url = get_edit_post_link();
		if ( str_contains( $edit_url, 'github.com' ) ) {
			return str_replace( '/edit/', '/commits/', $edit_url );
		}
		return '#';
	}
);
