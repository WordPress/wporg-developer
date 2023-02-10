<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Command_Subcommand;

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
		dirname( dirname( __DIR__ ) ) . '/build/command-subcommand',
		array(
			'render_callback' => __NAMESPACE__ . '\render',
		)
	);
}

/**
 * Render the block content.
 *
 * @return string Returns the block markup.
 */
function render( $attributes, $content, $block ) {
	$children = get_children(
		array(
			'post_parent'    => get_the_ID(),
			'post_type'      => 'command',
			'posts_per_page' => 250,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	// Append subcommands if they exist
	if ( ! $children ) {
		return '';
	}

	$title_block = sprintf(
		'<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">%1$s</h3><!-- /wp:heading -->',
		__( 'Subcommands', 'wporg' )
	);

	$content =
	'
	<table>
		<thead>
		<tr>
			<th>Name</th>
			<th>Description</th>
		</tr>
	<tbody>
	';

	foreach ( $children as $child ) {
		$content .= '<tr>';
		$content .= '<td>';
		$content .= '<a href="' . apply_filters( 'the_permalink', get_permalink( $child->ID ) ) . '">';
		$content .= apply_filters( 'the_title', $child->post_title );
		$content .= '</a>';
		$content .= '</td>';
		$content .= '<td>';
		$content .= apply_filters( 'the_excerpt', $child->post_excerpt );
		$content .= '</td>';
		$content .= '</tr>';
	}

	$content .=
	'
	</tbody>
	</table>
	';

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %1$s>%2$s</section>',
		$wrapper_attributes,
		$content,
	);
}
