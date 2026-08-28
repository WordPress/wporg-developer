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

	$post_ID = $block->context['postId'];

	if ( ! isset( $post_ID ) ) {
		return '';
	}

	$repo_url = get_post_meta( $post_ID, 'repo_url', true );
	$cmd_slug = str_replace( 'wp ', '', get_the_title( $post_ID ) );
	$open_issues = 'https://github.com/login?return_to=%2Fissues%3Fq%3Dlabel%3A' . urlencode( 'command:' . str_replace( ' ', '-', $cmd_slug ) ) . '+sort%3Aupdated-desc+org%3Awp-cli+is%3Aopen';
	$closed_issues = 'https://github.com/login?return_to=%2Fissues%3Fq%3Dlabel%3A' . urlencode( 'command:' . str_replace( ' ', '-', $cmd_slug ) ) . '+sort%3Aupdated-desc+org%3Awp-cli+is%3Aclosed';
	$issue_count = get_issue_count( $cmd_slug );

	$content .= '<div class="github-tracker">';

	if ( $repo_url ) {
		$content .= sprintf(
			'
			<div class="btn-group">
				<a class="button" href="%s" style="padding-top: 0;">GitHub Repository
					<img decoding="async" src="https://make.wordpress.org/cli/wp-content/plugins/wporg-cli/assets/images/github-mark.svg" class="icon-github" alt="GitHub">
				</a>
			</div>
			',
			esc_url( $repo_url )
		);
	}

	$content .= sprintf(
		'
		<div class="btn-group">
			<a href="%1$s" class="button wporg-command-github-open">
				%2$s <span class="green">%3$s</span>
			</a>
			<a href="%4$s" class="button wporg-command-github-closed">
				%5$s <span class="red">%6$s</span>
			</a>
		',
		esc_url( $open_issues ),
		__( 'View Open Issues', 'wporg' ),
		false !== $issue_count['open'] ? '(' . number_format_i18n( (int) $issue_count['open'] ) . ')' : '',
		esc_url( $closed_issues ),
		__( 'View Closed Issues', 'wporg' ),
		false !== $issue_count['open'] ? '(' . number_format_i18n( (int) $issue_count['closed'] ) . ')' : '',
	);

	if ( $repo_url ) {
		$content .= sprintf(
			'<a href="%1$s" class="button wporg-command-github-new">%2$s</a>',
			esc_url( rtrim( $repo_url, '/' ) . '/issues/new' ),
			__( 'Create New Issue', 'wporg' ),
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
