<?php
/**
 * Title: Article Meta
 * Slug: wporg-developer-2023/article-meta
 * Inserter: no
 */

?>

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20"},"margin":{"top":"20","bottom":"0"},"blockGap":"var:preset|spacing|10"},"border":{"top":{"color":"var:preset|color|light-grey-1","width":"1px"}}},"className":"entry-meta","layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group entry-meta" style="border-top-color:var(--wp--preset--color--light-grey-1);border-top-width:1px;margin-top:20px;margin-bottom:0;padding-top:var(--wp--preset--spacing--20)">

	<!-- wp:wporg/article-meta-date {"heading":"<?php esc_html_e( 'First published', 'wporg' ); ?>"} /-->

	<!-- wp:wporg/article-meta-date {"heading":"[last_updated]","displayType":"modified"} /-->

	<!-- wp:wporg/handbook-meta-link {"heading":"<?php esc_html_e( 'Edit article', 'wporg' ); ?>","linkURL":"[article_edit_link]","linkText":"<?php esc_html_e( 'Improve it on GitHub', 'wporg' ); ?>"} /-->
	
	<!-- wp:wporg/handbook-meta-link {"heading":"<?php esc_html_e( 'Changelog', 'wporg' ); ?>","linkURL":"[article_changelog_link]","linkText":"<?php esc_html_e( 'See list of changes', 'wporg' ); ?>"} /-->

</div>
<!-- /wp:group -->
