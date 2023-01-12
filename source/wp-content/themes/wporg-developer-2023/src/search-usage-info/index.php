<?php
/**
 * Block Name: Search Usage Info
 * Description: A dynamic list of code references.
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Search_Usage_Info;

use function DevHub\get_source_file;
use function DevHub\get_line_number;
use function DevHub\show_usage_info;
use function DevHub\get_used_by;
use function DevHub\get_uses;
use function DevHub\get_github_source_file_link;

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
		dirname( dirname( __DIR__ ) ) . '/build/search-usage-info',
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

	if ( ! show_usage_info() ) {
		return '';
	}

	$used_by = ( $q = get_used_by() ) ? $q->post_count : 0;
	$uses    = ( $q = get_uses() ) ? $q->post_count : 0;

	$used_by_html = sprintf(
		/* translators: 1: permalink, 2: number of functions */
		_n( 'Used by <a href="%1$s">%2$d function</a>', 'Used by <a href="%1$s">%2$d functions</a>', $used_by, 'wporg' ),
		esc_url( apply_filters( 'the_permalink', get_permalink() ) ) . '#used-by',
		$used_by
	);

	$uses_html = sprintf(
		/* translators: 1: permalink, 2: number of functions */
		_n( 'Uses <a href="%1$s">%2$d function</a>', 'Uses <a href="%1$s">%2$d functions</a>', $uses, 'wporg' ),
		esc_url( apply_filters( 'the_permalink', get_permalink() ) ) . '#uses',
		$uses
	);

	$source_html = __( 'Source:', 'wporg' ) . ' <a class="wp-block-wporg-search-usage-info__source external-link" href="' . get_github_source_file_link() . '">' . get_source_file() . ':' . get_line_number() . '</a>';

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<div %1$s>%2$s | %3$s | %4$s</div>',
		$wrapper_attributes,
		$used_by_html,
		$uses_html,
		$source_html
	);
}
