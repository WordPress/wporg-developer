<?php
function wporg_developer_toc_block_init() {
	register_block_type( __DIR__ . '/build', array(
         'render_callback' => 'wporg_developer_toc_block_callback'
     ) );

	 add_filter( 'handbook_display_toc', '__return_false' );
}
add_action( 'init', 'wporg_developer_toc_block_init' );

function wporg_developer_toc_block_callback() {
	$wrapper_attributes = get_block_wrapper_attributes();

	$toc = wporg_developer_toc_get_links();

	if ( empty( $toc ) ) {
		return;
	}

	$output = '<section ' . $wrapper_attributes . '>';
	$output .= $toc;
	$output .= '</section>';

	return $output;
}

function wporg_developer_toc_get_links() {
	$toc = new WPorg_Handbook_TOC( [ 'page' ], [
		'header_text' => 'On this Page',
		'override_filter' => true
	] );

	$items = $toc->get_items( get_the_content() );

	if ( empty( $items ) ) {
		return false;
	}

	$toc = $toc->build_toc( $items );

	return $toc;
}
