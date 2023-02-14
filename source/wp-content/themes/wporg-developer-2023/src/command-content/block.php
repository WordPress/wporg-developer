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

	$content = filter_the_content( get_the_content() );

	$wrapper_attributes = get_block_wrapper_attributes();
	$repo_url = get_post_meta( get_the_ID(), 'repo_url', true );

	if ( is_bundled_commands( $repo_url ) ) {
		return sprintf(
			'<section %1$s>%2$s</section>',
			$wrapper_attributes,
			do_blocks( $content ),
		);
	}

	$repo_slug = str_replace( 'https://github.com/', '', $repo_url );
	$command = get_the_title();
	$installing_instructions = sprintf(
		'
		<!-- wp:heading {"fontSize":"heading-3"} --><h3 class="wp-block-heading has-heading-3-font-size">%1$s</h3><!-- /wp:heading -->
		<p>Use the <code>%2$s</code> command by installing the command\'s package:</p>
		<pre><code>wp package install %3$s</code></pre>
		<p>Once the package is successfully installed, the <code>%4$s</code> command will appear in the list of available commands.</p>
		',
		__( 'Installing', 'wporg' ),
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
 * Filter the content of command pages
 */
function filter_the_content( $content ) {
	if ( 'command' !== get_post_type() || ! is_singular() ) {
		return $content;
	}

	// Transform emdash back to triple-dashes
	$content = str_replace( '&#045;&#8211;', '&#045;&#045;&#045;', $content );

	// Transform HTML entity artifacts back to their original
	$content = str_replace( '&amp;#039;', '\'', $content );

	return $content;
}
