<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Title;

use function DevHub\get_signature;

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
		dirname( dirname( __DIR__ ) ) . '/build/code-title',
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

	$post_ID = $block->context['postId'];
	$content = get_signature( $post_ID );

	if ( isset( $attributes['isLink'] ) && $attributes['isLink'] ) {
		$content = sprintf(
			'<a href="%1$s">%2$s</a>',
			get_the_permalink( $post_ID ),
			$content
		);
	}

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<%1$s %2$s>%3$s</%1$s>',
		esc_attr( $attributes['tagName'] ),
		$wrapper_attributes,
		$content
	);
}
