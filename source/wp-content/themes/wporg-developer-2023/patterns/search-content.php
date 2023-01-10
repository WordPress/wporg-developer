<?php
/**
 * Title: Search Content
 * Slug: wporg-developer-2023/search-content
 * Inserter: no
 */

?>

<!-- wp:group {"tagName":"main","style":{"spacing":{"blockGap":"0px"}},"className":"entry-content","layout":{"type":"constrained"}} -->
<main class="wp-block-group entry-content"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"bottom":"var:preset|spacing|70"}}},"layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide" style="padding-bottom:var(--wp--preset--spacing--70)">

	<!-- wp:query {"queryId":0,"query":{"inherit":true,"perPage":10}} -->
	<div class="wp-block-query"><!-- wp:post-template -->

	<!-- wp:post-title {"isLink":true} /-->

	<!-- wp:post-excerpt /-->

	<!-- /wp:post-template -->

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
	<!-- /wp:query --></div>
	<!-- /wp:group --></main>
<!-- /wp:group -->
