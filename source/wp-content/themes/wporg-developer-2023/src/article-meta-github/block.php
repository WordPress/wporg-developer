<?php
namespace WordPressdotorg\Theme\Developer_2023\Article_Meta_Github;

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
		dirname( dirname( __DIR__ ) ) . '/build/article-meta-github',
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
	$github_handbooks = array(
		'wpcs-handbook',
		'blocks-handbook',
		'rest-api-handbook',
		'cli-handbook',
		'adv-admin-handbook',
	);

	$post_type = $block->context['postType'];
	$post_id = $block->context['postId'];

	if (
		! isset( $post_id )
		|| ! isset( $post_type )
		|| ! in_array( $post_type, $github_handbooks, true )
	) {
		return '';
	}

	$title = get_the_title( $post_id );

	$content = do_blocks( '<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} --><div class="wp-block-group">' );
	$content .= sprintf(
		do_blocks( '<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}}} --><p style="font-style:normal;font-weight:700">%s</p><!-- /wp:paragraph -->' ),
		esc_html__( 'Edit article', 'wporg' )
	);
	$content .= sprintf(
		do_blocks( '<!-- wp:paragraph {"className":"external-link"} --><p class="external-link"><a href="[article_edit_link]">%s</a></p><!-- /wp:paragraph -->' ),
		sprintf(
			/* translators: %s: article title */
			__( 'Improve it on GitHub<span class="screen-reader-text">: %s</span>', 'wporg' ),
			esc_html( $title )
		)
	);
	$content .= do_blocks( '</div><!-- /wp:group -->' );

	if ( 'blocks-handbook' !== $post_type ) {
		$content .= do_blocks( '<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} --><div class="wp-block-group">' );
		$content .= sprintf(
			do_blocks( '<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}}} --><p style="font-style:normal;font-weight:700">%s</p><!-- /wp:paragraph -->' ),
			esc_html__( 'Changelog', 'wporg' )
		);
		$content .= sprintf(
			do_blocks( '<!-- wp:paragraph {"className":"external-link"} --><p class="external-link"><a href="[article_changelog_link]">%s</a></p><!-- /wp:paragraph -->' ),
			sprintf(
				/* translators: %s: article title */
				__( 'See list of changes<span class="screen-reader-text">: %s</span>', 'wporg' ),
				esc_html( $title )
			)
		);
		$content .= do_blocks( '</div><!-- /wp:group -->' );
	}

	return $content;
}
