<?php
namespace WordPressdotorg\Theme\Developer_2023\Article_Meta_Date;

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
		dirname( dirname( __DIR__ ) ) . '/build/article-meta-date',
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
	return sprintf(
		do_blocks(
			'<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group">
				<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}}} -->
				<p style="font-style:normal;font-weight:700">%1$s</p>
				<!-- /wp:paragraph -->

				%2$s
			</div>
			<!-- /wp:group -->'
		),
		esc_html( $attributes['heading'] ),
		do_blocks(
			sprintf(
				'<!-- wp:post-date {"displayType":"%s"} /-->',
				esc_js( $attributes['displayType'] ),
			),
		),
	);
}
