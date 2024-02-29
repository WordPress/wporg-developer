<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Comment_Form;

use function DevHub\can_user_post_note;

add_action( 'init', __NAMESPACE__ . '\init' );
add_filter( 'comment_form_submit_field', __NAMESPACE__ . '\modify_submit_field', 10, 2 );

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function init() {
	register_block_type(
		dirname( dirname( __DIR__ ) ) . '/build/code-comment-form',
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

	if ( ! comments_open() || ! can_user_post_note( true, get_the_ID() ) ) {
		return '';
	}

	if ( is_user_logged_in() ) {
		ob_start(); // Capture form output
		$args = \DevHub_User_Submitted_Content::comment_form_args();
		comment_form( $args );
		$form = ob_get_clean(); // End capture

		$editor_rules = \DevHub_User_Submitted_Content::get_editor_rules();
		$output = "<div class='wp-block-wporg-code-reference-comment-form-content'>{$form}<div class='comment-rules'>{$editor_rules}</div></div>";

	} else {
		$output = '<p>' . sprintf(
			/* translators: %s: login URL */
			__( 'You must <a href="%s">log in</a> before being able to contribute a note or feedback.', 'wporg' ),
			esc_url( 'https://login.wordpress.org/?redirect_to=' . urlencode( get_permalink() ) )
		) . '</p>';
	}

	// If there are comments the heading will be displayed at the top of that block, and won't be required again here
	$ordered_comments = wporg_developer_get_ordered_notes();
	$title_block = empty( $ordered_comments ) ? sprintf(
		'<!-- wp:heading --><h2 class="wp-block-heading">%s</h2><!-- /wp:heading -->',
		__( 'User Contributed Notes', 'wporg' )
	) : '';

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %1$s>%2$s %3$s</section>',
		$wrapper_attributes,
		$title_block,
		$output,
	);
}

/**
 * Update the for submit field html to include the log in html.
 *
 * @param string $submit_field
 * @return string
 */
function modify_submit_field( $submit_field ) {
	$user_identity = wp_get_current_user();

	$logged_in_link = sprintf(
		wp_kses_post(
			/* translators: %1$s: User url, %2$s: User name, %3$s: Logout url.  */
			__( 'Logged in as <a href="%1$s" aria-label="%2$s">%2$s</a>. (<a href="%3$s">log out</a>)', 'wporg' )
		),
		esc_url( 'https://profiles.wordpress.org/' . esc_attr( $user_identity->user_nicename ) . '/' ),
		esc_attr( $user_identity->display_name ),
		esc_url( wp_logout_url( apply_filters( 'the_permalink', get_permalink() ) ) )
	);

	return str_replace( '</p>', "<span class='logged-in-as'>{$logged_in_link}</span></p>", $submit_field );
}
