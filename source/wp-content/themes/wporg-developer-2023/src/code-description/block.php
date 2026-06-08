<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Description;

use function DevHub\get_description;
use function DevHub\get_see_tags;

const PHP_CODE_SNIPPET_SCRIPT_URL = 'https://playground.wordpress.net/php-code-snippet.js';

add_action( 'init', __NAMESPACE__ . '\init' );
add_filter( 'script_loader_tag', __NAMESPACE__ . '\add_php_code_snippet_script_type', 10, 3 );

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
	$snippets    = get_code_snippets_content( $post_id );

	if ( ! $description && ! $see_tags && ! $snippets ) {
		return '';
	}

	$output .= $description;
	$output .= $snippets;

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
	wp_enqueue_script(
		'wporg-developer-php-code-snippet',
		PHP_CODE_SNIPPET_SCRIPT_URL,
		array(),
		null,
		true
	);
}

/**
 * Load the PHP snippet web component as an ES module.
 *
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @param string $src    The script source URL.
 * @return string
 */
function add_php_code_snippet_script_type( $tag, $handle, $src ) {
	if ( 'wporg-developer-php-code-snippet' !== $handle ) {
		return $tag;
	}

	return sprintf(
		'<script type="module" src="%s"></script>' . "\n",
		esc_url( $src )
	);
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
	if ( ! isset( $snippet['code'] ) || ! is_string( $snippet['code'] ) ) {
		return '';
	}

	if ( isset( $snippet['expected_output'] ) && ! is_string( $snippet['expected_output'] ) ) {
		return '';
	}

	$code       = $snippet['code'];
	$attributes = array(
		'name' => get_php_code_snippet_name( $post_id, $index ),
	);

	if ( isset( $snippet['blueprint'] ) ) {
		if ( is_string( $snippet['blueprint'] ) && array_key_exists( $snippet['blueprint'], $setup_blueprints ) ) {
			$attributes['blueprint']                 = get_setup_blueprint_id( $post_id, $snippet['blueprint'] );
			$used_blueprints[ $snippet['blueprint'] ] = true;
		} elseif ( is_array( $snippet['blueprint'] ) || is_string( $snippet['blueprint'] ) ) {
			$attributes['blueprint'] = get_inline_blueprint_id( $post_id, $index );
		}
	}

	$output = '';

	if ( isset( $attributes['blueprint'] ) && $attributes['blueprint'] === get_inline_blueprint_id( $post_id, $index ) ) {
		$output .= render_blueprint_script( $attributes['blueprint'], $snippet['blueprint'] );
	}

	$output .= '<php-snippet' . render_php_code_snippet_attributes( $attributes ) . '>';
	$output .= '<script type="application/x-php">' . escape_script_data( $code ) . '</script>';
	$output .= '<script type="text/expected-output">' . escape_script_data( $snippet['expected_output'] ?? '' ) . '</script>';

	$output .= '</php-snippet>';

	return $output;
}

/**
 * Render a setup Blueprint script tag.
 *
 * @param string       $id        Script tag ID.
 * @param array|string $blueprint Blueprint data.
 * @return string
 */
function render_blueprint_script( $id, $blueprint ) {
	if ( is_array( $blueprint ) ) {
		$blueprint = wp_json_encode( $blueprint );
	}

	return sprintf(
		'<script id="%s" type="application/json">%s</script>',
		esc_attr( $id ),
		escape_script_data( (string) $blueprint )
	);
}

/**
 * Render attributes for the php-snippet element.
 *
 * @param array $attributes Attributes keyed by name.
 * @return string
 */
function render_php_code_snippet_attributes( $attributes ) {
	$output = '';

	foreach ( $attributes as $name => $value ) {
		$output .= sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( $value ) );
	}

	return $output;
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
 * Escape content inside script tags while preserving ordinary PHP/HTML text.
 *
 * @param string $content Script tag content.
 * @return string
 */
function escape_script_data( $content ) {
	return str_ireplace( '</script', '<\/script', $content );
}
