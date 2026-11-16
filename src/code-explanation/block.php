<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Explanation;

use function DevHub\get_explanation_content;

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
		dirname( dirname( __DIR__ ) ) . '/build/code-explanation',
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

	$explanation = get_explanation_content( $block->context['postId'] );

	if ( empty( trim( $explanation ) ) ) {
		return '';
	}

	$title_block = sprintf(
		'<!-- wp:heading {"fontSize":"heading-5"} --><h2 class="wp-block-heading has-heading-5-font-size">%s</h2><!-- /wp:heading -->',
		__( 'More Information', 'wporg' )
	);

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'wporg-has-embedded-code' ) );
	return sprintf(
		'<section %s>%s %s</section>',
		$wrapper_attributes,
		do_blocks( $title_block ),
		$explanation
	);
}
