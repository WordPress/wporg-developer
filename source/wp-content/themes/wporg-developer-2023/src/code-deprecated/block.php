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
 * @return string Returns the block markup.
 */
function render() {
	$deprecated_html = get_deprecated();

	if ( empty( $deprecated_html ) ) {
		return '';
	}

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %s>%s</section>',
		$wrapper_attributes,
		$deprecated_html
	);
}
