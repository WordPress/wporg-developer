<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Comments;

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
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	if ( post_password_required() ) {
		return;
	}

	if ( ! comments_open() ) {
		return '';
	}

	ob_start(); // Capture all output

	$ordered_comments = wporg_developer_get_ordered_notes();

	if ( empty( $ordered_comments ) ) {
		return '';
	}

	wporg_developer_list_notes( $ordered_comments, array() );

	$output = ob_get_clean();

	$title_block = sprintf(
		'<!-- wp:heading {"fontSize":"heading-5"} --><h2 class="wp-block-heading has-heading-5-font-size">%s</h2><!-- /wp:heading -->',
		__( 'User Contributed Notes', 'wporg' )
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %1$s>%2$s <ol class="comment-list">%3$s</ol></section>',
		$wrapper_attributes,
		$title_block,
		$output
	);
}
