<?php
namespace WordPressdotorg\Theme\Developer_2023\Chapter_List;

require_once __DIR__ . '/class-chapter-walker.php';

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
		dirname( dirname( __DIR__ ) ) . '/build/chapter-list',
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

	$post_id   = $block->context['postId'];
	$post_type = get_post_type( $post_id );

	$args = array(
		'title_li'    => '',
		'echo'        => 0,
		'sort_column' => 'menu_order, title',
		'post_type'   => $post_type,

		// Use custom walker that excludes display of orphaned pages. (An ancestor
		// of such a page is likely not published and thus this is not accessible.)
		'walker'      => new Chapter_Walker(),
	);

	$post_type_obj = get_post_type_object( $post_type );

	if ( $post_type_obj && current_user_can( $post_type_obj->cap->read_private_posts ) ) {
		$args['post_status'] = array( 'publish', 'private' );
	}

	$content = wp_list_pages( $args );

	$title = do_blocks(
		'<!-- wp:heading {"fontFamily":"inter"} -->
		<h2 class="wp-block-heading has-inter-font-family">' . __( 'Chapters', 'wporg' ) . '</h2>
		<!-- /wp:heading -->'
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<nav %1$s>%2$s<ul>%3$s</ul></nav>',
		$wrapper_attributes,
		$title,
		$content
	);
}
