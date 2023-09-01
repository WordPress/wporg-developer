<?php
/**
 * Title: Header
 * Slug: wporg-developer-2023/header
 * Inserter: no
 */
?>

<!-- wp:group {"align":"full","style":{"spacing":{"blockGap":"0"}},"backgroundColor":"charcoal-2","className":"is-sticky","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull is-sticky has-charcoal-2-background-color has-background">

	<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"18px","bottom":"18px","left":"var:preset|spacing|edge-space","right":"var:preset|spacing|edge-space"}},"elements":{"link":{"color":{"text":"var:preset|color|white"}}},"border":{"top":{"color":"var:preset|color|opacities-white-10","width":"1px"},"right":{},"bottom":{},"left":{}}},"backgroundColor":"charcoal-2","textColor":"white","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignfull has-white-color has-charcoal-2-background-color has-text-color has-background has-link-color" style="border-top-color:var(--wp--preset--color--opacities-white-10);border-top-width:1px;padding-top:18px;padding-right:var(--wp--preset--spacing--edge-space);padding-bottom:18px;padding-left:var(--wp--preset--spacing--edge-space)">

		<!-- wp:paragraph {"fontSize":"small"} -->
		<p class="has-small-font-size"><?php esc_html_e( 'Developer Resources', 'wporg' ); ?></p>
		<!-- /wp:paragraph -->
		
		<!-- wp:navigation {"ref":144496,"textColor":"white","backgroundColor":"charcoal-2","className":"is-style-dropdown-on-mobile","style":{"spacing":{"blockGap":"24px"}},"fontSize":"small"} /-->

	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"18px","bottom":"18px","left":"var:preset|spacing|edge-space","right":"var:preset|spacing|edge-space"}}},"backgroundColor":"white","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignfull has-white-background-color has-background" style="padding-top:18px;padding-right:var(--wp--preset--spacing--edge-space);padding-bottom:18px;padding-left:var(--wp--preset--spacing--edge-space)">
		
		<!-- wp:wporg/site-breadcrumbs {"style":{"elements":{"link":{"color":{"text":"var:preset|color|charcoal-4"}}}},"textColor":"charcoal-1","fontSize":"small"} /-->

	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->
