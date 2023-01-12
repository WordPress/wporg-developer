<?php
/**
 * Block Name: Search Title
 * Description: A dynamic list of code references.
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Search_Title;

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
		dirname( dirname( __DIR__ ) ) . '/build/search-title',
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
	$type = strtolower( get_post_type_object( get_post_type( $block->context['postId'] ) )->labels->singular_name );

	$type_html = sprintf(
		'<span class="wp-block-wporg-search-title__type">%1$s</span>',
		$type
	);

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => $type,
		)
	);

	return sprintf(
		'<div %1$s>%2$s <a href="%3$s">%4$s</a></div>',
		$wrapper_attributes,
		$type_html,
		esc_url( get_permalink( $block->context['postId'] ) ),
		$content,
	);
}
