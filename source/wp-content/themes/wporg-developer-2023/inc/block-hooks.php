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
 * @param array $block
 * @return string
 */
function filter_search_block( $block_content, $block ) {
	if ( 'core/search' !== $block['blockName'] ) {
		return $block_content;
	}

	// Inject filters if search bar has our class.
	if ( isset( $block['attrs']['className'] ) && strpos( $block['attrs']['className'], 'wporg-filtered-search-form' ) ) {
		return str_replace( '</form>', do_blocks( '<!-- wp:wporg/search-filters /-->' ) . '</form>', $block_content );
	}

	return $block_content;
}

add_filter( 'render_block', __NAMESPACE__ . '\\filter_search_block', 10, 2 );
