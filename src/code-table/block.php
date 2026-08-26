<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Code_Table;

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
		dirname( dirname( __DIR__ ) ) . '/build/code-table',
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
function render( $attributes ) {
	$table  = '<!-- wp:table {"className":"' . $attributes['className'] . '"} --><figure class="wp-block-table ' . $attributes['className'] . '">';
	$table .= '<table>';
	$table .= '<thead>';
	$table .= '<tr>';
	foreach ( $attributes['headings'] as $heading ) {
		$table .= render_table_heading_row( $heading );
	}
	$table .= '</tr>';
	$table .= '</thead>';
	$table .= '<tbody>';
	foreach ( $attributes['rows'] as $key => $value ) {
		$class_name = $key >= $attributes['itemsToShow'] ? 'wporg-hidden' : '';
		$table .= render_table_row( $value, $class_name );
	}
	$table .= '</tbody></table>';
	$table .= '</figure><!-- /wp:table -->';

	$row_count = count( $attributes['rows'] );

	if ( $row_count > $attributes['itemsToShow'] ) {
		$table .= render_toggle( $row_count - $attributes['itemsToShow'] );
	}

	$wrapper_attributes = get_block_wrapper_attributes();
	if ( isset( $attributes['id'] ) ) {
		$wrapper_attributes .= ' id="' . esc_attr( $attributes['id'] ) . '"';
	}

	return sprintf(
		'<section %1$s>%2$s</section>',
		$wrapper_attributes,
		do_blocks( $table )
	);
}

/**
 * Render a table heading cell.
 *
 * @param string[] $heading Cell content.
 *
 * @return string Returns the table header markup.
 */
function render_table_heading_row( $heading ) {
	return '<th scope="col">' . wp_kses_post( $heading ) . '</th>';
}

/**
 * Render a table row.
 *
 * @param string[] $data A list of table row data.
 *
 * @return string Returns the row markup.
 */
function render_table_row( $data, $class_name ) {
	$row = '<tr class="' . esc_attr( $class_name ) . '">';

	foreach ( $data as $col ) {
		$row .= '<td>' . wp_kses_post( $col ) . '</td>';
	}
	$row .= '</tr>';

	return $row;
}

/**
 * Render the expand/collapse toggle.
 *
 * @param number $remaining The number of remaining items to show.
 * @return string
 */
function render_toggle( $remaining ) {
	$show_more = sprintf(
		'<a class="wp-block-wporg-code-table-show-more" href="#">%s</a>',
		sprintf(
			/* translators: %s: Number of remaining items. */
			__( 'Show %s more', 'wporg' ),
			number_format_i18n( $remaining )
		)
	);

	$show_less = sprintf(
		'<a class="wp-block-wporg-code-table-show-less" href="#">%s</a>',
		__( 'Show less', 'wporg' )
	);

	return $show_more . $show_less;
}
