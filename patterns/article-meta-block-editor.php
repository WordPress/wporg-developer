<?php
/**
 * Title: Article Meta for the Block Editor GitHub handbook
 * Slug: wporg-developer-2023/article-meta-block-editor
 * Inserter: no
 */

?>

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|20"},"border":{"top":{"color":"var:preset|color|light-grey-1","width":"1px"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left","verticalAlignment":"top"},"className":"entry-meta"} -->
<div class="wp-block-group entry-meta" style="border-top-color:var(--wp--preset--color--light-grey-1);border-top-width:1px;margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40);padding-top:var(--wp--preset--spacing--40)">

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

	<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">

		<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}}} -->
		<p style="font-style:normal;font-weight:700"><?php esc_html_e( 'Edit article', 'wporg' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"className":"external-link"} -->
		<p class="external-link"><a href="[article_edit_link]">
			<?php echo wp_kses_post(
				sprintf(
					/* translators: %s: article title */
					__( 'Improve it on GitHub<span class="screen-reader-text">: %s</span>', 'wporg' ),
					'[article_title]'
				)
			); ?>
		</a></p>
		<!-- /wp:paragraph -->

	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
