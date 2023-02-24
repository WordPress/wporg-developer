<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Command_Title;

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
		dirname( dirname( __DIR__ ) ) . '/build/command-title',
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
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$post_ID = $block->context['postId'];

	$children = get_children(
		array(
			'post_parent'    => $post_ID,
			'post_type'      => 'command',
			'posts_per_page' => 1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	$content = sprintf(
		'
		<h1><a href="%1$s" rel="bookmark">%2$s
		',
		get_the_permalink( $post_ID ),
		get_the_title( $post_ID ),
	);

	if ( $children ) {
		$content .= sprintf(
			'<span>&#60;%s&#62;</span>',
			__( 'command', 'wporg' ),
		);
	}

	$content .= '</a>';
	$content .= '</h1>';

	$excerpt = get_the_excerpt();
	if ( $excerpt ) {
		$content .= '<p class="excerpt">' . $excerpt . '</p>';
	}

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %1$s>%2$s</section>',
		$wrapper_attributes,
		$content,
	);
}
