<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Changelog;

use function DevHub\get_changelog_data;

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
		dirname( dirname( __DIR__ ) ) . '/build/code-changelog',
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

	$changelog_data = get_changelog_data();

	if ( empty( $changelog_data ) ) {
		return '';
	}

	$title_block = sprintf(
		'<!-- wp:heading {"fontSize":"heading-5"} --><h2 class="wp-block-heading has-heading-5-font-size">%s</h2><!-- /wp:heading -->',
		__( 'Changelog', 'wporg' )
	);

	$table_block = sprintf(
		'<!-- wp:wporg/code-table {"itemsToShow":5,"headings":%s,"rows":%s,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} /--> ',
		wp_json_encode( array( __( 'Version', 'wporg' ), __( 'Description', 'wporg' ) ) ),
		wp_json_encode( get_row_data( $changelog_data ) )
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %1$s>%2$s %3$s</section>',
		$wrapper_attributes,
		do_blocks( $title_block ),
		do_blocks( $table_block )
	);
}

/**
 * Get the changelog data for the current post.
 *
 * @param array $changelog_data
 * @return array
 */
function get_row_data( $changelog_data ) {
	$rows = array();
	$count = count( $changelog_data );
	$i = 0;
	$changelog_data = array_reverse( $changelog_data );

	foreach ( $changelog_data as $version => $data ) {
		// Add "Introduced." for the initial version description, last since the array is reversed.
		$data['description'] = ( ( $count - 1 ) == $i ) ? __( 'Introduced.', 'wporg' ) : $data['description'];

		$version_link = sprintf(
			'<a href="%1$s" alt="%2$s">%3$s</a>',
			esc_url( $data['since_url'] ),
			esc_attr( "WordPress {$version}" ),
			esc_html( $version )
		);

		$i++;

		$rows[] = array( $version_link, $data['description'] );
	}

	return $rows;
}
