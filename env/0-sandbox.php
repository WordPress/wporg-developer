<?php
/**
 * These are stubs for closed source code, or things that only apply to local environments.
 */

namespace WordPressdotorg\Developer;

defined( 'WPINC' ) || die();

require_once WPMU_PLUGIN_DIR . '/wporg-mu-plugins/mu-plugins/loader.php';

/**
 * Filter the imported content URI's to point to the local site, rather than developer.wordpress.org.
 *
 * @param string $html The imported & parsed markdown.
 * @return string
 */
function wporg_markdown_after_transform( $html ) {
	return str_replace( 'https://developer.wordpress.org/', home_url( '/' ), $html );
}
add_filter( 'wporg_markdown_after_transform', __NAMESPACE__ . '\wporg_markdown_after_transform', 100 );