<?php
/**
 * Suppress sections that don't belong in the markdown rendering.
 *
 * The html-to-md mu-plugin converts the full HTML template output to markdown
 * whenever a request asks for it (via ?output_format=md, ?output_format=markdown,
 * or an `Accept: text/markdown` header). Several theme blocks add content that
 * is meaningful in the browser but pure noise in a markdown body intended for
 * LLMs or scripted consumers — the in-article TOC, user-contributed notes, and
 * the comment form / login prompt.
 *
 * Short-circuit each of those blocks via `pre_render_block` so we don't even
 * run their render callbacks (which query comments, notes, etc.) on markdown
 * requests.
 *
 * @package WordPressdotorg\Theme\Developer_2023
 */

namespace WordPressdotorg\Theme\Developer_2023\Markdown_Output;

defined( 'ABSPATH' ) || exit;

/**
 * Block names suppressed when the request asked for markdown.
 */
const SUPPRESSED_BLOCKS = array(
	'wporg/sidebar-container',           // Sidebar wrapper — also adds the "↑ Back to top" tail.
	'wporg/table-of-contents',           // "In this article" sidebar TOC.
	'wporg/code-reference-comment-form', // Login prompt + comment form + heading.
	'wporg/code-reference-comments',     // Rendered list of user notes.
	'wporg/code-reference-user-notes',   // Standalone notes block.
	'wporg/code-reference-comment-edit', // Edit-note form.
);

/**
 * Theme patterns to suppress. Each is rendered as a `core/pattern` block with
 * the listed slug as an attribute — they're handbook-page footers that either
 * duplicate frontmatter ("First published" / "Last updated") or add UI-only
 * navigation that doesn't make sense in a static markdown render ("Previous: …",
 * "Next: …").
 */
const SUPPRESSED_PATTERNS = array(
	'wporg-developer-2023/article-meta',
	'wporg-developer-2023/article-meta-block-editor',
	'wporg-developer-2023/article-meta-github',
	'wporg-developer-2023/handbook-pagination',
);

add_filter( 'pre_render_block', __NAMESPACE__ . '\\maybe_suppress_block', 10, 2 );
add_filter( 'render_block_data', __NAMESPACE__ . '\\maybe_expand_code_table', 10 );
add_filter( 'render_block_wporg/code-reference-parameters', __NAMESPACE__ . '\\maybe_space_param_dt', 10 );
add_filter( 'html_to_markdown_frontmatter_fields', __NAMESPACE__ . '\\strip_author_field', 10 );

/**
 * Detection mirrors the html-to-md mu-plugin so this stays accurate even when
 * the mu-plugin's detection changes (since these run in the same request).
 */
function is_markdown_request(): bool {
	$by_query  = in_array( $_GET['output_format'] ?? '', array( 'md', 'markdown' ), true );
	$by_accept = 1 === preg_match( '~^text/(?:x-)?markdown(?:[,;]|$)~', $_SERVER['HTTP_ACCEPT'] ?? '' );

	return $by_query || $by_accept;
}

/**
 * Short-circuit the suppressed blocks on markdown requests.
 *
 * Returning a non-null value from `pre_render_block` skips the entire render
 * pipeline for that block — its render_callback never fires, so we save the
 * comments / notes queries that those callbacks would otherwise run.
 *
 * @param string|null $pre   The pre-rendered HTML, or null to render normally.
 * @param array       $block The parsed block.
 * @return string|null
 */
function maybe_suppress_block( $pre, $block ) {
	if ( null !== $pre ) {
		return $pre;
	}
	if ( ! is_markdown_request() ) {
		return $pre;
	}

	$name = $block['blockName'] ?? '';

	if ( in_array( $name, SUPPRESSED_BLOCKS, true ) ) {
		return '';
	}

	if ( 'core/pattern' === $name && in_array( $block['attrs']['slug'] ?? '', SUPPRESSED_PATTERNS, true ) ) {
		return '';
	}

	return $pre;
}

/**
 * Force `wporg/code-table` to render every row on markdown requests.
 *
 * The block hides rows beyond `itemsToShow` (default 5) behind a JS-driven
 * "Show more" / "Show less" toggle. In the markdown rendering the JS never
 * runs, so the hidden rows are silently lost. Bumping the attribute keeps
 * every Changelog / Related / etc. row in the body.
 *
 * @param array $parsed_block The parsed block data.
 * @return array
 */
function maybe_expand_code_table( $parsed_block ) {
	if ( 'wporg/code-table' !== ( $parsed_block['blockName'] ?? '' ) ) {
		return $parsed_block;
	}
	if ( ! is_markdown_request() ) {
		return $parsed_block;
	}

	$parsed_block['attrs']['itemsToShow'] = PHP_INT_MAX;

	return $parsed_block;
}

/**
 * Insert spaces between the inline spans inside each parameter `<dt>` so the
 * markdown rendering reads `$var string required` instead of `$varstringrequired`.
 *
 * The `<dt>` is `<code>$var</code><span class="type">…</span><span class="required">…</span>`
 * with no whitespace between siblings — CSS handles the layout in the browser,
 * so adding plain ASCII whitespace here is invisible to web view but gives the
 * HTML-to-markdown converter the separator it needs.
 *
 * @param string $html Rendered block HTML.
 * @return string
 */
function maybe_space_param_dt( $html ) {
	if ( ! is_markdown_request() ) {
		return $html;
	}

	return str_replace(
		array( '</code><span class="type">', '</span><span class="required">' ),
		array( '</code> <span class="type">', '</span> <span class="required">' ),
		$html
	);
}

/**
 * Drop the `author` YAML frontmatter field on developer.wordpress.org.
 *
 * The post author rarely reflects who wrote a developer-docs page — handbook
 * pages get re-attributed during imports, code-reference posts are owned by
 * the parser bot, and the author line just consumes tokens without telling
 * the consumer anything actionable.
 *
 * @param array $fields YAML key => value pairs.
 * @return array
 */
function strip_author_field( $fields ) {
	unset( $fields['author'] );

	return $fields;
}
