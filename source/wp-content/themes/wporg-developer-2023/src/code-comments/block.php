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

	// Not sure why, figure out later
	if ( ! comments_open() || (int) get_comments_number() < 1 ) {
		return '';
	}

	ob_start(); // Capture all output

	/*
	 Loop through and list the comments. Use wporg_developer_list_notes() to format the comments.
	* If you want to override this in a child theme, then you can
	* define wporg_developer_list_notes() and that will be used instead.
	* See wporg_developer_list_notes() in inc/template-tags.php for more.
	*/
	if ( is_singular( 'post' ) ) {
		wp_list_comments();
	} else {
		$ordered_comments = wporg_developer_get_ordered_notes();
		if ( $ordered_comments ) {
			wporg_developer_list_notes( $ordered_comments, array() );
		}
	}

	$output = ob_get_clean();

	$title_block = sprintf(
		'<h2 class="wp-block-heading">%s</h2>',
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
