<?php
namespace DevHub;

function wporg_developer_code_reference_description_init() {
	register_block_type( __DIR__ . '/build', array(
         'render_callback' => 'DevHub\wporg_developer_code_reference_description_callback'
     ) );
}
add_action( 'init', 'DevHub\wporg_developer_code_reference_description_init' );

function wporg_developer_code_reference_description_callback() {
	$wrapper_attributes = get_block_wrapper_attributes();

	$output = '<section ' . $wrapper_attributes . '>';
	$output .= wporg_developer_code_reference_description_render();
	$output .= "</section>";

	return $output;
}

function wporg_developer_code_reference_description_render() {
	$output = '';
	$description = get_description();
	$see_tags    = get_see_tags();

	if ( ! $description && ! $see_tags ) {
		return '';
	}

	$output .= $description;

	if ( $see_tags ) {
		$output .= '<h3>' . __( 'See also', 'wporg' ) . '</h3>';

		$output .= '<ul>';
		foreach ( $see_tags as $tag ) {
			$see_ref = '';
			if ( ! empty( $tag['refers'] ) ) {
				$see_ref .= '{@see ' . $tag['refers'] . '}';
			}
			if ( ! empty( $tag['content'] ) ) {
				if ( $see_ref ) {
					$see_ref .= ': ';
				}
				$see_ref .= $tag['content'];
			}
			// Process text for auto-linking, etc.
			remove_filter( 'the_content', 'wpautop' );
			$see_ref = apply_filters( 'the_content', apply_filters( 'get_the_content', $see_ref ) );
			add_filter( 'the_content', 'wpautop' );

			$output .= '<li>' . $see_ref . "</li>\n";
		}
		$output .= '</ul>';
	}

	return $output;
}
