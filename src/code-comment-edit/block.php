<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Comment_Edit;

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
		dirname( dirname( __DIR__ ) ) . '/build/code-comment-edit',
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

	$comment_id    = get_query_var( 'edit_user_note' );
	$comment       = get_comment( $comment_id );
	$can_user_edit = \DevHub\can_user_edit_note( $comment_id );

	// Move this to the redirect
	if ( ! ( $comment && $can_user_edit ) ) {
		return '';
	}

	$has_parent  = $comment->comment_parent ? true : false;
	$parent      = $has_parent ? get_comment( $comment->comment_parent ) : false;

	ob_start();

	if ( $has_parent ) {
		echo \DevHub_User_Submitted_Content::wp_editor_feedback( $comment, 'show', true );
	} else {
		$args = \DevHub_User_Submitted_Content::comment_form_args( $comment, 'edit' );
		comment_form( $args );
	}
	$form = ob_get_clean();

	$title_block = sprintf(
		'<h2 class="wp-block-heading">%s %d</h2>',
		$has_parent ? __( 'Edit Feedback', 'wporg' ) : __( 'Edit Note', 'wporg' ),
		$comment_id
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %1$s>%2$s %3$s %4$s</section>',
		$wrapper_attributes,
		$title_block,
		__( "<p>You can edit this note as long as it's in moderation.</p>", 'wporg' ),
		$form,
	);
}
