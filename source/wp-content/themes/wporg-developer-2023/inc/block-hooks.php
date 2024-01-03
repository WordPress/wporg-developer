<?php
/**
 * Blocks hooks.
 *
 * @package wporg-developer-2023
 */

use function DevHub\is_parsed_post_type;

add_filter( 'render_block', __NAMESPACE__ . '\filter_article_meta_block', 10, 2 );

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
 * Filters an article meta block and conditionally removes it.
 *
 * @param string $block_content
 * @param array  $block
 * @return string
 */
function filter_article_meta_block( $block_content, $block ) {	
	// Not all handbooks come from GitHub.
	$local_handbooks = array( 'plugin-handbook', 'theme-handbook' );

	if ( 'wporg/article-meta-github' === $block['blockName'] ) {
		$post_type = get_post_type();

		if ( in_array( $post_type, $local_handbooks ) ) {
			return '';
		}

		// The block editor handbook doesn't have a changelog.
		// We only know it's the changelog because of the linkURL attribute.
		if ( 'blocks-handbook' === $post_type && '[article_changelog_link]' === $block['attrs']['linkURL'] ) {
			return '';
		}
	}

	if ( 'wporg/article-meta-date' === $block['blockName'] ) {
		$post_type = get_post_type();

		// Last modified date for handbooks from GitHub is the last import date, rather than the edited date.
		// Don't display until we have the edited date from GitHub.
		// See https://github.com/WordPress/wporg-developer/issues/456
		if ( wporg_is_handbook_post_type() && ! in_array( $post_type, $local_handbooks ) && '[last_updated]' === $block['attrs']['heading'] ) {
			return '';
		}
	}

	return $block_content;
}
