<?php
/**
 * Title: Single Handbook Template
 * Slug: wporg-developer-2023/single-handbook
 * Inserter: no
 */

?>

<!-- wp:group {"tagName":"main","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"left":"var:preset|spacing|edge-space","right":"var:preset|spacing|edge-space"}}}} -->
<main class="wp-block-group alignfull" style="padding-left:var(--wp--preset--spacing--edge-space);padding-right:var(--wp--preset--spacing--edge-space)">

	<!-- wp:group {"layout":{"type":"constrained","justifyContent":"left"},"align":"wide"} -->
	<div class="wp-block-group alignwide">

		<!-- wp:group {"tagName":"article"} -->
		<article class="wp-block-group">
			<!-- wp:post-title {"level":1,"style":{"spacing":{"margin":{"top":"var:preset|spacing|edge-space","bottom":"var:preset|spacing|40"}}}} /-->

			<!-- wp:group {"tagName":"aside","className":"sidebar-container"} -->
			<aside class="wp-block-group sidebar-container">
				<!-- wp:search {"label":"<?php esc_attr_e( 'Search', 'wporg' ); ?>","showLabel":false,"widthUnit":"px","buttonText":"<?php esc_attr_e( 'Search', 'wporg' ); ?>","buttonPosition":"button-inside","buttonUseIcon":true,"className":"is-style-secondary-search-control","borderColor":"light-grey-1","placeholder":"<?php esc_attr_e( 'Search in the documentation', 'wporg' ); ?>"} /-->

				<!-- wp:wporg/table-of-contents {"style":{"elements":{"link":{"color":{"text":"var:preset|color|blueberry-1"}}},"spacing":{"padding":{"top":"var:preset|spacing|20","right":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20"}}},"backgroundColor":"blueberry-4","textColor":"blueberry-1"} /-->

				<!-- wp:paragraph {"fontSize":"small","className":"is-link-to-top"} -->
				<p class="has-small-font-size is-link-to-top"><a href="#wp--skip-link--target"><?php esc_html_e( '↑ Back to top', 'wporg' ); ?></a></p>
				<!-- /wp:paragraph -->
			</aside>
			<!-- /wp:group -->

			<!-- wp:post-content {"layout":{"inherit":true}} /-->
		</article>
		<!-- /wp:group -->

		<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|20"},"border":{"top":{"color":"var:preset|color|light-grey-1","width":"1px"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left"}} -->
		<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--light-grey-1);border-top-width:1px;margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40);padding-top:var(--wp--preset--spacing--40)">
			<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group">
				<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}}} -->
				<p style="font-style:normal;font-weight:700"><?php esc_html_e( 'First published', 'wporg' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:post-date {"fontSize":"normal"} /-->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group">
				<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}}} -->
				<p style="font-style:normal;font-weight:700"><?php esc_html_e( 'Last updated', 'wporg' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:post-date {"displayType":"modified","fontSize":"normal"} /-->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group">
				<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}}} -->
				<p style="font-style:normal;font-weight:700"><?php esc_html_e( 'Edit article', 'wporg' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"external-link"} -->
				<p class="external-link"><a href="#"><?php esc_html_e( 'Improve it on GitHub', 'wporg' ); ?></a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group">
				<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}}} -->
				<p style="font-style:normal;font-weight:700"><?php esc_html_e( 'Changelog', 'wporg' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"external-link"} -->
				<p class="external-link"><a href="#"><?php esc_html_e( 'See list of changes', 'wporg' ); ?></a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:spacer {"height":"40px"} -->
	<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->
</main>
<!-- /wp:group -->
