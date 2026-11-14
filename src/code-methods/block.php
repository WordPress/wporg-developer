<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Methods;

use function DevHub\is_deprecated;

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
		dirname( dirname( __DIR__ ) ) . '/build/code-methods',
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

	$content = get_methods_content( $block->context['postId'] );

	if ( empty( $content ) ) {
		return '';
	}

	$title_block = sprintf(
		'<!-- wp:heading {"fontSize":"heading-5"} --><h2 class="wp-block-heading has-heading-5-font-size">%s</h2><!-- /wp:heading -->',
		__( 'Methods', 'wporg' )
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %1$s>%2$s %3$s</section>',
		$wrapper_attributes,
		$title_block,
		do_blocks( $content )
	);
}

/**
 * Return code methods html.
 *
 * @param int $post_id
 * @return string
 */
function get_methods_content( $post_id ) {
	if ( 'wp-parser-class' === get_post_type() ) {
		$class_methods = get_children(
			array(
				'post_parent' => $post_id,
				'post_status' => 'publish',
			)
		);

		if ( $class_methods ) {
			usort( $class_methods, 'DevHub\compare_objects_by_name' );
			$headings     = array( __( 'Name', 'wporg' ), __( 'Description', 'wporg' ) );

			return sprintf(
				'<!-- wp:wporg/code-table {"id":"uses","headings":%1$s,"rows":%2$s,"itemsToShow":150,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} /--> ',
				wp_json_encode( $headings ),
				wp_json_encode( get_row_data( $class_methods ) )
			);
		}
	}
}

/**
 * Returns list of rows for the table.
 *
 * @param WP_Post[] $class_methods
 * @return array[]
 */
function get_row_data( $class_methods ) {
	$rows = array();

	foreach ( $class_methods as $method ) {
		// Remove the filter that adds the code reference block to the content to avoid a possible infinite loop.
		remove_filter( 'the_content', 'DevHub\filter_code_content', 4 );
		$excerpt = sanitize_text_field( apply_filters( 'get_the_excerpt', $method->post_excerpt, $method ) );
		if ( is_deprecated( $method->ID ) ) {
			$excerpt .= ' &mdash; <span class="deprecated-method">' . __( 'deprecated', 'wporg' ) . '</span>';
		}
		// Re-add the filter that adds this block to the content.
		add_filter( 'the_content', 'DevHub\filter_code_content', 4 );

		$rows[] = array(
			sprintf(
				'<a href="%1$s">%2$s</a>',
				get_permalink( $method->ID ),
				get_the_title( $method ),
			),
			$excerpt ? $excerpt : '-',
		);
		wp_reset_postdata();
	}

	return $rows;
}
