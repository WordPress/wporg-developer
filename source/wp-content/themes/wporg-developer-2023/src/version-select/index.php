<?php
/**
 * Block Name: Version Select
 * Description: Displays WordPress versions in a select element.
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Version_Select;

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
		dirname( dirname( __DIR__ ) ) . '/build/version-select',
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
	$versions = get_terms( 'wp-parser-since', array( 'order' => 'DESC' ) );

	if ( is_wp_error( $versions ) || empty( $versions ) ) {
		return '';
	}

	$mu_versions = array_filter(
		$versions,
		function( $version ) {
			return strpos( $version->name, 'MU' ) === 0;
		}
	);

	$versions = array_filter(
		$versions,
		function( $version ) {
			return strpos( $version->name, 'MU' ) !== 0;
		}
	);

	$options = '';
	foreach ( array_merge( $versions, $mu_versions ) as $version ) {
		$options .= '<option value="' . $version->name . '">' . $version->name . '</option>';
	}

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<div %1$s>
			<label class="screen-reader-text" for="%2$s">%3$s</label>
			<select name="%2$s" id="%2$s">%4$s</select>
		</div>',
		$wrapper_attributes,
		esc_attr( generate_id( $block->parsed_block ) ),
		esc_html( __( 'Select version', 'wporg' ) ),
		$options,
	);
}

/**
 * Generates a unique identifier.
 *
 * @param array $block Block object.
 * @return string      The unique identifier.
 */
function generate_id( $block ) {
	return 'wporg-version-select-' . md5( serialize( $block ) );
}
