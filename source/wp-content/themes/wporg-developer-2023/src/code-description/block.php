<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Description;

use function DevHub\get_description;
use function DevHub\get_see_tags;

const PHP_CODE_SNIPPET_SCRIPT_URL = 'https://playground.wordpress.net/php-code-snippet.js';

/**
 * ID of the shared WordPress auto-prepend script tag. The script is identical
 * for every snippet, so a single element per page, printed in the footer,
 * serves them all.
 */
const PHP_CODE_SNIPPET_AUTO_PREPEND_ID = 'wporg-code-snippet-auto-prepend';

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
		wp_enqueue_script_module(
			'wporg-developer-php-code-snippet',
			PHP_CODE_SNIPPET_SCRIPT_URL,
			array(),
			null
		);

		// Print the shared WordPress auto-prepend script once per page. The
		// code reference blocks render twice per request (once for the table
		// of contents, once for the content), so the script is printed from
		// `wp_footer` rather than inline; registering the same callback again
		// is a no-op.
		add_action( 'wp_footer', __NAMESPACE__ . '\print_php_code_snippet_auto_prepend_script' );
	}

	// Emit the referenced setup Blueprint <script> tags once, up front.
	foreach ( array_keys( $used_setup_blueprints ) as $name ) {
		if ( isset( $setup_blueprints[ $name ] ) ) {
			$output .= render_php_code_snippet_blueprint_script(
				get_php_code_snippet_blueprint_id( $post_id, 'setup:' . $name ),
				$setup_blueprints[ $name ]
			);
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
	$processor = new PHP_Code_Snippet_Placeholder_Processor( $description );

	while ( $processor->next_token() ) {
		if ( \WP_HTML_Tag_Processor::COMMENT_AS_HTML_COMMENT !== $processor->get_comment_type() ) {
			continue;
		}

		$comment_text = $processor->get_modifiable_text();
		$prefix       = ' wp-parser-code-snippet:';
		if ( ! str_starts_with( $comment_text, $prefix ) || ! str_ends_with( $comment_text, ' ' ) ) {
			continue;
		}

		$index = substr( $comment_text, strlen( $prefix ), -1 );
		if ( ! ctype_digit( $index ) ) {
			continue;
		}

		$index = (int) $index;
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

	$code = $snippet['code'];

	// The two class_list() examples in WordPress 7.1 load WordPress themselves.
	// Remove once the parser strips this or Core ships without it.
	$preamble = "<?php\nrequire '/wordpress/wp-load.php';\n";
	if ( str_starts_with( $code, $preamble ) ) {
		$code = substr( $code, strlen( $preamble ) );
	}

	$code = wp_json_encode( $code, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES );
	if ( ! is_string( $code ) ) {
		return '';
	}

	if ( array_key_exists( 'expected_output', $snippet ) ) {
		if ( ! is_string( $snippet['expected_output'] ) ) {
			return '';
		}

		$expected_output = wp_json_encode( $snippet['expected_output'], JSON_HEX_TAG | JSON_UNESCAPED_SLASHES );
		if ( ! is_string( $expected_output ) ) {
			return '';
		}
	}

	$post_slug = get_post_field( 'post_name', $post_id );
	if ( ! $post_slug ) {
		$post_slug = 'example';
	}

	$inline_blueprint_id = get_php_code_snippet_blueprint_id( $post_id, 'inline:' . ( $index + 1 ) );
	$attributes = array(
		'name'                  => $post_slug . '-' . ( $index + 1 ) . '.php',
		'auto-prepend-script'   => '#' . PHP_CODE_SNIPPET_AUTO_PREPEND_ID,
		'implicit-php-open-tag' => true,
	);

	if ( array_key_exists( 'blueprint', $snippet ) ) {
		if (
			is_string( $snippet['blueprint'] ) &&
			isset( $setup_blueprints[ $snippet['blueprint'] ] ) &&
			( is_array( $setup_blueprints[ $snippet['blueprint'] ] ) || is_object( $setup_blueprints[ $snippet['blueprint'] ] ) )
		) {
			$attributes['blueprint']                 = get_php_code_snippet_blueprint_id( $post_id, 'setup:' . $snippet['blueprint'] );
			$used_blueprints[ $snippet['blueprint'] ] = true;
		} elseif ( is_array( $snippet['blueprint'] ) || is_object( $snippet['blueprint'] ) ) {
			$attributes['blueprint'] = $inline_blueprint_id;
		} else {
			// Keep snippets with unusable setup visible without offering a Run action that cannot succeed.
			$attributes['runnable'] = 'false';
		}
	}

	$output = '';

	if ( isset( $attributes['blueprint'] ) && $attributes['blueprint'] === $inline_blueprint_id ) {
		$output .= render_php_code_snippet_blueprint_script( $attributes['blueprint'], $snippet['blueprint'] );
	}

	$snippet_output = '<php-snippet>';
	$snippet_output .= wp_get_inline_script_tag(
		$code,
		array( 'type' => 'application/x-php+json' )
	);

	if ( array_key_exists( 'expected_output', $snippet ) ) {
		$snippet_output .= wp_get_inline_script_tag(
			$expected_output,
			array( 'type' => 'text/expected-output+json' )
		);
	}

	$snippet_output .= '</php-snippet>';

	$tags = new \WP_HTML_Tag_Processor( $snippet_output );
	if ( $tags->next_tag( 'php-snippet' ) ) {
		foreach ( $attributes as $name => $value ) {
			$tags->set_attribute( $name, $value );
		}
		$snippet_output = $tags->get_updated_html();
	}

	$output .= $snippet_output;

	return $output;
}

/**
 * Render a setup Blueprint script tag.
 *
 * @param string       $id        Script tag ID.
 * @param array|object $blueprint Blueprint data.
 * @return string
 */
function render_php_code_snippet_blueprint_script( $id, $blueprint ) {
	if ( is_array( $blueprint ) ) {
		// The parser's JSON importer uses associative arrays. Cast only the
		// Blueprint root back to its required JSON object type before encoding.
		$blueprint = (object) $blueprint;
	} elseif ( ! is_object( $blueprint ) ) {
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
 * Print the shared WordPress auto-prepend script tag for PHP code snippets.
 *
 * Hooked to `wp_footer` when a page renders snippets.
 */
function print_php_code_snippet_auto_prepend_script() {
	$auto_prepend_script = wp_json_encode(
		"<?php require_once '/wordpress/wp-load.php';",
		JSON_HEX_TAG | JSON_UNESCAPED_SLASHES
	);
	if ( ! is_string( $auto_prepend_script ) ) {
		return;
	}

	wp_print_inline_script_tag(
		$auto_prepend_script,
		array(
			'id'   => PHP_CODE_SNIPPET_AUTO_PREPEND_ID,
			'type' => 'application/x-php+json',
		)
	);
}

/**
 * Return a stable script ID for a snippet Blueprint.
 *
 * URL encoding preserves distinct Blueprint keys while removing the ASCII
 * whitespace that HTML IDs prohibit.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Blueprint key.
 * @return string
 */
function get_php_code_snippet_blueprint_id( $post_id, $key ) {
	return 'wporg-code-snippet-blueprint-' . absint( $post_id ) . '-' . rawurlencode( $key );
}

/**
 * HTML processor for replacing parser snippet placeholder comments.
 */
class PHP_Code_Snippet_Placeholder_Processor extends \WP_HTML_Tag_Processor {
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
