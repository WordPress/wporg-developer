<?php
/**
 * Block Name: Search Title
 * Description: A dynamic list of code references.
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Search_Filters;

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
		dirname( dirname( __DIR__ ) ) . '/build/search-filters',
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
	$search_post_types = array(
		'wp-parser-function' => __( 'Functions', 'wporg' ),
		'wp-parser-hook'     => __( 'Hooks', 'wporg' ),
		'wp-parser-class'    => __( 'Classes', 'wporg' ),
		'wp-parser-method'   => __( 'Methods', 'wporg' ),
	);
	$qv_post_type = array_filter( (array) get_query_var( 'post_type' ) );
	$no_filters   = true === $GLOBALS['wp_query']->is_empty_post_type_search;
	if ( in_array( 'any', $qv_post_type ) || $no_filters ) {
		// No filters used.
		$qv_post_type = array();
	}

	$content = '<div class="wp-block-button is-style-toggle is-small">';
	$content .= sprintf(
		'<button id="wp-block-wporg-search-filters-all" class="wp-block-button__link wp-element-button" aria-pressed="%1$s">%2$s</button>',
		empty( $qv_post_type ) ? 'true' : 'false',
		__( 'All', 'wporg' ),
	);
	$content .= '</div>';

	foreach ( $search_post_types as $post_type => $label ) {
		$input_id = esc_attr( $post_type );
		$checked = checked( in_array( $post_type, $qv_post_type ), true, false );
		$content .= '<div class="wp-block-button is-style-toggle is-small">';
		$content .= sprintf( '<input id="%1$s" type="checkbox" name="post_type[]" value="%1$s" %2$s />', $input_id, $checked );
		$content .= sprintf( '<label for="%1$s" class="wp-block-button__link wp-element-button">%2$s</label>', $input_id, $label );
		$content .= '</div>';
	}

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<div %1$s>
			%2$s 
			<div class="wp-block-button is-style-text is-small">
				<button class="wp-block-button__link wp-element-button" type="submit">%3$s</button>
			</div>
		</div>',
		$wrapper_attributes,
		$content,
		esc_attr( __( 'Apply', 'wporg' ) )
	);
}
