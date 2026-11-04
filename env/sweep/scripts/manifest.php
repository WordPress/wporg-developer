<?php
/**
 * Writes the sweep page manifest to env/sweep/results/manifest.json.
 *
 * Lists every published reference page whose imported DocBlock carries at
 * least one code snippet. The Playwright sweep visits exactly these pages.
 *
 * Run with: wp eval-file env/sweep/scripts/manifest.php
 */

$post_ids = get_posts(
	array(
		'post_type'      => array( 'wp-parser-function', 'wp-parser-class', 'wp-parser-hook', 'wp-parser-method' ),
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

$manifest = array();

foreach ( $post_ids as $post_id ) {
	$snippets = get_post_meta( $post_id, '_wp-parser_code_snippets', true );

	if ( ! is_array( $snippets ) || ! $snippets ) {
		continue;
	}

	$manifest[] = array(
		'id'       => $post_id,
		'type'     => get_post_type( $post_id ),
		'slug'     => get_post_field( 'post_name', $post_id ),
		'title'    => get_the_title( $post_id ),
		'url'      => get_permalink( $post_id ),
		'snippets' => count( $snippets ),
	);
}

usort(
	$manifest,
	function ( $a, $b ) {
		return strcmp( $a['type'] . $a['slug'], $b['type'] . $b['slug'] );
	}
);

$results_dir = ABSPATH . 'env/sweep/results';

if ( ! is_dir( $results_dir ) && ! mkdir( $results_dir, 0777, true ) ) {
	WP_CLI::error( 'Unable to create ' . $results_dir );
}

file_put_contents(
	$results_dir . '/manifest.json',
	wp_json_encode( $manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES )
);

WP_CLI::success( count( $manifest ) . ' pages with snippets listed in env/sweep/results/manifest.json' );
