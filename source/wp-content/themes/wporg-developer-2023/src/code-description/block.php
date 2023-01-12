<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Description;

use function DevHub\get_description;
use function DevHub\get_see_tags;

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
function render() {
	$content = wporg_developer_code_reference_description_render();

	if( empty( $content ) ) {
		return '';
	}

	$title_block = sprintf(
		'<!-- wp:wporg/code-reference-section-title {"title":"%s"} /-->',
		__( 'Description', 'wporg' )
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %s>%s %s</section>',
		$wrapper_attributes,
		do_blocks( $title_block ),
		$content
	);
}

function wporg_developer_code_reference_description_render() {
	$output = '';
	$description = get_description();
	$see_tags    = get_see_tags();

	if ( ! $description && ! $see_tags ) {
		return '';
	}

	$output .= $description;

	if ( $see_tags ) {
		$output .= '<h3>' . __( 'See also', 'wporg' ) . '</h3>';

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
			$see_ref = apply_filters( 'the_content', apply_filters( 'get_the_content', $see_ref ) );
			add_filter( 'the_content', 'wpautop' );

			$output .= '<li>' . $see_ref . "</li>\n";
		}
		$output .= '</ul>';
	}

	return $output;
}
