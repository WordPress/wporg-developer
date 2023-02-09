<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Command_Installation;

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
		dirname( dirname( __DIR__ ) ) . '/build/command-installation',
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

	$non_bundled_commands = array(
		'https://github.com/wp-cli/admin-command',
		'https://github.com/wp-cli/dist-archive-command',
		'https://github.com/wp-cli/doctor-command',
		'https://github.com/wp-cli/find-command',
		'https://github.com/wp-cli/profile-command',
		'https://github.com/wp-cli/scaffold-package-command',
	);

	$repo_url = get_post_meta( get_the_ID(), 'repo_url', true );
	// Only non-bundled commands
	if ( ! in_array( $repo_url, $non_bundled_commands, true ) ) {
		return '';
	}
	$repo_slug = str_replace( 'https://github.com/', '', $repo_url );
	$command = get_the_title();
	$content = sprintf(
		'
		<p>Use the <code>%1$s</code> command by installing the command\'s package:</p>
		<pre><code>wp package install %2$s</code></pre>
		<p>Once the package is successfully installed, the <code>%3$s</code> command will appear in the list of available commands.</p>
		',
		$command,
		esc_html( $repo_slug ),
		$command
	);

	$title_block = sprintf(
		'<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">%s</h3><!-- /wp:heading -->',
		__( 'Installing', 'wporg' )
	);

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %1$s>%2$s %3$s</section>',
		$wrapper_attributes,
		do_blocks( $title_block ),
		$content
	);
}
