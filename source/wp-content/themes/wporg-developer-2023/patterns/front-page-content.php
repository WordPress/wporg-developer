<?php
/**
 * Title: Front Page Content
 * Slug: wporg-developer-2023/front-page-content
 * Inserter: no
 */

?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"16px","right":"var:preset|spacing|edge-space","bottom":"16px","left":"var:preset|spacing|edge-space"}},"elements":{"link":{"color":{"text":"var:preset|color|white"}}}},"backgroundColor":"charcoal-2","textColor":"white","className":"is-sticky wporg-front-page-breadcrumb","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull is-sticky wporg-front-page-breadcrumb has-white-color has-charcoal-2-background-color has-text-color has-background has-link-color" style="padding-top:16px;padding-right:var(--wp--preset--spacing--edge-space);padding-bottom:16px;padding-left:var(--wp--preset--spacing--edge-space)"><!-- wp:group {"align":"full","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignfull"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:heading {"className":"is-style-default","fontSize":"small","fontFamily":"inter"} -->
<h2 class="wp-block-heading is-style-default has-inter-font-family has-small-font-size"><?php esc_attr_e( 'Developers', 'wporg' ); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:navigation {"textColor":"white","backgroundColor":"charcoal-2","className":"is-style-dropdown-on-mobile","style":{"spacing":{"blockGap":"24px"}},"fontSize":"small"} -->
<!-- wp:navigation-link {"label":"<?php esc_attr_e( 'Blog', 'wporg' ); ?>","url":"https://developer.wordpress.org/news/","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"<?php esc_attr_e( 'Forums', 'wporg' ); ?>","url":"https://wordpress.org/support/forums/","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"<?php esc_attr_e( 'Documentation', 'wporg' ); ?>","type":"custom","url":"https://wordpress.org/support/","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"<?php esc_attr_e( 'Get Involved', 'wporg' ); ?>","type":"custom","url":"https://make.wordpress.org/support/handbook/","kind":"custom","isTopLevelLink":true} /-->
<!-- /wp:navigation --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

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

<!-- wp:group {"align":"full","style":{"border":{"bottom":{"color":"var:preset|color|light-grey-1","style":"solid","width":"1px"}},"spacing":{"padding":{"right":"var:preset|spacing|edge-space","left":"var:preset|spacing|edge-space"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="border-bottom-color:var(--wp--preset--color--light-grey-1);border-bottom-style:solid;border-bottom-width:1px;padding-right:var(--wp--preset--spacing--edge-space);padding-left:var(--wp--preset--spacing--edge-space)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"0","left":"0"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","right":"var:preset|spacing|60"}},"border":{"right":{"width":"1px","style":"solid","color":"var:preset|color|light-grey-1"}}}} -->
<div class="wp-block-column" style="border-right-color:var(--wp--preset--color--light-grey-1);border-right-style:solid;border-right-width:1px;padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"style":{"elements":{"link":{"color":{"text":"var:preset|color|charcoal-1"}}}},"className":"wp-block-heading is-style-default"} -->
<h2 class="wp-block-heading is-style-default has-link-color"><?php esc_attr_e( 'Documentation', 'wporg' ); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:paragraph -->
<p><a href="https://developer.wordpress.org/block-editor" data-type="helphub_article"><?php esc_attr_e( 'Block Editor', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="https://developer.wordpress.org/themes" data-type="helphub_article"><?php esc_attr_e( 'Themes', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="https://developer.wordpress.org/plugins"><?php esc_attr_e( 'Plugins', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="https://developer.wordpress.org/apis" data-type="URL" data-id="https://developers.wordpress.org/apis"><?php esc_attr_e( 'Common APIs', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="https://developer.wordpress.org/advanced-administration"><?php esc_attr_e( 'Advanced Administration', 'wporg' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-column" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--60)"><!-- wp:heading {"style":{"elements":{"link":{"color":{"text":"var:preset|color|charcoal-1"}}}},"className":"wp-block-heading"} -->
<h2 class="wp-block-heading has-link-color"><?php esc_attr_e( 'API Reference', 'wporg' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><a href="https://developer.wordpress.org/rest-api/" data-type="helphub_article"><?php esc_attr_e( 'REST API', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="https://developer.wordpress.org/apis/making-http-requests/" data-type="helphub_article"><?php esc_attr_e( 'HTTP API', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="https://developer.wordpress.org/cli/commands" data-type="URL" data-id="https://developers.wordpress.org/cli/commands"><?php esc_attr_e( 'WP-CLI', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="https://developer.wordpress.org/coding-standards/" data-type="URL" data-id="https://developers.wordpress.org/coding-standards/"><?php esc_attr_e( 'Coding Standards', 'wporg' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"right":"var:preset|spacing|edge-space","left":"var:preset|spacing|edge-space"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" id="news" style="padding-right:var(--wp--preset--spacing--edge-space);padding-left:var(--wp--preset--spacing--edge-space)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":"0px","padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"className":"is-style-default"} -->
<div class="wp-block-columns alignwide is-style-default" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:column {"verticalAlignment":"top","width":"33%","className":"is-left-column","layout":{"inherit":false}} -->
<div class="wp-block-column is-vertically-aligned-top is-left-column" style="flex-basis:33%"><!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"fontSize":"heading-2"} -->
<h2 class="wp-block-heading has-heading-2-font-size"><?php esc_attr_e( 'More Resources', 'wporg' ); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"66%","layout":{"inherit":false}} -->
<div class="wp-block-column" style="flex-basis:66%"><!-- wp:group {"style":{"spacing":{"margin":{"top":"15px"}}}} -->
<div class="wp-block-group" style="margin-top:15px"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"left"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"is-style-short-text"} -->
<p class="is-style-short-text"><a href="https://learn.wordpress.org"><?php esc_attr_e( 'Learn Wordpress', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php esc_attr_e( 'Access to hundreds of tutorials and lessons', 'wporg' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"left"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"is-style-short-text"} -->
<p class="is-style-short-text"><a href="https://wordpress.org/support/"><?php esc_attr_e( 'Documentation', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"is-style-short-text"} -->
<p class="is-style-short-text"><?php esc_attr_e( 'End-user documentation to help you get the most out of WordPress', 'wporg' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"left"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"is-style-short-text"} -->
<p class="is-style-short-text"><a href="https://wordpress.tv/"><?php esc_attr_e( 'WordPress.tv', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php esc_attr_e( 'Watch videos curated by the community', 'wporg' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"left"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"is-style-short-text"} -->
<p class="is-style-short-text"><a href="https://wordpress.org/support/forums/"><?php esc_attr_e( 'Support Forums', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php esc_attr_e( 'Learn, share, and troubleshoot with the community', 'wporg' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

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
<div class="wp-block-button is-style-fill-on-dark"><a href="https://make.wordpress.org" class="wp-block-button__link wp-element-button"><?php esc_attr_e( 'Contribute', 'wporg' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline-on-dark external-link"} -->
<div class="wp-block-button is-style-outline-on-dark external-link"><a class="wp-block-button__link wp-element-button" href="https://make.wordpress.org/core/handbook/"><?php esc_attr_e( 'Check Out Contributor Handbook ↗', 'wporg' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
