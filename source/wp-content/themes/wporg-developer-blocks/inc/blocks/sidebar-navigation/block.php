<?php
function wporg_developer_sidebar_navigation_init() {
	register_block_type( __DIR__ . '/build', array(
         'render_callback' => 'wporg_developer_sidebar_navigation_callback'
     ) );
}
add_action( 'init', 'wporg_developer_sidebar_navigation_init' );

function wporg_developer_sidebar_navigation_callback( $attributes, $content ) {
	include_once __DIR__ . '/walker.php';

	$pages = wp_list_pages( array(
		'post_type' => 'blocks-handbook',
		'walker' => new Wporg_Developer_Sidebar_Navigation_Walker,
		'title_li' => '',
		'echo' => false,
	) );

	$output = '<div class="wp-block-wporg-developer-sidebar-navigation">';
	$output .= '<nav class="tree-nav">' . $pages . '</nav>';
	$output .= "</div>";

	return $output;
}

