<?php
function wporg_developer_breadcrumb_init() {
	register_block_type( __DIR__ . '/build', array(
         'render_callback' => 'wporg_developer_breadcrumb_callback'
     ) );
}
add_action( 'init', 'wporg_developer_breadcrumb_init' );

function wporg_developer_breadcrumb_callback( $attributes, $content ) {
	return "Developers > Breadcrumb";
}

