<?php
/**
 * Block Name: Resource Select
 * Description: Displays a selector to choose which resource to search.
 *
 * @package wporg
 */

namespace WordPressdotorg\Theme\Developer_2023\Resource_Select;

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
		dirname( dirname( __DIR__ ) ) . '/build/resource-select',
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
	global $wp_query;
	$search_query = $wp_query->query_vars['s'];

	$resources = array(
		array(
			'url' => '/',
			'label' => __( 'Code Reference', 'wporg' ),
		),
		array(
			'url' => '/coding-standards/',
			'label' => __( 'Coding Standards', 'wporg' ),
		),
		array(
			'url' => '/block-editor/',
			'label' => __( 'Block Editor', 'wporg' ),
		),
		array(
			'url' => '/apis/',
			'label' => __( 'Common APIs', 'wporg' ),
		),
		array(
			'url' => '/themes/',
			'label' => __( 'Themes', 'wporg' ),
		),
		array(
			'url' => '/plugins/',
			'label' => __( 'Plugins', 'wporg' ),
		),
		array(
			'url' => '/rest-api/',
			'label' => __( 'REST API', 'wporg' ),
		),
		array(
			'url' => '/cli/commands/',
			'label' => __( 'CLI Commands', 'wporg' ),
		),
		array(
			'url' => '/advanced-administration/',
			'label' => __( 'Advanced Administration', 'wporg' ),
		),
	);

	$options = '';
	foreach ( $resources as $resource ) {
		$selected = false;
	
		// Compare the source url with the uri minus query vars.
		if ( $resource['url'] === strtok( $_SERVER['REQUEST_URI'], '?' ) ) {
			$selected = true;
		}

		$options .= sprintf(
			'<option value="%1$s" %2$s>%3$s</option>',
			esc_attr( $resource['url'] . '?s=' . $search_query ),
			esc_attr( selected( $selected, true, false ) ),
			esc_html( $resource['label'] )
		);
	}

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<div %1$s>
			<label class="%2$s" for="%3$s">%4$s</label>
			<div class="wporg-resource-select-container">
				<select name="search-source" id="%3$s">%5$s</select>
			</div>
		</div>',
		$wrapper_attributes,
		esc_attr( isset( $attributes['hideLabelFromVision'] ) && true === $attributes['hideLabelFromVision'] ? 'screen-reader-text' : '' ),
		esc_attr( 'wp-block-wporg-resource-select' ),
		esc_html( $attributes['label'] ),
		$options,
	);
}
