<?php
namespace DevHub;

function wporg_developer_code_reference_summary_init() {
	register_block_type( __DIR__ . '/build', array(
         'render_callback' => 'DevHub\wporg_developer_code_reference_summary_callback'
     ) );
}
add_action( 'init', 'DevHub\wporg_developer_code_reference_summary_init' );

function wporg_developer_code_reference_summary_callback() {
	$wrapper_attributes = get_block_wrapper_attributes();

	$output = '<section ' . $wrapper_attributes . '>';
	$output .= get_summary();
	$output .= "</section>";

	return $output;
}
