<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Deprecated;

use function DevHub\get_deprecated;

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
		dirname( dirname( __DIR__ ) ) . '/build/code-deprecated',
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

	$deprecated_html = get_deprecated( $block->context['postId'] );

	if ( empty( $deprecated_html ) ) {
		return '';
	}

	$block_markup = <<<EOT
	<!-- wp:wporg/notice {"type":"warning"} -->
	<div class="wp-block-wporg-notice is-warning-notice">
	<div class="wp-block-wporg-notice__icon"></div>
	<div class="wp-block-wporg-notice__content"><p>$deprecated_html</p></div></div>
	<!-- /wp:wporg/notice -->
	EOT;

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<div %s>%s</div>',
		$wrapper_attributes,
		do_blocks( $block_markup )
	);
}
