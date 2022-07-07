<?php
namespace DevHub;

function wporg_developer_code_reference_source_init() {
	register_block_type( __DIR__ . '/build', array(
         'render_callback' => 'DevHub\wporg_developer_code_reference_source_callback'
     ) );
}
add_action( 'init', 'DevHub\wporg_developer_code_reference_source_init' );

function wporg_developer_code_reference_source_callback() {
	$wrapper_attributes = get_block_wrapper_attributes();

	$output = '<section ' . $wrapper_attributes . '>';
	$output .= wporg_developer_code_reference_source_render();
	$output .= "</section>";

	return $output;
}

function wporg_developer_code_reference_source_render() {
	$source_file = get_source_file();
	$output = '';

	if ( ! empty( $source_file ) ) {
		$source_code = post_type_has_source_code() ? get_source_code() : '';

		$output .= '<p>' .
			sprintf(
				__( 'File: %s.', 'wporg' ),
				'<code>' . esc_html( $source_file ) . '</code>'
			) .
			sprintf(
				'<a href="%s">%s</a>',
				esc_url( get_source_file_archive_link( $source_file ) ),
				__( 'View all references', 'wporg' )
			) . '</p>';

		if ( ! empty( $source_code ) ) {
			$output .= do_blocks(
				sprintf(
					'<!-- wp:code {"lineNumbers":true} --><pre class="wp-block-code" data-start="%1$s" aria-label="%2$s"><code lang="php" class="language-php line-numbers">%3$s</code></pre><!-- /wp:code -->',
					esc_attr( get_post_meta( get_the_ID(), '_wp-parser_line_num', true ) ),
					__( 'Function source code', 'wporg' ),
					htmlentities( $source_code )
				)
			);

			$output .= '<p class="source-code-links">
				<span><a href="' . esc_attr( get_source_file_link() ) . '">' . __( 'View on Trac', 'wporg' ) . '</a></span>
				<span><a href="' . esc_attr( get_github_source_file_link() ) . '">' . __( 'View on GitHub', 'wporg' ) . '</a></span>
			</p>';
		} else {
			$output .= '<p><a href="' . esc_attr( get_source_file_link() ) . '">' . __( 'View on Trac', 'wporg' ) . '</a></p>';
		}
	}

	return $output;
}
