<?php
namespace DevHub;

function wporg_developer_code_reference_title_init() {
	register_block_type( __DIR__ . '/build', array(
         'render_callback' => 'DevHub\wporg_developer_code_reference_title_callback'
     ) );
}
add_action( 'init', 'DevHub\wporg_developer_code_reference_title_init' );

function wporg_developer_code_reference_title_callback() {
	$wrapper_attributes = get_block_wrapper_attributes();

	$output = '<h1 ' . $wrapper_attributes . '>';
	$output .= get_signature();
	$output .= "</h1>";

	return $output;
}
