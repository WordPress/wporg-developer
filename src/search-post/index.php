<?php
/**
 * Block Name: Search Post
 * Description: The post template for search results.
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Search_Post;

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
		dirname( dirname( __DIR__ ) ) . '/build/search-post',
		array(
			'render_callback' => __NAMESPACE__ . '\render',
		)
	);
}

/**
 * Render the post to be shown in search results (default).
 */
function render_post() {
	return do_blocks(
		'<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20","right":"var:preset|spacing|20"}},"border":{"width":"1px","color":"#d9d9d9","radius":"2px"}}} -->
		<div class="wp-block-group has-border-color" style="border-color:#d9d9d9;border-width:1px;border-radius:2px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20)">

			<!-- wp:wporg/code-short-title /-->

			<!-- wp:post-excerpt /-->

		</div>
		<!-- /wp:group -->'
	);
}

/**
 * Render the post to be shown in search results, when the post is a code reference item.
 */
function render_code_reference_post() {
	return do_blocks(
		'<!-- wp:group {"align":"wide","layout":{"type":"constrained","justifyContent":"left"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10"}}}} -->
		<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--10);padding-bottom:var(--wp--preset--spacing--10)">

			<!-- wp:group {"layout":{"type":"constrained"}} -->
			<div class="wp-block-group">

				<!-- wp:wporg/code-short-title /-->

				<!-- wp:post-excerpt /-->

				<!-- wp:wporg/code-type-usage-info {"style":{"elements":{"link":{"color":{"text":"var:preset|color|charcoal-4"}}}},"textColor":"charcoal-4","fontSize":"small"} /-->

			</div>
			<!-- /wp:group -->

		</div>
		<!-- /wp:group -->'
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

	$post_type = get_post_type( $block->context['postId'] );
	$is_parsed_type = is_parsed_post_type( $post_type );

	$content_html = $is_parsed_type ? render_code_reference_post() : render_post();

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => $is_parsed_type ? 'wp-block-wporg-search-post-parsed' : '',
		)
	);

	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		$content_html,
	);
}
