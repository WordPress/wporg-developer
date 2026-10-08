<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_CLI_Command_Table;

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
		dirname( dirname( __DIR__ ) ) . '/build/cli-command-table',
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
	$posts = get_posts(
		array(
			'post_type'      => 'command',
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post_parent'    => 0,
			'nopaging'       => true,
		)
	);

	if ( is_wp_error( $posts ) || empty( $posts ) ) {
		return '';
	}

	$table_block = sprintf(
		'<!-- wp:wporg/code-table {"className":"is-responsive","itemsToShow":50,"headings":%s,"rows":%s,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} /--> ',
		wp_json_encode( array( __( 'Command', 'wporg' ), __( 'Description', 'wporg' ) ) ),
		wp_json_encode( get_row_data( $posts ) )
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %1$s>%2$s</section>',
		$wrapper_attributes,
		do_blocks( $table_block )
	);
}

/**
 * Get the changelog data for the current post.
 *
 * @param WP_Post[] $posts The posts to get the data from.
 * @return array
 */
function get_row_data( $posts ) {
	$rows = array();

	foreach ( $posts as $post ) {
		$title_link = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( get_the_permalink( $post ) ),
			esc_html( $post->post_title ),
		);

		$rows[] = array( $title_link, get_the_excerpt( $post ) );
	}

	return $rows;
}
