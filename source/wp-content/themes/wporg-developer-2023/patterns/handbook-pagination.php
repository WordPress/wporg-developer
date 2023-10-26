<?php
/**
 * Title: Handbook Pagination
 * Slug: wporg-developer-2023/handbook-pagination
 * Inserter: no
 */

?>

<!-- wp:group {"classname":"has-three-columns__pagination","align":"full","style":{"border":{"top":{"color":"var:preset|color|light-grey-1","width":"1px","style":"solid"},"bottom":{"color":"var:preset|color|light-grey-1","width":"1px"}},"spacing":{"margin":{"top":"var:preset|spacing|20"},"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignfull has-three-columns__pagination" style="border-top-color:var(--wp--preset--color--light-grey-1);border-top-style:solid;border-top-width:1px;border-bottom-color:var(--wp--preset--color--light-grey-1);border-bottom-width:1px;margin-top:var(--wp--preset--spacing--20);padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)">
	<!-- wp:post-navigation-link {"type":"previous","label":"<?php esc_html_e( 'Previous ', 'wporg' ); ?>","showTitle":true,"linkLabel":true} /-->
	<!-- wp:post-navigation-link {"label":"<?php esc_html_e( 'Next ', 'wporg' ); ?>","showTitle":true,"linkLabel":true} /-->
</div>
<!-- /wp:group -->
