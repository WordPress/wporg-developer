<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Related;

use function DevHub\post_type_has_uses_info;
use function DevHub\post_type_has_usage_info;
use function DevHub\get_source_file;
use function DevHub\get_summary;
use function DevHub\get_uses;
use function DevHub\get_used_by;
use function DevHub\split_uses_by_frequent_funcs;

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
		dirname( dirname( __DIR__ ) ) . '/build/code-related',
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
	$uses        = null;
	$used_by     = null;
	$has_uses    = false;
	$has_used_by = false;

	if ( post_type_has_uses_info() ) {
		$uses     = get_uses();
		$has_uses = $uses->have_posts();
	}

	if ( post_type_has_usage_info() ) {
		$used_by     = get_used_by();
		$has_used_by = $used_by->have_posts();
	}

	if ( ! $has_uses && ! $has_used_by ) {
		return '';
	}

	$title_block = sprintf(
		'<h2 class="wp-block-heading">%s</h2>',
		__( 'Related', 'wporg' )
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %s>%s %s %s</section>',
		$wrapper_attributes,
		$title_block,
		$has_uses ? do_blocks( get_uses_table( $uses ) ) : '',
		$has_used_by ? do_blocks( get_used_by_table( $used_by ) ) : '',
	);
}

/**
 * Returns list of rows for the table.
 *
 * @param WP_Post[] $posts
 * @return array[]
 */
function get_row_data( $posts ) {
	$rows = array();

	while ( $posts->have_posts() ) {
		$posts->the_post();
		$is_function = ! in_array( get_post_type(), array( 'wp-parser-class', 'wp-parser-hook' ) );

		$rows[] = array(
			sprintf(
				'<a href="%1$s">%2$s%3$s</a><code>%4$s</code></a>',
				get_permalink(),
				get_the_title(),
				$is_function ? '()' : '',
				esc_attr( get_source_file() )
			),
			get_summary(),
		);
		wp_reset_postdata();
	}

	return $rows;
}

/**
 * Returns a table for uses.
 */
function get_uses_table( $uses ) {
	$uses_to_show     = 5;
	$min_uses_to_show = 2;
	$uses_to_show = min( $uses_to_show, max( split_uses_by_frequent_funcs( $uses->posts ), $min_uses_to_show ) );
	$headings     = array( __( 'Uses', 'wporg' ), __( 'Description', 'wporg' ) );

	return sprintf(
		'<!-- wp:wporg/code-table {"itemsToShow":%s,"headings":%s,"rows":%s,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} /--> ',
		esc_attr( $uses_to_show ),
		wp_json_encode( $headings ),
		wp_json_encode( get_row_data( $uses ) )
	);
}

/**
 * Returns a table for used by.
 */
function get_used_by_table( $used_by ) {
	$headings = array( __( 'Used by', 'wporg' ), __( 'Description', 'wporg' ) );
	return sprintf(
		'<!-- wp:wporg/code-table {"itemsToShow":5,"headings":%s,"rows":%s,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} /--> ',
		wp_json_encode( $headings ),
		wp_json_encode( get_row_data( $used_by ) )
	);
}

