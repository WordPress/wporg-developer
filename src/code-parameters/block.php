<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Parameters;

use function DevHub\get_param_reference;
use function DevHub\get_params;

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
		dirname( dirname( __DIR__ ) ) . '/build/code-parameters',
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

	$params = get_params( $block->context['postId'] );

	if ( empty( $params ) ) {
		return '';
	}

	$title_block = sprintf(
		'<!-- wp:heading {"fontSize":"heading-5"} --><h2 class="wp-block-heading has-heading-5-font-size">%s</h2><!-- /wp:heading -->',
		__( 'Parameters', 'wporg' )
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %s>%s %s</section>',
		$wrapper_attributes,
		$title_block,
		get_param_content( $params )
	);
}

/**
 * Return code reference parameter html.
 *
 * @param [type] $params
 * @return string
 */
function get_param_content( $params ) {
	$output = '<dl>';

	foreach ( $params as $param ) {
		if ( ! empty( $param['variable'] ) ) {
			$output .= '<dt>';
			$output .= '<code>' . esc_html( $param['variable'] ) . '</code>';

			if ( ! empty( $param['types'] ) ) {
				$output .= '<span class="type">' . wp_kses_post( $param['types'] ) . '</span>';
			}

			if ( ! empty( $param['required'] ) && 'wp-parser-hook' !== get_post_type() ) {
				$output .= '<span class="required">' . esc_html( strtolower( $param['required'] ) ) . '</span>';
			}

			$output .= '</dt>';
		}

		$output .= '<dd>';
		$output .= '<div class="desc">';

		if ( ! empty( $param['content'] ) ) {
			$extra = get_param_reference( $param );

			if ( $extra ) {
				$output .= '<span class="description">' . wp_kses_post( $param['content'] ) . '</span>';
				$output .= '<details class="extended-description">';
				/* translators: 1: function name, 2: variable name */
				$output .= '<summary>' . esc_html( sprintf( __( 'More Arguments from %1$s( ... %2$s )', 'wporg' ), $extra['parent'], $extra['parent_var'] ) ) . '</summary>';
				$output .= '<span class="description">' . wp_kses_post( $extra['content'] ) . '</span>';
				$output .= '</details>';
			} else {
				$output .= '<span class="description">' . wp_kses_post( $param['content'] ) . '</span>';
			}
		}

		$output .= '</div>';

		if ( ! empty( $param['default'] ) ) {
			$output .= '<p class="default">' . __( 'Default:', 'wporg' ) . '<code>' . htmlentities( $param['default'] ) . '</code></p>';
		}

		$output .= '</dd>';
	}

	$output .= '</dl>';

	return $output;
}
