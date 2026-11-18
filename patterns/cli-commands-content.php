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
<p>
	<?php echo wp_kses_post(
		sprintf(
		/* translators: %1$s: URL of the WP-CLI handbook, %2$s: URL of the WP-CLI blog */
			__( 'Looking to learn more about the internal API of WP-CLI or to contribute to its development? Check out the WP-CLI team&rsquo;s <a href="%1$s">handbook</a> and the <a href="%2$s">WP-CLI Blog</a>.', 'wporg' ),
			'https://make.wordpress.org/cli/handbook/',
			'https://make.wordpress.org/cli/'
		)
	); ?>
</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"is-toc-heading"} -->
<h2 class="wp-block-heading is-toc-heading" id="commands"><a href="#commands"><?php esc_html_e( 'Commands', 'wporg' ); ?></a></h2>
<!-- /wp:heading -->

<!-- wp:wporg/cli-command-table {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} /-->

<!-- wp:heading {"className":"is-toc-heading"} -->
<h2 class="wp-block-heading is-toc-heading" id="other-developer-resources"><a href="#other-developer-resources"><?php esc_html_e( 'Other Developer Resources', 'wporg' ); ?></a></h2>
<!-- /wp:heading -->
