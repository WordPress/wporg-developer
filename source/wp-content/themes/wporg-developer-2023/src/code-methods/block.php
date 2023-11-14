<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Methods;

use function DevHub\is_deprecated;

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
		dirname( dirname( __DIR__ ) ) . '/build/code-methods',
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

	$content = get_methods_content( $block->context['postId'] );

	if ( empty( $content ) ) {
		return '';
	}

	$title_block = sprintf(
		'<!-- wp:heading {"fontSize":"heading-5"} --><h2 class="wp-block-heading has-heading-5-font-size">%s</h2><!-- /wp:heading -->',
		__( 'Methods', 'wporg' )
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %s>%s %s</section>',
		$wrapper_attributes,
		$title_block,
		$content
	);
}

/**
 * Return code methods html.
 *
 * @return string
 */
function get_methods_content( $post_id ) {
	$output = '';

	if ( 'wp-parser-class' === get_post_type() ) {
		$class_methods = get_children(
			array(
				'post_parent' => get_the_ID(),
				'post_status' => 'publish',
			)
		);

		if ( $class_methods ) {
			usort( $class_methods, 'DevHub\compare_objects_by_name' );
			$output .= '<ul>';
			foreach ( $class_methods as $method ) {
				$methods_list = '<li>';

				$permalink = get_permalink( $method->ID );
				$methods_list .= "<a href='$permalink'>";

				$title = get_the_title( $method );
				$last_colon = strrpos( $title, ':' );
				$pos = ( false !== $last_colon ) ? $last_colon + 1 : 0;
				$methods_list .= substr( $title, $pos );

				$methods_list .= '</a>';

				// Remove the filter that adds the code reference block to the content to avoid a possible infinite loop.
				remove_filter( 'the_content', 'DevHub\filter_code_content', 4 );

				$excerpt = apply_filters( 'get_the_excerpt', $method->post_excerpt, $method );
				if ( $excerpt ) {
					$methods_list .= ' &mdash; ' . sanitize_text_field( $excerpt );
				}

				// Re-add the filter that adds this block to the content.
				add_filter( 'the_content', 'DevHub\filter_code_content', 4 );

				if ( is_deprecated( $method->ID ) ) {
					$methods_list .= ' &mdash; <span class="deprecated-method">' . __( 'deprecated', 'wporg' ) . '</span>';
				}

				$methods_list .= '</li>';

				$output .= $methods_list;
			}
			$output .= '</ul>';
		}
	}

	return $output;
}
