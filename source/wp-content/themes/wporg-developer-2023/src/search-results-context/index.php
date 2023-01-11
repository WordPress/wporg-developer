<?php
/**
 * Block Name: Search Results Context
 * Description: Displays context information for search results.
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Developer_2023\Search_Results_Context;

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Render the block content.
 *
 * @return string Returns the block markup.
 */
function render( $attributes ) {
	global $wp_query;

	if ( ! is_search() ) {
		return '';
	}

	$content = sprintf(
		/* translators: %1$s number of results; %2$s keyword. */
		_n(
			'We found <b>%1$s</b> result for <b>%2$s</b>',
			'We found <b>%1$s</b> results for <b>%2$s</b>',
			$wp_query->found_posts,
			'wporg'
		),
		number_format_i18n( $wp_query->found_posts ),
		esc_html( $wp_query->query['s'] )
	);

	$wrapper_attributes = get_block_wrapper_attributes();

	return sprintf(
		'<%1$s %2$s>%3$s</%1$s>',
		esc_attr( $attributes['tagName'] ),
		$wrapper_attributes,
		$content
	);
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function init() {
	register_block_type(
		dirname( dirname( __DIR__ ) ) . '/build/search-results-context',
		array(
			'render_callback' => __NAMESPACE__ . '\render',
		)
	);
}
