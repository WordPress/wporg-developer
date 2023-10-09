<?php
/**
 * Title: Search Content
 * Slug: wporg-developer-2023/search-content
 * Inserter: no
 */

?>

<!-- wp:query {"queryId":0,"query":{"inherit":true,"perPage":25},"align":"wide"} -->
<div class="wp-block-query alignwide">

	<!-- wp:group {"className":"align-left","layout":{"type":"constrained","contentSize":"","justifyContent":"left"},"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}}} -->
	<div class="wp-block-group align-left" style="margin-bottom:var(--wp--preset--spacing--40)">

		<!-- wp:group {"align":"wide","className":"wporg-search-controls","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"top"}} -->
		<div class="wp-block-group alignwide wporg-search-controls">
	
			<!-- wp:search {"label":"<?php esc_attr_e( 'Search', 'wporg' ); ?>","showLabel":false,"placeholder":"<?php esc_attr_e( 'Search resources', 'wporg' ); ?>","width":260,"widthUnit":"px","buttonText":"<?php esc_attr_e( 'Search', 'wporg' ); ?>","buttonPosition":"button-inside","buttonUseIcon":true,"className":"is-style-secondary-search-control wporg-filtered-search-form"} /-->

			<!-- wp:wporg/resource-select {"label":"<?php esc_attr_e( 'Select resource to search', 'wporg' ); ?>","hideLabelFromVision":true} /-->
		
		</div>
		<!-- /wp:group -->

		<!-- wp:wporg/search-results-context {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}},"elements":{"link":{"color":{"text":"var:preset|color|charcoal-4"}}}},"textColor":"charcoal-4","fontSize":"small"} /-->

		<!-- wp:post-template {"align":"wide"} -->

			<!-- wp:wporg/search-post /-->

		<!-- /wp:post-template -->

		<!-- wp:query-no-results -->
		
			<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results.","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
			<p style="margin-top:var(--wp--preset--spacing--40)"><?php esc_attr_e( 'Sorry, but nothing matched your search terms.', 'wporg' ); ?></p>
			<!-- /wp:paragraph -->

		<!-- /wp:query-no-results -->
	
	</div>
	<!-- /wp:group -->

	<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"center"}} -->
		<!-- wp:query-pagination-previous {"label":"Previous"} /-->

		<!-- wp:query-pagination-numbers /-->

		<!-- wp:query-pagination-next {"label":"Next"} /-->
	<!-- /wp:query-pagination -->

</div>
<!-- /wp:query -->
