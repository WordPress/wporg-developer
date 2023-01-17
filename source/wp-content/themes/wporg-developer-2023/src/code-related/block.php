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


	$has_uses    = ( post_type_has_uses_info()  && ( $uses    = get_uses()    ) && $uses->have_posts()    );
	$has_used_by = ( post_type_has_usage_info() && ( $used_by = get_used_by() ) && $used_by->have_posts() );

	$uses_to_show     = 5;
	$min_uses_to_show = 2;
	$used_by_to_show  = 5;

	if ( $has_uses ) {
		$uses_to_show = min( $uses_to_show, max( split_uses_by_frequent_funcs( $uses->posts ), $min_uses_to_show ) );
	}

	if ( ! $has_uses && ! $has_used_by ) {
		return '';
	}

	$headings = array();
	$headings[] = esc_html( 'Uses', 'wporg' );
	$headings[] = esc_html( 'Description', 'wporg' );

	$rows = array();

	while( $uses->have_posts() ) {
		$uses->the_post();
		$columns = array();

		$columns[] = sprintf(
			'<a href="%s">%s</a>%s',
			get_permalink(),
			get_the_title(),
			esc_attr( get_source_file() )
		);
		$columns[] = get_summary();
		$rows[] = $columns;
		wp_reset_postdata();
	}


	$title_block = sprintf(
		'<h2 class="wp-block-heading">%s</h2>',
		__( 'Related', 'wporg' )
	);

	$uses_table = sprintf(
		'<!-- wp:wporg/code-table {"className": "super-table","headings":%s,"rows":%s } /--> ',
		json_encode( $headings ),
		json_encode( $rows )
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %s>%s %s</section>',
		$wrapper_attributes,
		$title_block,
		do_blocks( $uses_table )
	);
}
