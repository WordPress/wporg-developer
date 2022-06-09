<?php
function wporg_developer_breadcrumb_init() {
	register_block_type( __DIR__ . '/build', array(
         'render_callback' => 'wporg_developer_breadcrumb_callback'
     ) );
}
add_action( 'init', 'wporg_developer_breadcrumb_init' );

function wporg_developer_breadcrumb_callback() {
	return wporg_developer_build_breadcrumb();
}

function wporg_developer_build_breadcrumb() {
	$wrapper_attributes = get_block_wrapper_attributes();

	$links = [];

	// First link is always link to main site.
	$links[] = sprintf( '<a href="%s/">%s</a>', esc_url( site_url() ), __( 'Developers', 'wporg' ) );

	// Second link is always link to handbook home page.
	$handbook_name = wporg_get_current_handbook_name();
	if ( wporg_is_handbook_landing_page() ) {
		$links[] = $handbook_name;
	} else {
		if ( ! empty( $handbook_name ) ) {
			$links[] = sprintf( '<a href="%s">%s</a>', esc_url( wporg_get_current_handbook_home_url() ), $handbook_name );
		}
	}

	// Add in links to current handbook page and all of its ancestor pages.
	$page = $current_page = get_post();
	$pages = [];

	do {
		$parent_id = wp_get_post_parent_id( $page );
		if ( $parent_id ) {
			$pages[] = sprintf( '<a href="%s">%s</a>', esc_url( get_permalink( $parent_id ) ), get_the_title( $parent_id ) );
			$page = $parent_id;
		}
	} while ( $parent_id );

	$pages = array_reverse( $pages );
	foreach ( $pages as $page ) {
		$links[] = $page;
	}

	// Last link is the current handbook page, unless it's the landing page.
	$title = get_the_title( $current_page );
	if ( ! empty( $title ) && ! wporg_is_handbook_landing_page() ) {
		$links[] = $title;
	}

	if ( count( $links ) === 1 ) {
		$links[] = "Current Page";
	}

	$output = '<div ' . $wrapper_attributes . '>';
	$output .= implode( '&nbsp; &#8250; &nbsp;', $links );
	$output .= '</div>';

	return $output;
}
