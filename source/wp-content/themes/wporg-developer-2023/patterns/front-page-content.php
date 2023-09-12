<?php
/**
 * Title: Front Page Content
 * Slug: wporg-developer-2023/front-page-content
 * Inserter: no
 */

?>

<!-- wp:group {"align":"full","style":{"spacing":{"blockGap":"0"}},"backgroundColor":"charcoal-2","className":"is-sticky","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull is-sticky has-charcoal-2-background-color has-background"><!-- wp:wporg/local-navigation-bar {"backgroundColor":"charcoal-2","style":{"spacing":{"padding":{"right":"var:preset|spacing|60","left":"var:preset|spacing|60","top":"18px","bottom":"18px"}},"position":{"type":"sticky"},"border":{"top":{"color":"var:preset|color|opacities-white-10","width":"1px"}}}} -->
<!-- wp:html -->
<div style="height:calc(var(--wp--custom--body--small--typography--line-height) * var(--wp--preset--font-size--small));" aria-hidden="true"></div>
<!-- /wp:html -->

<!-- wp:navigation {"hasIcon":false,"layout":{"type":"flex","orientation":"horizontal"},"fontSize":"small","menuSlug":"developer"} /-->
<!-- /wp:wporg/local-navigation-bar --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"right":"var:preset|spacing|edge-space","left":"var:preset|spacing|edge-space"}}},"backgroundColor":"charcoal-2","className":"has-white-color has-charcoal-2-background-color has-text-color has-background has-link-color","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-white-color has-charcoal-2-background-color has-text-color has-background has-link-color" style="padding-right:var(--wp--preset--spacing--edge-space);padding-left:var(--wp--preset--spacing--edge-space)"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"bottom"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"50px","lineHeight":"1.2"}},"fontFamily":"eb-garamond"} -->
<h1 class="wp-block-heading has-eb-garamond-font-family" style="font-size:50px;line-height:1.2"><?php esc_html_e( 'Developer Resources', 'wporg' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"lineHeight":"2.3"}},"textColor":"white"} -->
<p class="has-white-color has-text-color" style="line-height:2.3"><?php esc_html_e( '/The freedom to build', 'wporg' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10"}}},"backgroundColor":"lemon-3","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-lemon-3-background-color has-background" style="padding-top:var(--wp--preset--spacing--10);padding-bottom:var(--wp--preset--spacing--10)"><!-- wp:paragraph {"align":"center","fontSize":"extra-small"} -->
<p class="has-text-align-center has-extra-small-font-size"><?php echo wp_kses_post(
	sprintf(
	/* translators: %1$s: version link, %2$s: version number */
		__( 'See <a href="%1$s">what has changed</a> in the WordPress %2$s API.', 'wporg' ),
		'[wordpress_version_link]',
		'[wordpress_version]'
	)
); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"right":"var:preset|spacing|edge-space","left":"var:preset|spacing|edge-space","top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}},"border":{"bottom":{"color":"var:preset|color|light-grey-1","width":"1px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="border-bottom-color:var(--wp--preset--color--light-grey-1);border-bottom-width:1px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--edge-space);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--edge-space)"><!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:search {"label":"<?php esc_attr_e( 'Search', 'wporg' ); ?>","showLabel":false,"placeholder":"<?php esc_attr_e( 'Search resources', 'wporg' ); ?>","width":240,"widthUnit":"px","buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"entry-content"} -->
<div class="wp-block-group alignfull entry-content"><!-- wp:group {"align":"full","style":{"spacing":{"padding":{"right":"var:preset|spacing|edge-space","left":"var:preset|spacing|edge-space"}}},"className":"home-section-docs-api has-linen-background-color has-background","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull home-section-docs-api has-linen-background-color has-background" style="padding-right:var(--wp--preset--spacing--edge-space);padding-left:var(--wp--preset--spacing--edge-space)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"0","left":"0"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}}} -->
<div class="wp-block-column" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:heading {"style":{"elements":{"link":{"color":{"text":"var:preset|color|charcoal-1"}}},"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}},"className":"wp-block-heading is-style-default"} -->
<h2 class="wp-block-heading is-style-default has-link-color" style="margin-bottom:var(--wp--preset--spacing--20)"><?php esc_attr_e( 'Documentation', 'wporg' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0"}}}} -->
<p style="margin-top:0"><a href="https://developer.wordpress.org/block-editor" data-type="helphub_article"><?php esc_attr_e( 'Block Editor', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0"}}}} -->
<p style="margin-top:0"><a href="https://developer.wordpress.org/themes" data-type="helphub_article"><?php esc_attr_e( 'Themes', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0"}}}} -->
<p style="margin-top:0"><a href="https://developer.wordpress.org/plugins"><?php esc_attr_e( 'Plugins', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0"}}}} -->
<p style="margin-top:0"><a href="https://developer.wordpress.org/apis" data-type="URL" data-id="https://developers.wordpress.org/apis"><?php esc_attr_e( 'Common APIs', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0"}}}} -->
<p style="margin-top:0"><a href="https://developer.wordpress.org/advanced-administration"><?php esc_attr_e( 'Advanced Administration', 'wporg' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}}} -->
<div class="wp-block-column" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:heading {"style":{"elements":{"link":{"color":{"text":"var:preset|color|charcoal-1"}}},"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} -->
<h2 class="wp-block-heading has-link-color" style="margin-bottom:var(--wp--preset--spacing--20)"><?php esc_attr_e( 'API Reference', 'wporg' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0"}}}} -->
<p style="margin-top:0"><a href="https://developer.wordpress.org/reference" data-type="helphub_article"><?php esc_attr_e( 'Code reference', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0"}}}} -->
<p style="margin-top:0"><a href="https://developer.wordpress.org/rest-api/" data-type="helphub_article"><?php esc_attr_e( 'REST API', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0"}}}} -->
<p style="margin-top:0"><a href="https://developer.wordpress.org/apis/making-http-requests/" data-type="helphub_article"><?php esc_attr_e( 'HTTP API', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0"}}}} -->
<p style="margin-top:0"><a href="https://developer.wordpress.org/cli/commands" data-type="URL" data-id="https://developers.wordpress.org/cli/commands"><?php esc_attr_e( 'WP-CLI', 'wporg' ); ?></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0"}}}} -->
<p style="margin-top:0"><a href="https://developer.wordpress.org/coding-standards/" data-type="URL" data-id="https://developers.wordpress.org/coding-standards/"><?php esc_attr_e( 'Coding Standards', 'wporg' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"right":"var:preset|spacing|edge-space","left":"var:preset|spacing|edge-space"}},"border":{"bottom":{"width":"1px","style":"solid","color":"var:preset|color|light-grey-1"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="border-bottom-color:var(--wp--preset--color--light-grey-1);border-bottom-style:solid;border-bottom-width:1px;padding-right:var(--wp--preset--spacing--edge-space);padding-left:var(--wp--preset--spacing--edge-space)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":"0px","padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"className":"is-style-default"} -->
<div class="wp-block-columns alignwide is-style-default" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:column {"verticalAlignment":"top","width":"33%","className":"is-left-column","layout":{"inherit":false}} -->
<div class="wp-block-column is-vertically-aligned-top is-left-column" style="flex-basis:33%"><!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}},"className":"has-heading-2-font-size"} -->
<h2 class="wp-block-heading has-heading-2-font-size" style="margin-bottom:var(--wp--preset--spacing--20)"><?php esc_attr_e( 'More Resources', 'wporg' ); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->



<!-- wp:column {"width":"66%","layout":{"inherit":false}} -->
<div class="wp-block-column" style="flex-basis:66%"><!-- wp:group {"style":{"spacing":{"margin":{"top":"5px"}}}} -->
<div class="wp-block-group" style="margin-top:5px"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"left"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"is-style-short-text"} -->
<p class="is-style-short-text"><a href="https://learn.wordpress.org"><?php esc_attr_e( 'Learn WordPress', 'wporg' ); ?></a></p>
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

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"right":"var:preset|spacing|edge-space","left":"var:preset|spacing|edge-space"}}},"backgroundColor":"white","layout":{"type":"constrained"},"anchor":"news"} -->
<div class="wp-block-group alignfull has-white-background-color has-background" id="news" style="padding-right:var(--wp--preset--spacing--edge-space);padding-left:var(--wp--preset--spacing--edge-space)"><!-- wp:columns {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"},"blockGap":"0px"}},"className":"is-style-default"} -->
<div class="wp-block-columns alignwide is-style-default" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:column {"verticalAlignment":"top","width":"33%","className":"is-left-column","layout":{"inherit":false}} -->
<div class="wp-block-column is-vertically-aligned-top is-left-column" style="flex-basis:33%"><!-- wp:heading {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}},"fontSize":"heading-2"} -->
<h2 class="wp-block-heading has-heading-2-font-size" style="margin-bottom:var(--wp--preset--spacing--20)"><?php esc_attr_e( 'Latest Posts', 'wporg' ); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"66%","layout":{"inherit":false}} -->
<div class="wp-block-column" style="flex-basis:66%"><!-- wp:group {"style":{"spacing":{"margin":{"top":"8px"}}}} -->
<div class="wp-block-group" style="margin-top:8px"><!-- wp:wporg/latest-news {"blogId":719} /-->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--50)"><a href="https://developer.wordpress.org/news/"><?php esc_attr_e( 'View all', 'wporg' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"left":"var:preset|spacing|edge-space","right":"var:preset|spacing|edge-space"}}},"backgroundColor":"aquamarine","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull has-aquamarine-background-color has-background" style="padding-right:var(--wp--preset--spacing--edge-space);padding-left:var(--wp--preset--spacing--edge-space)"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained","justifyContent":"center"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"70%"} -->
<div class="wp-block-column" style="flex-basis:70%"><!-- wp:heading {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"}}},"textColor":"charcoal-1"} -->
<h2 class="wp-block-heading has-charcoal-1-color has-text-color" style="margin-bottom:var(--wp--preset--spacing--50)"><?php esc_attr_e( 'Are you passionate about web development? Join the WordPress community and help shape the future of the web', 'wporg' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"charcoal-2","textColor":"white","className":"is-style-fill"} -->
<div class="wp-block-button is-style-fill"><a class="wp-block-button__link has-white-color has-charcoal-2-background-color has-text-color has-background wp-element-button" href="https://make.wordpress.org"><?php esc_attr_e( 'Contribute', 'wporg' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"backgroundColor":"aquamarine","textColor":"charcoal-2","className":"external-link is-style-outline"} -->
<div class="wp-block-button external-link is-style-outline"><a class="wp-block-button__link has-charcoal-2-color has-aquamarine-background-color has-text-color has-background wp-element-button" href="https://make.wordpress.org/core/handbook/"><?php esc_attr_e( 'Check Out Contributor Handbook ↗', 'wporg' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
