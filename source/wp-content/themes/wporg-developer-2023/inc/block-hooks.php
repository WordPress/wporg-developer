<?php
/**
 * Blocks hooks.
 *
 * @package wporg-developer-2023
 */

use function DevHub\is_parsed_post_type;

/**
 * Filters the search block and conditionally inserts search filters.
 *
 * @param string $block_content
 * @param array  $block
 * @return string
 */
function filter_search_block( $block_content, $block ) {
	if ( 'core/search' !== $block['blockName'] ) {
		return $block_content;
	}

	if ( ! wporg_is_handbook() ) {
		// Inject filters if search bar has our class
		if ( isset( $block['attrs']['className'] ) && strpos( $block['attrs']['className'], 'wporg-filtered-search-form' ) ) {
			$block_content = str_replace( '</form>', do_blocks( '<!-- wp:wporg/search-filters /-->' ) . '</form>', $block_content );
		}
	} else {
		$block_content = str_replace(
			'action="' . esc_url( home_url( '/' ) ) . '"',
			'action="' . esc_url( get_query_var( 'current_handbook_home_url' ) ) . '"',
			$block_content
		);
	}

	return $block_content;
}

add_filter( 'render_block', __NAMESPACE__ . '\\filter_search_block', 10, 2 );

/**
 * Filters the search block and updates the placeholder.
 *
 * @param string $parsed_block
 * @return array
 */
function render_block_data( $parsed_block ) {
	if ( 'core/search' !== $parsed_block['blockName'] ) {
		return $parsed_block;
	}

	if ( is_parsed_post_type() || 'command' === get_post_type() ) {
		$parsed_block['attrs']['placeholder'] = __( 'Search in the reference...', 'wporg' );
	}

	return $parsed_block;
}

add_filter( 'render_block_data', __NAMESPACE__ . '\render_block_data', 10, 2 );
