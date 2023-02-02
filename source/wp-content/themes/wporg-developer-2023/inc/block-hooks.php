<?php
/**
 * Blocks hooks.
 *
 * @package wporg-developer-2023
 */

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

	$is_handbook = $GLOBALS['wp_query']->is_handbook ?? false;

	// Inject filters if search bar has our class and isn't a handbook search
	if ( ! $is_handbook && isset( $block['attrs']['className'] ) && strpos( $block['attrs']['className'], 'wporg-filtered-search-form' ) ) {
		$block_content = str_replace( '</form>', do_blocks( '<!-- wp:wporg/search-filters /-->' ) . '</form>', $block_content );
	}

	$search_url = get_query_var( 'current_handbook_home_url' );
	if ( $search_url ) {
		$block_content = str_replace(
			'action="' . esc_url( home_url( '/' ) ) . '"',
			'action="' . esc_url( $search_url ) . '"',
			$block_content
		);
	}

	return $block_content;
}

add_filter( 'render_block', __NAMESPACE__ . '\\filter_search_block', 10, 2 );
