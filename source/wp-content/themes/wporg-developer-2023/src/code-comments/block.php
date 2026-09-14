<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Comments;

use function DevHub\is_parsed_post_type;

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
		dirname( dirname( __DIR__ ) ) . '/build/code-comments',
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
	if ( ! isset( $block->context['postId'] ) || ! isset( $block->context['postType'] ) ) {
		return '';
	}

	if ( post_password_required() ) {
		return;
	}

	if ( ! comments_open() ) {
		return '';
	}

	if ( is_parsed_post_type( $block->context['postType'] ) ) {
		// Ensure editor and dashicons styles are enqueued.
		// When there are multiple comment forms this is not always the case.
		// Handles must be different from default for these assets or they won't load.
		// Don't include the version as we want the WordPress version.
		wp_enqueue_style( // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			'editor-buttons-style',
			includes_url() . 'css/editor.css',
			array(),
		);
		wp_enqueue_style( // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			'dashicons-style',
			includes_url() . 'css/dashicons.css',
			array(),
		);
	}

	$ordered_comments = wporg_developer_get_ordered_notes();

	if ( empty( $ordered_comments ) ) {
		return '';
	}

	ob_start(); // Capture all output
	wporg_developer_list_notes( $ordered_comments, array() );

	$output = ob_get_clean();

	/*
	 * This output is parsed a second time on its way through `the_content`: core's do_blocks at
	 * priority 9 and do_shortcode at priority 11 both run after filter_code_content() has inserted it.
	 * Neutralize the syntax of both parsers. The shortcodes a note may use (code, php, js, css) have
	 * already been expanded by DevHub_User_Submitted_Content::do_note_shortcodes() on `comment_text`,
	 * so any bracket still present here is literal text.
	 *
	 * Nothing in this buffer may emit inline JavaScript or a JSON island: those are the only contexts
	 * in which the entities below would not be decoded by the browser.
	 */
	$output = preg_replace( '/<!--(\s*\/?wp:)/', '&lt;!--$1', $output );
	$output = str_replace( array( '[', ']' ), array( '&#91;', '&#93;' ), $output );

	$wrapper_attributes = get_block_wrapper_attributes( [ 'data-nosnippet' => 'true' ] );
	return sprintf(
		'<section %1$s><h2 class="wp-block-heading">%2$s</h2> <ol class="comment-list">%3$s</ol></section>',
		$wrapper_attributes,
		esc_html__( 'User Contributed Notes', 'wporg' ),
		$output
	);
}
