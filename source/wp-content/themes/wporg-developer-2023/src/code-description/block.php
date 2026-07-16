<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Description;

use function DevHub\get_description;
use function DevHub\get_see_tags;

const PHP_CODE_SNIPPET_SCRIPT_URL = 'https://playground.wordpress.net/php-code-snippet.js';

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
		dirname( dirname( __DIR__ ) ) . '/build/code-description',
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

	$content = get_description_content( $block->context['postId'] );

	if ( empty( $content ) ) {
		return '';
	}

	$title_block = sprintf(
		'<!-- wp:heading {"fontSize":"heading-5"} --><h2 class="wp-block-heading has-heading-5-font-size">%s</h2><!-- /wp:heading -->',
		__( 'Description', 'wporg' )
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
 * Return code description html.
 *
 * @return string
 */
function get_description_content( $post_id ) {
	$output = '';

	$description = get_description( $post_id );
	$see_tags    = get_see_tags( $post_id );

	$snippets = get_post_meta( $post_id, '_wp-parser_code_snippets', true );
	if ( ! is_array( $snippets ) ) {
		$snippets = array();
	}
	$setup_blueprints = get_post_meta( $post_id, '_wp-parser_setup_blueprints', true );
	if ( ! is_array( $setup_blueprints ) ) {
		$setup_blueprints = array();
	}

	$used_setup_blueprints = array();
	$placed                = array();

	// Render each snippet in place, where the parser left its placeholder
	// ( <!-- wp-parser-code-snippet:N --> ), so snippets stay between the
	// surrounding prose instead of collapsing to the end of the description.
	$description = render_php_code_snippet_placeholders(
		(string) $description,
		$post_id,
		$snippets,
		$setup_blueprints,
		$used_setup_blueprints,
		$placed
	);

	// Fallback: any snippet without a placeholder (e.g. metadata imported from an
	// older parser that stripped fences without leaving markers) is appended, so
	// nothing is silently dropped.
	$appended = '';
	foreach ( array_values( $snippets ) as $index => $snippet ) {
		if ( isset( $placed[ $index ] ) ) {
			continue;
		}
		if ( ! is_array( $snippet ) || ( $snippet['type'] ?? '' ) !== 'php-code-snippet' ) {
			continue;
		}
		$appended .= render_php_code_snippet( $post_id, $index, $snippet, $setup_blueprints, $used_setup_blueprints );
	}

	$has_snippets = ! empty( $placed ) || '' !== $appended;

	if ( ! $description && ! $see_tags && ! $has_snippets ) {
		return '';
	}

	if ( $has_snippets ) {
		enqueue_php_code_snippet_script();
	}

	// Emit the referenced setup Blueprint <script> tags once, up front.
	foreach ( array_keys( $used_setup_blueprints ) as $name ) {
		if ( isset( $setup_blueprints[ $name ] ) ) {
			$output .= render_blueprint_script( get_setup_blueprint_id( $post_id, $name ), $setup_blueprints[ $name ] );
		}
	}

	$output .= $description;

	if ( '' !== $appended ) {
		$output .= '<div class="wporg-code-snippets">' . $appended . '</div>';
	}

	if ( $see_tags ) {
		$output .= '<h3 class="has-heading-5-font-size">' . __( 'See also', 'wporg' ) . '</h3>';

		$output .= '<ul>';
		foreach ( $see_tags as $tag ) {
			$see_ref = '';
			if ( ! empty( $tag['refers'] ) ) {
				$see_ref .= '{@see ' . $tag['refers'] . '}';
			}
			if ( ! empty( $tag['content'] ) ) {
				if ( $see_ref ) {
					$see_ref .= ': ';
				}
				$see_ref .= $tag['content'];
			}
			// Process text for auto-linking, etc.
			remove_filter( 'the_content', 'wpautop' );

			// Remove the filter that adds the code reference block to the content.
			remove_filter( 'the_content', 'DevHub\filter_code_content', 4 );

			$see_ref = apply_filters( 'the_content', apply_filters( 'get_the_content', $see_ref ) );

			// Re-add the filter that adds this block to the content.
			add_filter( 'the_content', 'DevHub\filter_code_content', 4 );

			add_filter( 'the_content', 'wpautop' );

			$output .= '<li>' . $see_ref . "</li>\n";
		}
		$output .= '</ul>';
	}

	return $output;
}

/**
 * Return runnable code examples parsed from the DocBlock description.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function get_code_snippets_content( $post_id ) {
	$snippets = get_post_meta( $post_id, '_wp-parser_code_snippets', true );

	if ( empty( $snippets ) || ! is_array( $snippets ) ) {
		return '';
	}

	$setup_blueprints = get_post_meta( $post_id, '_wp-parser_setup_blueprints', true );
	if ( ! is_array( $setup_blueprints ) ) {
		$setup_blueprints = array();
	}

	$snippet_output        = '';
	$used_setup_blueprints = array();

	foreach ( array_values( $snippets ) as $index => $snippet ) {
		if ( ! is_array( $snippet ) || ( $snippet['type'] ?? '' ) !== 'php-code-snippet' ) {
			continue;
		}

		$snippet_output .= render_php_code_snippet( $post_id, $index, $snippet, $setup_blueprints, $used_setup_blueprints );
	}

	if ( '' === $snippet_output ) {
		return '';
	}

	enqueue_php_code_snippet_script();

	$output = '<div class="wporg-code-snippets">';

	foreach ( array_keys( $used_setup_blueprints ) as $name ) {
		$output .= render_blueprint_script( get_setup_blueprint_id( $post_id, $name ), $setup_blueprints[ $name ] );
	}

	$output .= $snippet_output;
	$output .= '</div>';

	return $output;
}

/**
 * Enqueue the web component that renders and runs PHP snippets.
 */
function enqueue_php_code_snippet_script() {
	wp_enqueue_script_module(
		'wporg-developer-php-code-snippet',
		PHP_CODE_SNIPPET_SCRIPT_URL,
		array(),
		null
	);
}

/**
 * Render parser snippet placeholders in HTML comments.
 *
 * @param string $description      Description HTML.
 * @param int    $post_id          Post ID.
 * @param array  $snippets         Parsed snippets.
 * @param array  $setup_blueprints Reusable setup Blueprints keyed by name.
 * @param array  $used_blueprints  Reusable setup Blueprint names referenced by rendered snippets.
 * @param array  $placed           Snippet indexes rendered in place.
 * @return string
 */
function render_php_code_snippet_placeholders( $description, $post_id, $snippets, $setup_blueprints, &$used_blueprints, &$placed ) {
	$processor = new Code_Snippet_Placeholder_Processor( $description );

	while ( $processor->next_token() ) {
		if ( \WP_HTML_Tag_Processor::COMMENT_AS_HTML_COMMENT !== $processor->get_comment_type() ) {
			continue;
		}

		$index = get_php_code_snippet_placeholder_index( $processor->get_modifiable_text() );
		if ( null === $index ) {
			continue;
		}

		if ( ! isset( $snippets[ $index ] ) || ! is_array( $snippets[ $index ] ) ) {
			$processor->replace_current_token( '' );
			continue;
		}

		$snippet_output = render_php_code_snippet(
			$post_id,
			$index,
			$snippets[ $index ],
			$setup_blueprints,
			$used_blueprints
		);
		$processor->replace_current_token( $snippet_output );
		if ( '' !== $snippet_output ) {
			$placed[ $index ] = true;
		}
	}

	return $processor->get_updated_html();
}

/**
 * Return the parser snippet placeholder index from exact HTML comment text.
 *
 * @param string $comment_text HTML comment text.
 * @return int|null
 */
function get_php_code_snippet_placeholder_index( $comment_text ) {
	$prefix = ' wp-parser-code-snippet:';
	$suffix = ' ';

	if ( ! str_starts_with( $comment_text, $prefix ) || ! str_ends_with( $comment_text, $suffix ) ) {
		return null;
	}

	$index = substr( $comment_text, strlen( $prefix ), -strlen( $suffix ) );
	if ( ! ctype_digit( $index ) ) {
		return null;
	}

	return (int) $index;
}

/**
 * Render a single PHP code snippet.
 *
 * @param int   $post_id          Post ID.
 * @param int   $index            Zero-based snippet index.
 * @param array $snippet          Snippet data.
 * @param array $setup_blueprints Reusable setup Blueprints keyed by name.
 * @param array $used_blueprints  Reusable setup Blueprint names referenced by rendered snippets.
 * @return string
 */
function render_php_code_snippet( $post_id, $index, $snippet, $setup_blueprints, &$used_blueprints ) {
	if ( ( $snippet['type'] ?? '' ) !== 'php-code-snippet' ) {
		return '';
	}

	if ( ! isset( $snippet['code'] ) || ! is_string( $snippet['code'] ) ) {
		return '';
	}

	if ( array_key_exists( 'expected_output', $snippet ) && ! is_string( $snippet['expected_output'] ) ) {
		return '';
	}

	$code       = $snippet['code'];
	$attributes = array(
		'name' => get_php_code_snippet_name( $post_id, $index ),
	);

	if ( array_key_exists( 'blueprint', $snippet ) ) {
		if (
			is_string( $snippet['blueprint'] ) &&
			isset( $setup_blueprints[ $snippet['blueprint'] ] ) &&
			is_array( $setup_blueprints[ $snippet['blueprint'] ] )
		) {
			$attributes['blueprint']                 = get_setup_blueprint_id( $post_id, $snippet['blueprint'] );
			$used_blueprints[ $snippet['blueprint'] ] = true;
		} elseif ( is_array( $snippet['blueprint'] ) ) {
			$attributes['blueprint'] = get_inline_blueprint_id( $post_id, $index );
		} else {
			// Keep snippets with unusable setup visible without offering a Run action that cannot succeed.
			$attributes['runnable'] = 'false';
		}
	}

	$output = '';

	if ( isset( $attributes['blueprint'] ) && $attributes['blueprint'] === get_inline_blueprint_id( $post_id, $index ) ) {
		$output .= render_blueprint_script( $attributes['blueprint'], $snippet['blueprint'] );
	}

	$snippet_output = '<php-snippet>';
	$snippet_output .= wp_get_inline_script_tag(
		escape_php_source_script_contents( $code ),
		array( 'type' => 'application/x-php' )
	);

	if ( array_key_exists( 'expected_output', $snippet ) ) {
		$snippet_output .= wp_get_inline_script_tag(
			$snippet['expected_output'],
			array( 'type' => 'text/expected-output' )
		);
	}

	$snippet_output .= '</php-snippet>';
	$output         .= render_php_code_snippet_element( $snippet_output, $attributes );

	return $output;
}

/**
 * Render a setup Blueprint script tag.
 *
 * @param string $id        Script tag ID.
 * @param array  $blueprint Blueprint data.
 * @return string
 */
function render_blueprint_script( $id, $blueprint ) {
	if ( ! is_array( $blueprint ) ) {
		return '';
	}

	$blueprint = wp_json_encode( $blueprint, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES );
	if ( ! is_string( $blueprint ) ) {
		return '';
	}

	return wp_get_inline_script_tag(
		$blueprint,
		array(
			'id'   => $id,
			'type' => 'application/json',
		)
	);
}

/**
 * Render the php-snippet element with its attributes.
 *
 * @param string $html       php-snippet element markup.
 * @param array  $attributes Attributes keyed by name.
 * @return string
 */
function render_php_code_snippet_element( $html, $attributes ) {
	$tags = new \WP_HTML_Tag_Processor( $html );
	if ( ! $tags->next_tag( 'php-snippet' ) ) {
		return $html;
	}

	foreach ( $attributes as $name => $value ) {
		$tags->set_attribute( $name, $value );
	}

	return $tags->get_updated_html();
}

/**
 * Return a stable snippet file name.
 *
 * @param int $post_id Post ID.
 * @param int $index   Zero-based snippet index.
 * @return string
 */
function get_php_code_snippet_name( $post_id, $index ) {
	$slug = get_post_field( 'post_name', $post_id );
	if ( ! $slug ) {
		$slug = 'example';
	}

	return sanitize_file_name( $slug . '-' . ( $index + 1 ) . '.php' );
}

/**
 * Return a stable script ID for a reusable setup Blueprint.
 *
 * @param int    $post_id Post ID.
 * @param string $name    Blueprint name.
 * @return string
 */
function get_setup_blueprint_id( $post_id, $name ) {
	$slug = sanitize_title( $name );

	if ( '' === $slug ) {
		$slug = substr( md5( $name ), 0, 8 );
	}

	return 'wporg-code-snippet-blueprint-' . absint( $post_id ) . '-' . $slug;
}

/**
 * Return a stable script ID for an inline setup Blueprint.
 *
 * @param int $post_id Post ID.
 * @param int $index   Zero-based snippet index.
 * @return string
 */
function get_inline_blueprint_id( $post_id, $index ) {
	return 'wporg-code-snippet-blueprint-' . absint( $post_id ) . '-' . absint( $index + 1 );
}

/**
 * Escape PHP source for embedding in script tags.
 *
 * @param string $content Script tag content.
 * @return string
 */
function escape_php_source_script_contents( $content ) {
	return str_ireplace( '</script', '<\/script', $content );
}

/**
 * HTML processor for replacing parser snippet placeholder comments.
 */
class Code_Snippet_Placeholder_Processor extends \WP_HTML_Tag_Processor {
	/**
	 * Replace the currently matched token with HTML.
	 *
	 * @param string $html Replacement HTML.
	 */
	public function replace_current_token( $html ) {
		$bookmark_name = 'wporg-code-snippet-placeholder';

		$this->set_bookmark( $bookmark_name );
		$span                    = $this->bookmarks[ $bookmark_name ];
		$this->lexical_updates[] = new \WP_HTML_Text_Replacement( $span->start, $span->length, $html );
		$this->release_bookmark( $bookmark_name );
	}
}
