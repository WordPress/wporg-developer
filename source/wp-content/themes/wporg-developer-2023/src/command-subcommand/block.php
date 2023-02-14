<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Command_Subcommand;

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
		dirname( dirname( __DIR__ ) ) . '/build/command-subcommand',
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
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$children = get_children(
		array(
			'post_parent'    => $block->context['postId'],
			'post_type'      => 'command',
			'posts_per_page' => 250,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	// Append subcommands if they exist
	if ( ! $children ) {
		return '';
	}

	$title_block = sprintf(
		'<!-- wp:heading {"fontSize":"heading-3"} --><h3 class="wp-block-heading has-heading-3-font-size">%1$s</h3><!-- /wp:heading -->',
		__( 'Subcommands', 'wporg' )
	);

	$table_block = sprintf(
		'<!-- wp:wporg/code-table {"className":"is-responsive","itemsToShow":50,"headings":%s,"rows":%s,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} /--> ',
		wp_json_encode( array( __( 'Name', 'wporg' ), __( 'Description', 'wporg' ) ) ),
		wp_json_encode( get_row_data( $children ) )
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %1$s>%2$s %3$s</section>',
		$wrapper_attributes,
		do_blocks( $title_block ),
		do_blocks( $table_block ),
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
			esc_url( apply_filters( 'the_permalink', get_permalink( $post->ID ) ) ),
			esc_html( apply_filters( 'the_title', $post->post_title ) ),
		);

		$rows[] = array( $title_link, apply_filters( 'the_excerpt', $post->post_excerpt ) );
	}

	return $rows;
}
