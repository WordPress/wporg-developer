<?php
namespace WordPressdotorg\Theme\Developer_2023\Page_Title;

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function init() {
	register_block_type(
		dirname( dirname( __DIR__ ) ) . '/build/page-title',
		array(
			'render_callback' => __NAMESPACE__ . '\render',
		)
	);
}

/**
 * Render the block content.
 *
 * @return string Returns the block markup.
 */
function render( $attributes, $content, $block ) {
	$tag_name = 'h1';
	if ( isset( $attributes['level'] ) ) {
		$tag_name = 0 === $attributes['level'] ? 'p' : 'h' . (int) $attributes['level'];
	}

	$title = get_the_title();

	if ( is_search() ) {
		$title = __( 'Search results', 'wporg' );
	} elseif ( is_archive() ) {
		$title = get_the_archive_title();
	} elseif ( in_array( get_post_type(), array( 'wp-parser-function', 'wp-parser-method' ) ) ) {
		$title .= '()';
	}

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<%1$s %2$s>%3$s</section>',
		$tag_name,
		$wrapper_attributes,
		$title,
	);
}
