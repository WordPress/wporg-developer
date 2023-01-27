<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Return_Value;

use function DevHub\get_return;

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
		dirname( dirname( __DIR__ ) ) . '/build/code-return-value',
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

	$content = get_return( $block->context['postId'] );

	if ( empty( $content ) ) {
		return '';
	}

	$title_block = sprintf(
		'<h2 class="wp-block-heading">%s</h2>',
		__( 'Return', 'wporg' )
	);

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'wporg-has-embedded-code' ) );
	return sprintf(
		'<section %s>%s %s</section>',
		$wrapper_attributes,
		do_blocks( $title_block ),
		$content
	);
}
