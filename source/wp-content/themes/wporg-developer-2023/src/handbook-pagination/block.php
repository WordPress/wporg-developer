<?php
namespace WordPressdotorg\Theme\Developer_2023\Handbook_Pagination;

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
		dirname( dirname( __DIR__ ) ) . '/build/handbook-pagination',
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
	$content = do_blocks(
		'<!-- wp:group {"align":"full","style":{"border":{"top":{"color":"var:preset|color|light-grey-1","width":"1px","style":"solid"}},"spacing":{"margin":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"},"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
		<div class="wp-block-group alignfull" style="border-top-color:var(--wp--preset--color--light-grey-1);border-top-style:solid;border-top-width:1px;margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--20);padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)">'
	);
	$content .= sprintf(
		do_blocks( '<!-- wp:post-navigation-link {"type":"previous","label":"%s","showTitle":true,"linkLabel":true} /-->' ),
		esc_html__( 'Previous ', 'wporg' )
	);
	$content .= sprintf(
		do_blocks( '<!-- wp:post-navigation-link {"label":"%s","showTitle":true,"linkLabel":true} /-->' ),
		esc_html__( 'Next ', 'wporg' )
	);
	$content .= do_blocks( '</div><!-- /wp:group -->' );

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		$content
	);
}
