<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Source;

use function DevHub\get_github_source_file_link;
use function DevHub\get_source_code;
use function DevHub\get_source_file;
use function DevHub\get_source_file_archive_link;
use function DevHub\get_source_file_link;
use function DevHub\post_type_has_source_code;

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
		dirname( dirname( __DIR__ ) ) . '/build/code-source',
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

	$content = get_source_content( $block->context['postId'] );

	if ( empty( $content ) ) {
		return '';
	}

	$title_block = sprintf(
		'<!-- wp:heading {"fontSize":"heading-5"} --><h2 class="wp-block-heading has-heading-5-font-size">%s</h2><!-- /wp:heading -->',
		__( 'Source', 'wporg' )
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %s>%s %s</section>',
		$wrapper_attributes,
		$title_block,
		$content
	);
}

/**
 * Returns code sample html.
 *
 * @param int $post_id
 *
 * @return string
 */
function get_source_content( $post_id ) {
	$post_type   = get_post_type( $post_id );
	$source_file = get_source_file( $post_id );
	$output      = '';

	if ( ! empty( $source_file ) ) {
		$source_code = post_type_has_source_code( $post_type ) ? get_source_code( $post_id ) : '';

		$view_reference_button = sprintf(
			'<a href="%s">%s</a>',
			esc_url( get_source_file_archive_link( $source_file ) ),
			__( 'View all references', 'wporg' )
		);

		$view_trac_button = sprintf(
			'<a href="%s">%s</a>',
			esc_url( get_source_file_link( $post_id ) ),
			__( 'View on Trac', 'wporg' )
		);

		$view_github_button = sprintf(
			'<a href="%s">%s</a>',
			esc_url( get_github_source_file_link( $post_id ) ),
			__( 'View on GitHub', 'wporg' )
		);

		if ( ! empty( $source_code ) ) {
			$output .= do_blocks(
				sprintf(
					'<!-- wp:code {"lineNumbers":true} --><pre class="wp-block-code" data-start="%1$s" aria-label="%2$s"><code id="wporg-source-code" lang="php" class="language-php line-numbers">%3$s</code></pre><!-- /wp:code -->',
					esc_attr( get_post_meta( get_the_ID(), '_wp-parser_line_num', true ) ),
					__( 'Function source code', 'wporg' ),
					htmlentities( $source_code )
				)
			);

			$output .= sprintf( '<p class="wporg-dot-link-list">%s</p>', implode( ' ', array( $view_reference_button, $view_trac_button, $view_github_button ) ) );
		} else {
			$output .= sprintf( '<p class="wporg-dot-link-list">%s</p>', implode( ' ', array( $view_reference_button, $view_trac_button ) ) );
		}
	}

	return $output;
}
