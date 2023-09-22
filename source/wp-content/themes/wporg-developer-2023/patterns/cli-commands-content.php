<?php
/**
 * Title: CLI Commands Page Static Content
 * Slug: wporg-developer-2023/cli-commands-content
 * Inserter: no
 */

?>

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Below is a listing of all currently available WP-CLI commands with links to documentation on usage and subcommands.', 'wporg' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php echo wp_kses_post(
	sprintf(
	/* translators: %1$s: URL of the WP-CLI handbook, %2$s: URL of the WP-CLI blog */
		__( 'Looking to learn more about the internal API of WP-CLI or to contribute to its development? Check out the WP-CLI team&rsquo;s <a href="%1$s">handbook</a> and the <a href="%2$s">WP-CLI Blog</a>.', 'wporg' ),
		'https://make.wordpress.org/cli/handbook/',
		'https://make.wordpress.org/cli/'
	)
); ?></p>
<!-- /wp:paragraph -->

<!-- wp:wporg/cli-command-table {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} /-->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Other Developer Resources', 'wporg' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"className":"is-style-cards-grid","layout":{"type":"grid","minimumColumnWidth":"49%"}} -->
<div class="wp-block-group is-style-cards-grid">
	
	<!-- wp:heading {"level":3,"fontSize":"small","fontFamily":"inter"} -->
	<h3 class="wp-block-heading has-inter-font-family has-small-font-size"><a href="https://make.wordpress.org/cli/"><strong><?php esc_html_e( 'CLI Blog', 'wporg' ); ?></strong><?php esc_html_e( 'Catch up on the latest on WP-CLI in the main updates blog.', 'wporg' ); ?></a></h3>
	<!-- /wp:heading -->

	<!-- wp:heading {"level":3,"fontSize":"small","fontFamily":"inter"} -->
	<h3 class="wp-block-heading has-inter-font-family has-small-font-size"><a href="https://make.wordpress.org/cli/handbook/"><strong><?php esc_html_e( 'CLI Handbook', 'wporg' ); ?></strong><?php esc_html_e( 'A collection of helpful guides and resources for using WP-CLI.', 'wporg' ); ?></a></h3>
	<!-- /wp:heading -->

</div>
<!-- /wp:group -->