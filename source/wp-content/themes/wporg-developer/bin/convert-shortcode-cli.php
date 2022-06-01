<?php
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
namespace WordPressdotorg\Developer_Network;

// This script should only be called in a CLI environment.
if ( 'cli' != php_sapi_name() ) {
	die();
}

$opts = getopt( '', array( 'post:', 'url:', 'abspath:' ) );

if ( empty( $opts['url'] ) ) {
	// $opts['url'] = 'https://developer.wordpress.org/';
	$opts['url'] = 'http://localhost:8888';
}
if ( empty( $opts['abspath'] ) && false !== strpos( __DIR__, 'wp-content' ) ) {
	$opts['abspath'] = substr( __DIR__, 0, strpos( __DIR__, 'wp-content' ) );
}

// Bootstrap WordPress.
$_SERVER['HTTP_HOST']   = parse_url( $opts['url'], PHP_URL_HOST );
$_SERVER['REQUEST_URI'] = parse_url( $opts['url'], PHP_URL_PATH );
$_SERVER['REQUEST_METHOD'] = 'GET';

require rtrim( $opts['abspath'], '/' ) . '/wp-load.php';

$explanations = get_posts(
	array(
		'post_type' => 'wporg_explanations',
		'post_status' => 'publish',
		'posts_per_page' => -1,
	)
);

$shortcode_regex = '/\[php\](.*)\[\/php\]/sU';
$block_syntax =
	"<!-- wp:code {\"lineNumbers\":true} -->\n" .
	'<pre class="wp-block-code"><code lang="php" class="language-php line-numbers">%s</code></pre>' .
	"\n<!-- /wp:code -->";

$count = 0;
$replaced = 0;
foreach ( $explanations as $explanation ) {
	$has_shortcode = has_shortcode( $explanation->post_content, 'php' );
	$content = $explanation->post_content;

	if ( $has_shortcode ) {
		$count++;
		preg_match_all( $shortcode_regex, $content, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match ) {
			$code = trim( $match[1] );
			$replacement = sprintf( $block_syntax, $code );
			$content = str_replace( $match[0], $replacement, $content );
		}

		$still_has_shortcode = str_contains( $content, '[php]' ) || str_contains( $content, '[/php]' );

		if ( $still_has_shortcode ) {
			echo "Error in {$explanation->ID}.\n";
		} else {
			wp_update_post(
				array(
					'ID'           => $explanation->ID,
					'post_content' => $content,
				)
			);
			$replaced++;
		}
	}
}

echo "\nShortcodes replaced in {$replaced} of {$count} explanations.\n";
