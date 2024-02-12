<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Command_Content;

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function init() {
	register_block_type(
		dirname( dirname( __DIR__ ) ) . '/build/command-content',
		array(
			'render_callback' => __NAMESPACE__ . '\render',
		)
	);
}

/**
 * Render the block content.
 *
 * @return string Returns the block markup.
 */
function render( $attributes, $content, $block ) {

	$post_ID = $block->context['postId'];

	if ( ! isset( $post_ID ) ) {
		return '';
	}

	$content = format_headings( get_the_content( null, false, $post_ID ) );

	$wrapper_attributes = get_block_wrapper_attributes();
	$repo_url = get_post_meta( $post_ID, 'repo_url', true );

	if ( is_bundled_commands( $repo_url ) ) {
		return sprintf(
			'<section %1$s>%2$s</section>',
			$wrapper_attributes,
			do_blocks( $content ),
		);
	}

	$repo_slug = str_replace( 'https://github.com/', '', $repo_url );
	$command = get_the_title( $post_ID );
	$installing_instructions = sprintf(
		'<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">%1$s</h3><!-- /wp:heading -->',
		__( 'Installing', 'wporg' )
	) . sprintf(
		/* translators: 1: command to be used, 2: package repo slug 3: command to be used */
		__(
			'
			<p>Use the <code>%1$s</code> command by installing the command\'s package:</p>
			<pre><code>wp package install %2$s</code></pre>
			<p>Once the package is successfully installed, the <code>%3$s</code> command will appear in the list of available commands.</p>
			',
			'wporg'
		),
		$command,
		esc_html( $repo_slug ),
		$command
	);

	// Insert before OPTIONS but after description if OPTIONS exists
	$options_match = '#<h3>Options<\/h3>|<h3>OPTIONS<\/h3>#';
	if ( preg_match( $options_match, $content ) ) {
		$content = preg_replace( $options_match, $installing_instructions . '$0', $content );
	} else {
		// Otherwise, appending to description is fine.
		$content .= $installing_instructions;
	}

	return sprintf(
		'<section %1$s>%2$s</section>',
		$wrapper_attributes,
		do_blocks( $content ),
	);
}

/**
 * Check if the repo url a bundled command
 */
function is_bundled_commands( $repo_url ) {
	$non_bundled_commands = array(
		'https://github.com/wp-cli/admin-command',
		'https://github.com/wp-cli/dist-archive-command',
		'https://github.com/wp-cli/doctor-command',
		'https://github.com/wp-cli/find-command',
		'https://github.com/wp-cli/profile-command',
		'https://github.com/wp-cli/scaffold-package-command',
	);

	if ( in_array( $repo_url, $non_bundled_commands, true ) ) {
		return false;
	}

	return true;
}

/**
 * Title case the headings of a post content.
 *
 * @param string $content
 * @return string Converted content.
 */
function format_headings( $content ) {
	$tag = 'h(?P<level>[1-6])';

	return preg_replace_callback(
		"/(?P<opening_tag><{$tag}>)(?P<title>.*?)(?P<closing_tag><\/{$tag}>)/J",
		function ( $matches ) {
			return $matches['opening_tag'] . ucwords( strtolower( $matches['title'] ) ) . $matches['closing_tag'];
		},
		$content
	);
}
