<?php
function wporg_developer_sidebar_navigation_init() {
	register_block_type( __DIR__ . '/build', array(
         'render_callback' => 'wporg_developer_sidebar_navigation_callback'
     ) );
}
add_action( 'init', 'wporg_developer_sidebar_navigation_init' );

function wporg_developer_sidebar_navigation_callback( $attributes, $content ) {
	return "Sidebar Nav";
}
