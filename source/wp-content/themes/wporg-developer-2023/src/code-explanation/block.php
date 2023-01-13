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
 * @return string Returns the block markup.
 */
function render() {
	$explanation = get_explanation_content( get_the_ID() );

	if ( empty( $explanation ) ) {
		return '';
	}

	$title_block = sprintf(
		'<h2 class="wp-block-heading">%</h2>',
		__( 'More Information', 'wporg' )
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %s>%s %s</section>',
		$wrapper_attributes,
		do_blocks( $title_block ),
		$explanation
	);
}
