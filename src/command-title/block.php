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

	$post_ID = $block->context['postId'];

	if ( ! isset( $post_ID ) ) {
		return '';
	}

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

	// Remove the filter that adds the code reference block to the content.
	remove_filter( 'the_content', 'DevHub\filter_command_content', 4 );

	$excerpt = get_the_excerpt( $post_ID );
	if ( $excerpt ) {
		$content .= '<p class="excerpt">' . $excerpt . '</p>';
	}

	// Re-add the filter that adds this block to the content.
	add_filter( 'the_content', 'DevHub\filter_command_content', 4 );

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %1$s>%2$s</section>',
		$wrapper_attributes,
		$content,
	);
}
