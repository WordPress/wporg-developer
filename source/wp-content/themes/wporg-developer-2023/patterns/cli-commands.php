<?php
/**
 * Title: CLI Commands Page Content
 * Slug: wporg-developer-2023/cli-commands
 * Inserter: no
 */

?>

<!-- wp:group {"tagName":"main","style":{"spacing":{"padding":{"left":"var:preset|spacing|edge-space","right":"var:preset|spacing|edge-space","bottom":"var:preset|spacing|60"}}},"className":"alignfull","layout":{"type":"constrained","wideSize":"1280px","contentSize":"680px"}} -->
<main class="wp-block-group alignfull" style="padding-right:var(--wp--preset--spacing--edge-space);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--edge-space)"><!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}},"layout":{"type":"constrained","justifyContent":"left"}} -->
<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--20)"><!-- wp:search {"label":"<?php esc_attr_e( 'Search', 'wporg' ); ?>","showLabel":false,"placeholder":"<?php esc_attr_e( 'Search resources', 'wporg' ); ?>","width":240,"widthUnit":"px","buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true} /--></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"layout":{"type":"constrained","justifyContent":"left"}} -->
<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--20)"><!-- wp:group {"tagName":"article","style":{"spacing":{"blockGap":"var:preset|spacing|40","margin":{"top":"0px"}}}} -->
<article class="wp-block-group" style="margin-top:0px"><!-- wp:wporg/sidebar-container -->
<!-- wp:group -->
<div class="wp-block-group"><!-- wp:wporg/table-of-contents /--></div>
<!-- /wp:group -->
<!-- /wp:wporg/sidebar-container -->

<!-- wp:group {"align":"wide","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide"><!-- wp:heading {"level":1,"fontSize":"heading-2"} -->
<h1 class="wp-block-heading has-heading-2-font-size"><?php esc_html_e( 'WP-CLI Commands', 'wporg' ); ?></h1>
<!-- /wp:heading -->

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
<div class="wp-block-group is-style-cards-grid"><!-- wp:heading {"level":3,"fontSize":"small","fontFamily":"inter"} -->
<h3 class="wp-block-heading has-inter-font-family has-small-font-size"><a href="https://make.wordpress.org/cli/"><strong><?php esc_html_e( 'CLI Blog', 'wporg' ); ?></strong><?php esc_html_e( 'Catch up on the latest on WP-CLI in the main updates blog.', 'wporg' ); ?></a></h3>
<!-- /wp:heading -->

<!-- wp:heading {"level":3,"fontSize":"small","fontFamily":"inter"} -->
<h3 class="wp-block-heading has-inter-font-family has-small-font-size"><a href="https://make.wordpress.org/cli/handbook/"><strong><?php esc_html_e( 'CLI Handbook', 'wporg' ); ?></strong><?php esc_html_e( 'A collection of helpful guides and resources for using WP-CLI.', 'wporg' ); ?></a></h3>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></article>
<!-- /wp:group --></div>
<!-- /wp:group --></main>
<!-- /wp:group -->
