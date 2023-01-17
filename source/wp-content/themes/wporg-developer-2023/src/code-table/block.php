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

	$title_block = sprintf(
		'<h2 class="wp-block-heading">%s</h2>',
		__( 'Related', 'wporg' )
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %s>%s %s</section>',
		$wrapper_attributes,
		$title_block,
		do_blocks( render_table( $attributes["headings"],  $attributes["rows"], $attributes["className"]) )
	);
}


/**
 * Render a table.
 *
 * @param array $releases A list of release versions.
 *
 * @return string Returns the table markup.
 */
function render_table( $headings, $rows, $className ) {
	$table = '<!-- wp:table {"className":"'. $className .'","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} --><figure class="wp-block-table" style="margin-top:var(--wp--preset--spacing--20)">';
	$table .= '<table>';
	$table .= '<thead>';
	$table .= '<tr>';
	foreach ( $headings as $heading ) {
		$table .= render_table_heading_row( $heading );
	}
	$table .= '</tr>';
	$table .= '</thead>';
	$table .= '<tbody>';
	foreach ( $rows as $row ) {
		$table .= render_table_row( $row );
	}
	$table .= '</tbody></table>';
	$table .= '</figure><!-- /wp:table -->';
	return $table;
}

/**
 * Render a release row.
 *
 * @param array $version A list of links and data about a given release version.
 *
 * @return string Returns the row markup.
 */
function render_table_heading_row( $heading ) {
	return '<th scope="col">' . $heading . '</th>';
}

/**
 * Render a release row.
 *
 * @param array $version A list of links and data about a given release version.
 *
 * @return string Returns the row markup.
 */
function render_table_row( $data ) {
	$row = '<tr>';
	foreach ( $data as $col ) {
		$row .= '<td>' . $col . '</td>';
	}
	$row .= '</tr>';

	return $row;
}