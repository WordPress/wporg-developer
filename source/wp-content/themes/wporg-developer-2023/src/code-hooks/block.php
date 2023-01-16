<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Hooks;

use function DevHub\get_hooks;
use function DevHub\post_type_has_hooks_info;
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
		dirname( dirname( __DIR__ ) ) . '/build/code-hooks',
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
function render() {
	$has_hooks   = ( post_type_has_hooks_info() && ( $hooks   = get_hooks() ) && $hooks->have_posts() );

	if ( ! $has_hooks ) {
		return '';
	}

	$title_block = sprintf(
		'<h2 class="wp-block-heading">%s</h2>',
		__( 'Hooks', 'wporg' )
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %s>%s %s</section>',
		$wrapper_attributes,
		do_blocks( $title_block ),
		'Not Implemented'
	);
}
