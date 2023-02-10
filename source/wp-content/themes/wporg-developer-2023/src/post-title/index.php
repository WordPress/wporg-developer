<?php
/**
 * Block Name: Post Title
 * Description: The post title for archive and search results.
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Post_Title;

use function DevHub\is_parsed_post_type;

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
		dirname( dirname( __DIR__ ) ) . '/build/post-title',
		array(
			'render_callback' => __NAMESPACE__ . '\render',
		)
	);
}

/**
 * Render the block content.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the block markup.
 */
function render( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$content = get_the_title( $block->context['postId'] );
	$post_type = get_post_type( $block->context['postId'] );
	$type = strtolower( get_post_type_object( $post_type )->labels->singular_name );

	$content_html = '';
	if ( is_parsed_post_type( $post_type ) ) {
		$content_html .= sprintf(
			'<span class="wp-block-wporg-post-title__type">%1$s</span>',
			$type
		);
	}

	$content_html .= sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( get_permalink( $block->context['postId'] ) ),
		$content
	);

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => $type,
		)
	);
	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		$content_html,
	);
}
