<?php
namespace DevHub;

function wporg_developer_code_reference_changelog_init() {
	register_block_type( __DIR__ . '/build', array(
         'render_callback' => 'DevHub\wporg_developer_code_reference_changelog_callback'
     ) );
}
add_action( 'init', 'DevHub\wporg_developer_code_reference_changelog_init' );

function wporg_developer_code_reference_changelog_callback() {
	$wrapper_attributes = get_block_wrapper_attributes();

	$output = '<section ' . $wrapper_attributes . '>';
	$output .= '';
	$output .= "</section>";

	return $output;
}
