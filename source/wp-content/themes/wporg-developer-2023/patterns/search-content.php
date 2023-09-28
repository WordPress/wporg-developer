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

		<!-- wp:group {"className":"awesomeplete-form-wrap search-wrap"} -->
		<div class="wp-block-group awesomeplete-form-wrap search-wrap">
		
			<!-- wp:search {"label":"<?php esc_attr_e( 'Search', 'wporg' ); ?>","showLabel":false,"placeholder":"<?php esc_attr_e( 'Search resources', 'wporg' ); ?>","width":260,"widthUnit":"px","buttonText":"<?php esc_attr_e( 'Search', 'wporg' ); ?>","buttonPosition":"button-inside","buttonUseIcon":true,"className":"is-style-secondary-search-control wporg-filtered-search-form"} /-->

		</div>
		<!-- /wp:group -->

		<!-- wp:wporg/search-results-context {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}}} /-->

		<!-- wp:post-template -->
			<?php
			$is_command_search = strpos( $_SERVER['REQUEST_URI'], 'cli/commands' );
			if ( $is_command_search ) { ?>
				<!-- wp:pattern {"slug":"wporg-developer-2023/search-content-post-command"} /-->
			<?php } else { ?>
				<!-- wp:pattern {"slug":"wporg-developer-2023/search-content-post-reference"} /-->
			<?php } ?>

		<!-- /wp:post-template -->

		<!-- wp:query-no-results -->
		
			<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results.","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
			<p style="margin-top:var(--wp--preset--spacing--40)"><?php esc_attr_e( 'Sorry, but nothing matched your search terms.', 'wporg' ); ?></p>
			<!-- /wp:paragraph -->

		<!-- /wp:query-no-results -->
	
	</div>
	<!-- /wp:group -->

	<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"center"}} -->
		<!-- wp:query-pagination-previous /-->

		<!-- wp:query-pagination-numbers /-->

		<!-- wp:query-pagination-next /-->
	<!-- /wp:query-pagination -->

</div>
<!-- /wp:query -->
