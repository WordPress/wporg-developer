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
 * @return string Returns the block markup.
 */
function render() {
	$content = wporg_developer_code_reference_source_render();

	if ( empty( $content ) ) {
		return '';
	}

	$title_block = sprintf(
		'<h2 class="wp-block-heading">%s</h2>',
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
 * @return string
 */
function wporg_developer_code_reference_source_render() {
	$source_file = get_source_file();
	$output = '';

	if ( ! empty( $source_file ) ) {
		$source_code = post_type_has_source_code() ? get_source_code() : '';

		$view_reference_button = sprintf(
			'<a href="%s">%s</a>',
			esc_url( get_source_file_archive_link( $source_file ) ),
			__( 'View all references', 'wporg' )
		);

		$view_trac_button = sprintf(
			'<a href="%s">%s</a>',
			esc_url( get_source_file_link() ),
			__( 'View on Trac', 'wporg' )
		);

		$view_github_button = sprintf(
			'<a href="%s">%s</a>',
			esc_url( get_github_source_file_link() ),
			__( 'View on GitHub', 'wporg' ) 
		);

		if ( ! empty( $source_code ) ) {
			$output .= do_blocks(
				sprintf(
					'<!-- wp:code {"lineNumbers":true} --><pre class="wp-block-code" data-start="%1$s" aria-label="%2$s"><code lang="php" class="language-php line-numbers">%3$s</code></pre><!-- /wp:code -->',
					esc_attr( get_post_meta( get_the_ID(), '_wp-parser_line_num', true ) ),
					__( 'Function source code', 'wporg' ),
					htmlentities( $source_code )
				)
			);

			$output .= sprintf('<p class="wporg-developer-code-block-source-links">%s</p>', implode(' ', array($view_reference_button, $view_trac_button, $view_github_button)));
		} else {
			$output .= sprintf('<p class="wporg-developer-code-block-source-links">%s</p>', implode(' ', array($view_reference_button, $view_trac_button)));
		}
	}

	return $output;
}
