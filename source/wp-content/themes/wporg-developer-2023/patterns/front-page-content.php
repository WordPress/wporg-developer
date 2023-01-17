<?php
/**
 * Title: Front Page Content
 * Slug: wporg-developer-2023/front-page-content
 * Inserter: no
 */

?>

<!-- wp:group {"className":"has-white-color has-charcoal-2-background-color has-text-color has-background has-link-color","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-white-color has-charcoal-2-background-color has-text-color has-background has-link-color"><!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained","contentSize":"1344px"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:image {"align":"wide","id":7564,"sizeSlug":"full","linkDestination":"none","className":"wporg-hero-graphic"} -->
<figure class="wp-block-image alignwide size-full wporg-hero-graphic"><img src="https://s.w.org/wp-content/themes/wporg-developer-2023/images/developers.svg?v=20230116" alt="<?php esc_attr_e( 'WordPress Developer Resources', 'wporg' ); ?>" class="wp-image-7564"/></figure>
<!-- /wp:image --><!-- wp:heading {"level":1,"className":"screen-reader-text"} -->
<h1 class="wp-block-heading screen-reader-text"><?php esc_attr_e( 'Developer Resources', 'wporg' ); ?></h1>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"bottom":"var:preset|spacing|60","right":"var:preset|spacing|edge-space","left":"var:preset|spacing|edge-space"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-right:var(--wp--preset--spacing--edge-space);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--edge-space)"><!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:search {"label":"<?php esc_attr_e( 'Search', 'wporg' ); ?>","showLabel":false,"placeholder":"<?php esc_attr_e( 'Search in the code reference...', 'wporg' ); ?>","width":432,"widthUnit":"px","buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"backgroundColor":"blueberry-4","style":{"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group has-blueberry-4-background-color has-background" style="padding-top:var(--wp--preset--spacing--10);padding-bottom:var(--wp--preset--spacing--10)"><!-- wp:paragraph {"align":"center","fontSize":"extra-small"}  -->
<p class="has-text-align-center has-extra-small-font-size">
<?php echo wp_kses_post(
	sprintf(
	/* translators: %1$s: version link, %2$s: version number */
		__( 'See <a href="%1$s">what has changed</a> in the WordPress %2$s API.', 'wporg' ),
		'[wordpress_version_link]',
		'[wordpress_version]'
	)
); ?>
</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:post-content {"style":{"spacing":{"blockGap":"0px"}},"layout":{"inherit":true}} /-->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"right":"var:preset|spacing|edge-space","left":"var:preset|spacing|edge-space"}}},"backgroundColor":"blueberry-4","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-blueberry-4-background-color has-background" id="news" style="padding-right:var(--wp--preset--spacing--edge-space);padding-left:var(--wp--preset--spacing--edge-space)"><!-- wp:columns {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"},"blockGap":"0px"}},"className":"is-style-default"} -->
<div class="wp-block-columns alignwide is-style-default" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:column {"verticalAlignment":"top","width":"33%","className":"is-left-column","layout":{"inherit":false}} -->
<div class="wp-block-column is-vertically-aligned-top is-left-column" style="flex-basis:33%"><!-- wp:group {"style":{"spacing":{"padding":{"right":"80px"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="padding-right:80px"><!-- wp:heading {"fontSize":"heading-2"} -->
<h2 class="wp-block-heading has-heading-2-font-size"><?php esc_attr_e( 'Latest Posts', 'wporg' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"20px"} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"66%","layout":{"inherit":false}} -->
<div class="wp-block-column" style="flex-basis:66%"><!-- wp:group {"style":{"spacing":{"margin":{"top":"15px"}}}} -->
<div class="wp-block-group" style="margin-top:15px"><!-- wp:wporg/latest-news /-->

<!-- wp:spacer {"height":"20px"} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph -->
<p><a href="https://developer.wordpress.org/news/"><?php esc_attr_e( 'View all', 'wporg' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->


<!-- wp:group {"backgroundColor":"blueberry-1","style":{"spacing":{"padding":{"left":"var:preset|spacing|edge-space","right":"var:preset|spacing|edge-space"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group has-blueberry-1-background-color has-background"style="padding-left:var(--wp--preset--spacing--edge-space);padding-right:var(--wp--preset--spacing--edge-space)"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained","justifyContent":"center"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"55%"} -->
<div class="wp-block-column" style="flex-basis:55%"><!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"}}},"textColor":"white"} -->
<h3 class="wp-block-heading has-white-color has-text-color" style="margin-bottom:var(--wp--preset--spacing--50)"><?php esc_attr_e( 'Are you passionate about web development? Join the WordPress community and help shape the future of the web', 'wporg' ); ?>
</h3>
<!-- /wp:heading -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-fill-on-dark"} -->
<div class="wp-block-button is-style-fill-on-dark"><a href="https://wordpress.org/support/article/contributing-to-wordpress/" class="wp-block-button__link wp-element-button"><?php esc_attr_e( 'Contribute', 'wporg' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline-on-dark external-link"} -->
<div class="wp-block-button is-style-outline-on-dark external-link"><a class="wp-block-button__link wp-element-button" href="https://make.wordpress.org/core/handbook/"><?php esc_attr_e( 'Check Out Contributor Handbook ↗', 'wporg' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
