<?php
namespace WordPressdotorg\Theme\Developer_2023\Dynamic_Command_Github;

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
		dirname( dirname( __DIR__ ) ) . '/build/command-github',
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
	$repo_url = get_post_meta( get_the_ID(), 'repo_url', true );
	$cmd_slug = str_replace( 'wp ', '', get_the_title() );
	$open_issues = 'https://github.com/login?return_to=%2Fissues%3Fq%3Dlabel%3A' . urlencode( 'command:' . str_replace( ' ', '-', $cmd_slug ) ) . '+sort%3Aupdated-desc+org%3Awp-cli+is%3Aopen';
	$closed_issues = 'https://github.com/login?return_to=%2Fissues%3Fq%3Dlabel%3A' . urlencode( 'command:' . str_replace( ' ', '-', $cmd_slug ) ) . '+sort%3Aupdated-desc+org%3Awp-cli+is%3Aclosed';
	$issue_count = get_issue_count( $cmd_slug );

	if ( $repo_url ) {
		$content .= sprintf(
			'
			<div class="github-tracker">
				<a href="%s">
					<img src="https://make.wordpress.org/cli/wp-content/plugins/wporg-cli/assets/images/github-mark.svg" class="icon-github" />
				</a>
			',
			esc_url( $repo_url )
		);
	}

	$content .= sprintf(
		'
		<div class="btn-group">
			<a href="%s" class="button">View Open Issues
		',
		esc_url( $open_issues )
	);

	if ( false !== $issue_count['open'] ) {
		$content .= sprintf(
			'
			<span class="green">(%s)</span>
			',
			(int) $issue_count['open']
		);
	}

	$content .= '</a>';

	$content .= sprintf(
		'
		<a href="%s"class="button">View Closed Issues
		',
		esc_url( $closed_issues )
	);

	if ( false !== $issue_count['closed'] ) {
		$content .= sprintf(
			'
			<span class="red">(%s)</span>
			',
			(int) $issue_count['closed']
		);
	}

	$content .= '</a>';

	if ( $repo_url ) {
		$content .= sprintf(
			'
			<a href="%s" class="button">Create New Issue</a>
			',
			esc_url( rtrim( $repo_url, '/' ) . '/issues/new' )
		);
	}

	$content .= '</div>';
	$content .= '</div>';

	$wrapper_attributes = get_block_wrapper_attributes();
	return sprintf(
		'<section %1$s>%2$s</section>',
		$wrapper_attributes,
		$content,
	);
}


/**
 * Get issue count
 */
function get_issue_count( $cmd_slug ) {
	$issue_count = array(
		'open'   => false,
		'closed' => false,
	);

	foreach ( $issue_count as $key => $value ) {
		$cache_key = 'wpcli_issue_count_' . md5( $cmd_slug . $key );
		$value = get_transient( $cache_key );
		if ( false === $value ) {
			$request_url = 'https://api.github.com/search/issues?q=' . rawurlencode( 'label:command:' . str_replace( ' ', '-', $cmd_slug ) . ' org:wp-cli state:' . $key );
			$response = wp_remote_get( $request_url );
			$ttl = 2 * MINUTE_IN_SECONDS;
			if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
				$data = json_decode( wp_remote_retrieve_body( $response ), true );
				if ( isset( $data['total_count'] ) ) {
					$value = $data['total_count'];
					$ttl = 2 * HOUR_IN_SECONDS;
				}
			}
			set_transient( $cache_key, $value, $ttl );
		}
		$issue_count[ $key ] = $value;
	}

	return $issue_count;
}
