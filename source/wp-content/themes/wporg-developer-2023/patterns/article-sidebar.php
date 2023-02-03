<?php
/**
 * Title: Article Sidebar
 * Slug: wporg-developer-2023/article-sidebar
 * Inserter: no
 */

?>

<!-- wp:group {"tagName":"aside","className":"sidebar-container"} -->
<aside class="wp-block-group sidebar-container">
	<!-- wp:search {"label":"<?php esc_attr_e( 'Search', 'wporg' ); ?>","showLabel":false,"widthUnit":"px","buttonText":"<?php esc_attr_e( 'Search', 'wporg' ); ?>","buttonPosition":"button-inside","buttonUseIcon":true,"className":"is-style-secondary-search-control","borderColor":"light-grey-1","placeholder":"<?php esc_attr_e( 'Search in the documentation', 'wporg' ); ?>"} /-->

	<!-- wp:wporg/table-of-contents {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","right":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20"}}}} /-->

	<!-- wp:paragraph {"fontSize":"small","className":"is-link-to-top"} -->
	<p class="has-small-font-size is-link-to-top"><a href="#wp--skip-link--target"><?php esc_html_e( '↑ Back to top', 'wporg' ); ?></a></p>
	<!-- /wp:paragraph -->
</aside>
<!-- /wp:group -->
