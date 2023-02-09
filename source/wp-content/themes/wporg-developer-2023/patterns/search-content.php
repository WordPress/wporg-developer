<?php
/**
 * Title: Search Content
 * Slug: wporg-developer-2023/search-content
 * Inserter: no
 */

?>

<!-- wp:query {"queryId":0,"query":{"inherit":true,"perPage":10},"align":"wide"} -->
<div class="wp-block-query alignwide">

<!-- wp:group {"className":"align-left","layout":{"type":"constrained","contentSize":"","justifyContent":"left"}} -->
<div class="wp-block-group align-left">

<!-- wp:group {"className":"awesomeplete-form-wrap search-wrap"} -->
<div class="wp-block-group awesomeplete-form-wrap search-wrap">
<!-- wp:search {"label":"Search","showLabel":false,"buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true,"className":"is-style-secondary-search-control wporg-filtered-search-form"} /--></div>
<!-- /wp:group -->

<!-- wp:wporg/search-results-context {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20"}}}} /-->

<!-- wp:post-template -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10"}}}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--10);padding-bottom:var(--wp--preset--spacing--10)">	
<!-- wp:wporg/code-short-title /-->
<!-- wp:post-excerpt /-->
<!-- wp:wporg/code-type-usage-info {"style":{"elements":{"link":{"color":{"text":"var:preset|color|charcoal-4"}}}},"textColor":"charcoal-4","fontSize":"small"} /--></div>
<!-- /wp:group -->

<!-- /wp:post-template -->
	<!-- wp:query-no-results -->
<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results.","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--40)"><?php esc_attr_e( 'Sorry, but nothing matched your search terms.', 'wporg' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:group -->

<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination --></div>
<!-- /wp:query -->
