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

<!-- wp:heading {"className":"is-toc-heading"} -->
<h2 class="wp-block-heading is-toc-heading" id="other-developer-resources"><a href="#other-developer-resources"><?php esc_html_e( 'Other Developer Resources', 'wporg' ); ?></a></h2>
<!-- /wp:heading -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"className":"is-style-cards-grid","layout":{"type":"grid","minimumColumnWidth":"49%"}} -->
<div class="wp-block-group is-style-cards-grid"><!-- wp:wporg/link-wrapper -->
<a class="wp-block-wporg-link-wrapper" href="https://make.wordpress.org/cli/"><!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"small","fontFamily":"inter"} -->
<h3 class="wp-block-heading has-inter-font-family has-small-font-size" id="cli-blog" style="font-style:normal;font-weight:700"><?php esc_html_e( 'CLI Blog', 'wporg' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php esc_html_e( 'Catch up on the latest on WP-CLI in the main updates blog.', 'wporg' ); ?></p>
<!-- /wp:paragraph --></a>
<!-- /wp:wporg/link-wrapper -->

<!-- wp:wporg/link-wrapper -->
<a class="wp-block-wporg-link-wrapper" href="https://make.wordpress.org/cli/"><!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"small","fontFamily":"inter"} -->
<h3 class="wp-block-heading has-inter-font-family has-small-font-size" id="cli-handbook" style="font-style:normal;font-weight:700"><?php esc_html_e( 'CLI Handbook', 'wporg' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php esc_html_e( 'A collection of helpful guides and resources for using WP-CLI.', 'wporg' ); ?></p>
<!-- /wp:paragraph --></a>
<!-- /wp:wporg/link-wrapper --></div>
<!-- /wp:group -->