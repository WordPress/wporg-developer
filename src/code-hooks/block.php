<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Hooks;

use function DevHub\get_hooks;
use function DevHub\post_type_has_hooks_info;
use function DevHub\get_signature;
use function DevHub\get_summary;

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
		dirname( dirname( __DIR__ ) ) . '/build/code-hooks',
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

	$post_id = $block->context['postId'];

	if ( ! post_type_has_hooks_info( get_post_type( $post_id ) ) ) {
		return '';
	}

	$hooks = get_hooks( $post_id );

	if ( ! $hooks->have_posts() ) {
		return '';
	}

	$content = array();
	while ( $hooks->have_posts() ) {
		$hooks->the_post();

		$content[] = do_blocks( '<!-- wp:wporg/code-reference-title {"isLink":true,"tagName":"dt","fontSize":"normal"} /-->' );
		$content[] = '<dd>' . get_summary() . '</dd>';
	}
	wp_reset_postdata();

	$title_block = sprintf(
		'<!-- wp:heading {"fontSize":"heading-5"} --><h2 class="wp-block-heading has-heading-5-font-size">%s</h2><!-- /wp:heading -->',
		__( 'Hooks', 'wporg' )
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %s>%s <dl>%s</dl></section>',
		$wrapper_attributes,
		do_blocks( $title_block ),
		join( '', $content )
	);
}
