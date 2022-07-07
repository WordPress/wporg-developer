<?php
namespace DevHub;

function wporg_developer_code_reference_params_init() {
	register_block_type( __DIR__ . '/build', array(
         'render_callback' => 'DevHub\wporg_developer_code_reference_params_callback'
     ) );
}
add_action( 'init', 'DevHub\wporg_developer_code_reference_params_init' );

function wporg_developer_code_reference_params_callback() {
	$wrapper_attributes = get_block_wrapper_attributes();
	$params = get_params();

	$output = '<section ' . $wrapper_attributes . '>';
	$output .= wporg_developer_code_reference_build_params( $params );
	$output .= "</section>";

	return $output;
}

function wporg_developer_code_reference_build_params( $params ) {
	$output = '<dl>';

	foreach ( $params as $param ) {
		if ( ! empty( $param['variable'] ) ) {
			$output .= '<dt>';
			$output .= '<code>' . esc_html( $param['variable'] ) . '</code>';

			if ( ! empty( $param['types'] ) ) {
				$output .= '<span class="type">' . wp_kses_post( $param['types'] ) . '</span>';
			}

			if ( ! empty( $param['required'] ) && 'wp-parser-hook' !== get_post_type() ) {
				$output .= '<span class="required">' . esc_html( $param['required'] ) . '</span>';
			}

			$output .= '</dt>';
		}

		$output .= '<dd>';
		$output .= '<div class="desc">';

		if ( ! empty( $param['content'] ) ) {
			if ( $extra = get_param_reference( $param ) ) {
				$output .= '<span class="description">' . wp_kses_post( $param['content'] ) . '</span>';
				$output .= '<details class="extended-description">';
				$output .= '<summary>' . esc_html( sprintf( __( 'More Arguments from %s( ... %s )', 'wporg' ), $extra[ 'parent' ], $extra['parent_var'] ) ) . '</summary>';
				$output .= '<span class="description">' . wp_kses_post( $extra['content'] ) . '</span>';
				$output .= '</details>';
			} else {
				$output .= '<span class="description">' . wp_kses_post( $param['content'] ) . '</span>';
			}
		}

		$output .= '</div>';

		if ( ! empty( $param['default'] ) ) {
			$output .= '<p class="default">' . __( 'Default:', 'wporg' ) . '<code>' . htmlentities( $param['default'] ) . '</code></p>';
		}

		$output .= '</dd>';
	}

	$output .= '</dl>';

	return $output;
}
