<?php
function wporg_developer_sidebar_navigation_init() {
	register_block_type( __DIR__ . '/build', array(
         'render_callback' => 'wporg_developer_sidebar_navigation_callback'
     ) );
}
add_action( 'init', 'wporg_developer_sidebar_navigation_init' );

function wporg_developer_sidebar_navigation_callback( $attributes, $content ) {
	if ( ! wporg_is_handbook_post_type() ) {
		return wporg_developer_sidebar_navigation_render(
			'<details class="tree-nav__item is-expandable">
				<summary class="tree-nav__item-title"><a href="">Page One</a></summary>
				<details class="tree-nav__item"><summary class="tree-nav__item-title"><a href="">Sub Page One</a></summary></details>
			</details>
			<details class="tree-nav__item"><summary class="tree-nav__item-title"><a href="">Page Two</a></summary></details>
			<details class="tree-nav__item"><summary class="tree-nav__item-title"><a href="">Page Three</a></summary></details>
			<details class="tree-nav__item"><summary class="tree-nav__item-title"><a href="">Page Four</a></summary></details>
		');
	}

	include_once __DIR__ . '/walker.php';

	$pages = wp_list_pages( array(
		'post_type' => get_post_type(),
		'walker' => new Wporg_Developer_Sidebar_Navigation_Walker,
		'title_li' => '',
		'echo' => false,
	) );

	return wporg_developer_sidebar_navigation_render( $pages );
}

function wporg_developer_sidebar_navigation_render( $pages ) {
	$output = '<div class="wp-block-wporg-developer-sidebar-navigation">';
	$output .= '<nav class="tree-nav">' . $pages . '</nav>';
	$output .= "</div>";

	return $output;
}
