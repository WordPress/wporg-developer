<?php
namespace WordPressdotorg\Theme\Developer_2023\Reference_New_Updated;

use function DevHub\get_current_version_term;
use function DevHub\get_parsed_post_types;

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
		dirname( dirname( __DIR__ ) ) . '/build/reference-new-updated',
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
	$version = get_current_version_term();

	if ( ! ( $version && ! is_wp_error( $version ) ) ) {
		return '';
	}

	$list = new \WP_Query(
		array(
			'posts_per_page' => 15,
			'post_type'      => get_parsed_post_types(),
			'orderby'        => 'title',
			'order'          => 'ASC',
			'tax_query'      => array(
				array(
					'taxonomy' => 'wp-parser-since',
					'field'    => 'ids',
					'terms'    => $version->term_id,
				),
			),
		)
	);

	$content = '<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20","right":"var:preset|spacing|20"},"margin":{"top":"var:preset|spacing|20"}},"border":{"width":"1px","radius":"2px"}},"borderColor":"light-grey-1","layout":{"type":"constrained"}} -->
	<div class="wp-block-group has-border-color has-light-grey-1-border-color" style="border-width:1px;border-radius:2px;margin-top:var(--wp--preset--spacing--20);padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20)">

		<!-- wp:list {"style":{"spacing":{"padding":{"left":"0"}}},"className":"wporg-reference-list","fontSize":"small"} -->
		<ul class="wporg-reference-list has-small-font-size" style="padding-left:0">';

	while ( $list->have_posts() ) :
		$list->the_post();

		$content .= sprintf(
			'<!-- wp:list-item --><li><a href="%s">%s</a></li><!-- /wp:list-item -->',
			esc_url( get_permalink() ),
			esc_html( get_the_title() )
		);

	endwhile;

	$content .= '</ul><!-- /wp:list --></div><!-- /wp:group -->';
	$version_name = substr( $version->name, 0, -2 );

	$title_block = sprintf(
		'<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
		<div class="wp-block-group">

			<!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"heading-5"} -->
			<h2 class="wp-block-heading has-heading-5-font-size" style="font-style:normal;font-weight:600">%s</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><a href="%s"><span aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a></p>
			<!-- /wp:paragraph -->

		</div>
		<!-- /wp:group -->',
		// translators: %s is the version name
		sprintf( __( 'New and updated in %s', 'wporg' ), $version_name ),
		esc_attr( get_term_link( $version, 'wp-parser-since' ) ),
		__( 'View all', 'wporg' ),
		// translators: %s is the version name
		sprintf( __( 'View all new and updated in %s', 'wporg' ), $version_name ),
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<div %s>%s %s</div>',
		$wrapper_attributes,
		do_blocks( $title_block ),
		do_blocks( $content )
	);
}
