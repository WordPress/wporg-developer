<?php
/**
 * Blocks hooks.
 *
 * @package wporg-developer-2023
 */

use function DevHub\is_parsed_post_type;

add_filter( 'render_block_data', __NAMESPACE__ . '\modify_header_template_part' );

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

	if ( 'command' === get_post_type() ) {
		$block_content = get_block_content_by_home_url( $block_content, home_url( 'cli/commands/' ) );
	} elseif ( function_exists( 'wporg_is_handbook' ) && wporg_is_handbook() ) {
		$block_content = get_block_content_by_home_url( $block_content, get_query_var( 'current_handbook_home_url' ) );
	} else {
		if ( isset( $block['attrs']['className'] ) && strpos( $block['attrs']['className'], 'wporg-filtered-search-form' ) ) {
			$block_content = str_replace( '</form>', do_blocks( '<!-- wp:wporg/search-filters /-->' ) . '</form>', $block_content );
		}
	}

	return $block_content;
}

add_filter( 'render_block', __NAMESPACE__ . '\\filter_search_block', 10, 2 );

/**
 * Replaces the action URL in a block content string with a given URL path.
 *
 * @param string $block_content The block content string to modify.
 * @param string $replacement_home_url Replacement string for the action attribute. Defaults to an empty string.
 * @return string The modified block content string.
 */
function get_block_content_by_home_url( $block_content, $replacement_home_url = '' ) {
	return str_replace(
		'action="' . esc_url( home_url( '/' ) ) . '"',
		'action="' . esc_url( $replacement_home_url ) . '"',
		$block_content
	);
}

/**
 * Update header template based on current query.
 *
 * @param array $parsed_block The block being rendered.
 *
 * @return array The updated block.
 */
function modify_header_template_part( $parsed_block ) {
	if (
		'core/template-part' === $parsed_block['blockName'] &&
		! empty( $parsed_block['attrs']['slug'] ) &&
		str_starts_with( $parsed_block['attrs']['slug'], 'header' )
	) {
		$template_slug = 'header-third';
		if (
			function_exists( 'wporg_is_handbook' ) &&
			wporg_is_handbook() &&
			! wporg_is_handbook_landing_page()
		) {
			$parsed_block['attrs']['slug'] = $template_slug;
		} elseif ( 'command' === get_post_type() && is_single() ) {
			$parsed_block['attrs']['slug'] = $template_slug;
		} elseif ( is_parsed_post_type() && ! is_search() ) {
			$parsed_block['attrs']['slug'] = $template_slug;
		}
	}
	return $parsed_block;
}
