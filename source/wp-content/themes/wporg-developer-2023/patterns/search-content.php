<?php
/**
 * Title: Search Content
 * Slug: wporg-developer-2023/search-content
 * Inserter: no
 */

?>

<!-- wp:group {"tagName":"main","style":{"spacing":{"blockGap":"0px","padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"className":"entry-content","layout":{"type":"constrained"}} -->
<main class="wp-block-group entry-content" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">

	<!-- wp:query {"queryId":0,"query":{"inherit":true,"perPage":10},"align":"wide"} -->
	<div class="wp-block-query alignwide">

	<!-- wp:group {"className":"align-left","layout":{"type":"constrained","contentSize":"","justifyContent":"left"}} -->
	<div class="wp-block-group align-left">

	<!-- wp:group {"className":"awesomeplete-form-wrap search-wrap"} -->
	<div class="wp-block-group awesomeplete-form-wrap search-wrap">
	<!-- wp:search {"label":"Search","showLabel":false,"buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true,"className":"is-style-secondary-search-control wporg-filtered-search-form"} /--></div>
	<!-- /wp:group -->

	<!-- wp:post-template -->
	
	<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10"}}}} -->
	<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--10);padding-bottom:var(--wp--preset--spacing--10)">	
	<!-- wp:wporg/search-title /-->
	<!-- wp:post-excerpt /-->
	<!-- wp:wporg/search-usage-info {"style":{"elements":{"link":{"color":{"text":"var:preset|color|charcoal-4"}}}},"textColor":"charcoal-4","fontSize":"small"} /--></div>
	<!-- /wp:group -->

	<!-- /wp:post-template --></div>
	<!-- /wp:group -->

	<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"center"}} -->
	<!-- wp:query-pagination-previous /-->

	<!-- wp:query-pagination-numbers /-->

	<!-- wp:query-pagination-next /-->
	<!-- /wp:query-pagination -->

	<!-- wp:query-no-results -->
	<!-- wp:heading {"textAlign":"center","level":1,"fontSize":"heading-2"} -->
	<h1 class="has-text-align-center has-heading-2-font-size"><?php esc_attr_e( 'No results found', 'wporg' ); ?></h1>
	<!-- /wp:heading -->
	<!-- /wp:query-no-results --></div>
	<!-- /wp:query --></main>
<!-- /wp:group -->
