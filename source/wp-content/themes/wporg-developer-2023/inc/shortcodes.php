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
 */
add_shortcode(
	'article_edit_link',
	function() {
		global $post;
		$markdown_source = get_markdown_edit_link( $post->ID );
		if ( $markdown_source ) {
			return esc_url( $markdown_source );
		}
		return is_user_logged_in() ? get_edit_post_link() : wp_login_url( get_permalink() );
	}
);

/**
 * Get the link to the GH commit history.
 */
add_shortcode(
	'article_changelog_link',
	function() {
		global $post;
		$markdown_source = get_markdown_edit_link( $post->ID );
		// If this is a github page, use the edit URL to generate the
		// commit history URL
		if ( str_contains( $markdown_source, 'github.com' ) ) {
			return str_replace( '/edit/', '/commits/', $markdown_source );
		}
		return '#';
	}
);

/**
 * Get the title of the article.
 */
add_shortcode(
	'article_title',
	function() {
		global $post;
		return get_the_title();
	}
);

/**
 * Only display the 'Last updated' if the modified date is later than the publishing date.
 */
add_shortcode(
	'last_updated',
	function() {
		global $post;
		if ( get_the_modified_date( 'Ymdhi', $post->ID ) > get_the_date( 'Ymdhi', $post->ID ) ) {
			return '<p style="font-style:normal;font-weight:700">' . esc_html__( 'Last updated', 'wporg' ) . '</p>';
		}
		return '';
	}
);

/**
 * Get the markdown link.
 *
 * @param int $post_id Post ID.
 */
function get_markdown_edit_link( $post_id ) {
	$markdown_source = get_post_meta( $post_id, 'wporg_markdown_source', true );
	if ( ! $markdown_source ) {
		return;
	}

	if ( 'github.com' !== parse_url( $markdown_source, PHP_URL_HOST ) ) {
		return $markdown_source;
	}

	if ( preg_match( '!^https?://github.com/(?P<repo>[^/]+/[^/]+)/(?P<editblob>blob|edit)/(?P<branchfile>.*)$!i', $markdown_source, $m ) ) {
		if ( 'edit' === $m['editblob'] ) {
			return $markdown_source;
		}

		$markdown_source = "https://github.com/{$m['repo']}/edit/{$m['branchfile']}";
	}

	return $markdown_source;
}
