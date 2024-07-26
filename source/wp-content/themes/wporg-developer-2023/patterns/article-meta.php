<?php
/**
 * Title: Article Meta
 * Slug: wporg-developer-2023/article-meta
 * Inserter: no
 */

?>

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20"},"margin":{"top":"20","bottom":"0"},"blockGap":"5px"},"border":{"top":{"color":"var:preset|color|light-grey-1","width":"1px"}}},"className":"entry-meta","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group entry-meta" style="border-top-color:var(--wp--preset--color--light-grey-1);border-top-width:1px;margin-top:20px;margin-bottom:0;padding-top:var(--wp--preset--spacing--20)">

	<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}}} -->
		<p style="font-style:normal;font-weight:700"><?php esc_html_e( 'First published', 'wporg' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:post-date /-->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}}} -->
		<p style="font-style:normal;font-weight:700">[last_updated]</p>
		<!-- /wp:paragraph -->

		<!-- wp:post-date {"displayType":"modified"} /-->
	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->
