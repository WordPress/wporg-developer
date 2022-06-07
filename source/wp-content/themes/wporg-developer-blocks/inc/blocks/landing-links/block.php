<?php
function wporg_developer_landing_links_init() {
	register_block_type( __DIR__ . '/build', array(
         'render_callback' => 'wporg_developer_landing_links_callback'
     ) );
}
add_action( 'init', 'wporg_developer_landing_links_init' );

function wporg_developer_landing_links_callback( $attributes, $content ) {
	$handbook = esc_html( $attributes['handbook'] );

	if ( 'not-selected' === $handbook ) {
		return 'Please select a handbook to display links for.';
	}

	$pages = get_posts( array(
		'post_type' => $handbook,
		'post_parent' => 0,
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'posts_per_page' => -1
	) );

	// Clip the first page as it's the handbook root.
	array_shift( $pages );

	$output = '<ul class="wp-block-wporg-developer-landing-links">';

	$counter = 0;
	foreach( (array) $pages as $page ) {
		if ( $counter > 12 ) {
			$output .= '<li class="nav-more">';
			$output .= '<a href="' . esc_attr( get_permalink( $pages[0]->ID ) ) . '">More</a> &rarr;';
			$output .= '</li>';
			break;
		}

		$output .= '<li>';
		$output .= '<a href="' . esc_attr( get_permalink( $page->ID ) ) . '">' . esc_html( $page->post_title ) . '</a>';
		$output .= '</li>';

		$counter++;
	}

	$output .= '</ul>';

	return $output;
}
