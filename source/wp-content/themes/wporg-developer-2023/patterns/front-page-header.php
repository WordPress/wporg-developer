<?php
/**
 * Title: Front Page Header
 * Slug: wporg-developer-2023/front-page-header
 * Inserter: no
 */

?>

<!-- wp:wporg/local-navigation-bar {"backgroundColor":"charcoal-2","style":{"elements":{"link":{"color":{"text":"var:preset|color|white"},":hover":{"color":{"text":"var:preset|color|white"}}}}},"textColor":"white","fontSize":"small"} -->

	<!-- wp:html -->
	<div style="height:calc(var(--wp--custom--body--small--typography--line-height) * var(--wp--preset--font-size--small));" aria-hidden="true"></div>
	<!-- /wp:html -->

	<!-- wp:navigation {"icon":"menu","overlayBackgroundColor":"charcoal-2","overlayTextColor":"white","layout":{"type":"flex","orientation":"horizontal"},"fontSize":"small","menuSlug":"developer"} /-->

<!-- /wp:wporg/local-navigation-bar -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"right":"var:preset|spacing|edge-space","left":"var:preset|spacing|edge-space"}}},"backgroundColor":"charcoal-2","className":"has-white-color has-charcoal-2-background-color has-text-color has-background has-link-color","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-white-color has-charcoal-2-background-color has-text-color has-background has-link-color" style="padding-right:var(--wp--preset--spacing--edge-space);padding-left:var(--wp--preset--spacing--edge-space)">

	<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"bottom"}} -->
	<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">
	
		<!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"50px","fontStyle":"normal","fontWeight":"400"}},"fontFamily":"ibm-plex-sans"} -->
		<h1 class="wp-block-heading has-ibm-plex-sans-font-family" style="font-size:50px;font-style:normal;font-weight:400"><?php esc_html_e( 'Developer Resources', 'wporg' ); ?></h1>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"style":{"typography":{"lineHeight":"2.3"}},"textColor":"white"} -->
		<p class="has-white-color has-text-color" style="line-height:2.3"><?php esc_html_e( '/The freedom to build', 'wporg' ); ?></p>
		<!-- /wp:paragraph -->
	
	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10"}}},"backgroundColor":"lemon-3","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-lemon-3-background-color has-background" style="padding-top:var(--wp--preset--spacing--10);padding-bottom:var(--wp--preset--spacing--10)">

	<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
	<p class="has-text-align-center has-small-font-size"><?php echo wp_kses_post(
		sprintf(
		/* translators: %1$s: version link, %2$s: version number */
			__( 'See <a href="%1$s">what has changed</a> in the WordPress %2$s API.', 'wporg' ),
			'[wordpress_version_link]',
			'[wordpress_version]'
		)
	); ?></p>
	<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->
