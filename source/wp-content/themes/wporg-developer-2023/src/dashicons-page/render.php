<?php

namespace WordPressdotorg\Theme\Developer_2023\Block_Dashicons_Page;

require_once dirname( dirname( __DIR__ ) ) . '/inc/dashicons.php';

wp_enqueue_style(
	'dashicons-page',
	get_stylesheet_directory_uri() . '/stylesheets/page-dashicons.css',
	array(),
	get_stylesheet_directory() . '/stylesheets/page-dashicons.css'
);

$icons = array();
$icons_sections = array();

foreach ( \DevHub_Dashicons::get_dashicons() as $section_group => $section ) {
	$icon_section = array(
		'label' => $section['label'],
		'slug' => sanitize_title( $section_group ),
		'icons' => array(),
	);
	foreach ( $section['icons'] as $k => $v ) {
		$icons[$k] = $v;
		$icons[$k]['sectionLabel'] = $section['label'];
		$icon_section['icons'][] = $k;
	}
	$icons_sections[] = $icon_section;
}

$selected_icon = array_rand( $icons );

wp_interactivity_state(
	'wporg/dashicons-page',
	array(
		/*
		 * START: Derived state.
		 *
		 * All these "derived state" getters must be defined in view.js as well
		 */
		'iconClass' => function() {
			return 'dashicons ' . wp_interactivity_get_context()['icon'];
		},
		'sectionAnchorTarget' => function() {
			return 'icons-' . wp_interactivity_get_context()['section']['slug'];
		},
		'sectionAnchorHref' => function () {
			return '#icons-' . wp_interactivity_get_context()['section']['slug'];
		},
		'iconSectionLabel' => function() {
			return wp_interactivity_get_context()['section']['label'];
		},
		'eachIcon' => function() use ( $icons ) {
			return $icons[ wp_interactivity_get_context()['icon'] ];
		},
		/*
		 * END: Derived state
		 */

		'iconsSections' => $icons_sections,
		'icons' => $icons,
		'selectedIcon' => array( $selected_icon ),
	)
);

$interactivity_context = array(
);

$deprecation_notice = sprintf(
	'<!-- wp:wporg/notice {"type":"alert"} -->
<div class="wp-block-wporg-notice is-alert-notice">
<div class="wp-block-wporg-notice__icon"></div>
<div class="wp-block-wporg-notice__content"><p>%s</p></div>
</div>
<!-- /wp:wporg/notice -->',
	__( 'The Dashicons project is no longer accepting icon requests. Here’s why:&nbsp;<a href="https://make.wordpress.org/design/2020/04/20/next-steps-for-dashicons/">Next steps for Dashicons</a>.', 'wporg' )
);

?>
<div id="content-area" <?php body_class( 'dashicons-page' ); ?> data-wp-interactive="wporg/dashicons-page"
	<?php echo wp_interactivity_data_wp_context( $interactivity_context ); ?>>
	<main id="main" <?php post_class( 'site-main' ); ?> role="main">

		<?php echo do_blocks( wp_kses_post( $deprecation_notice ) ); ?>

		<div class="details clear">
			<div id="glyph">
				<template data-wp-each--icon="state.selectedIcon" data-wp-each-key="state.eachIcon.slug">
					<div data-wp-bind--class="state.iconClass"></div>
					<div class="info">
						<span><strong data-wp-text="state.eachIcon.sectionLabel"></strong></span>
						<span class="name"><code data-wp-text="state.selectedIcon.0"></code></span>
						<span class="charCode"><code data-wp-text="state.eachIcon.code"></code></span>
						<span class="link"><a href='javascript:dashicons.copy( "content: \"\\{{data.attr}}\";", "css" )'><?php _e( 'Copy CSS', 'wporg' ); ?></a></span>
						<span class="link"><a href="javascript:dashicons.copy( '{{data.html}}', 'html' )"><?php _e( 'Copy HTML', 'wporg' ); ?></a></span>
						<span class="link"><a href="javascript:dashicons.copy( '{{data.glyph}}' )"><?php _e( 'Copy Glyph', 'wporg' ); ?></a></span>
					</div>
				</template>
			</div>
			<div class="entry-content">
				<?php the_content(); ?>
			</div><!-- .entry-content -->
			<div class="icon-filter">
				<input placeholder="<?php esc_attr_e( 'Filter&hellip;', 'wporg' ); ?>" name="search" id="search" type="text" value="" maxlength="150">
			</div>
		</div>
		<div id="icons">
			<div id="iconlist">
				<template
					data-wp-each--section="state.iconsSections"
					data-wp-each-key="context.section.slug"
				>
					<h4 data-wp-bind--id="state.sectionAnchorTarget">
						<span data-wp-text="context.section.label"></span>
						<a data-wp-bind--href="state.sectionAnchorHref" class="anchor"><span aria-hidden="true">#</span><span class="screen-reader-text" data-wp-text="context.section.label"></span></a>
					</h4>
					<ul>
						<template
							data-wp-each--icon="context.section.icons"
							data-wp-each-key="state.eachIcon.slug"
						>
							<li data-wp-bind--data-keywords="state.eachIcon.keywords" data-wp-bind--data-code="state.sectionIcon.code" data-wp-bind--class="state.iconClass" data-wp-on--click="actions.handleIconClick">
								<span data-wp-text="state.eachIcon.label"></span>
							</li>
						</template>
					</ul>
				</template>
			</div>
		</div>

		<div id="instructions">
			<h3><?php _e( 'WordPress Usage', 'wporg' ); ?></h3>
			<p>
			<?php  printf(
				__( 'Admin menu items can be added with <code><a href="%1$s">register_post_type()</a></code> and <code><a href="%2$s">add_menu_page()</a></code>, which both have an option to set an icon. To show the current icon, you should pass in %3$s.', 'wporg' ),
				'https://developer.wordpress.org/reference/functions/register_post_type/',
				'https://developer.wordpress.org/reference/functions/add_menu_page/',
				'<code>\'dashicons-<span id="wp-class-example">{icon}</span>\'</code>'
			); ?></p>

			<h4><?php _e( 'Examples', 'wporg' ); ?></h4>
			<p><?php printf(
				__( 'In <code><a href="%s">register_post_type()</a></code>, set <code>menu_icon</code> in the arguments array.', 'wporg' ),
				'https://developer.wordpress.org/reference/functions/register_post_type/'
			); ?></p>
		
<pre>&lt;?php
/**
* Register the Product post type with a Dashicon.
*
* @see register_post_type()
*/
function wpdocs_create_post_type() {
	register_post_type( 'acme_product',
		array(
			'labels' => array(
				'name'          => __( 'Products', 'textdomain' ),
				'singular_name' => __( 'Product', 'textdomain' )
			),
			'public'      => true,
			'has_archive' => true,
			'menu_icon'   => 'dashicons-products',
		)
	);
}
add_action( 'init', 'wpdocs_create_post_type', 0 );
</pre>
				<p>
				<?php printf(
					__( 'The function <code><a href="%s">add_menu_page()</a></code> accepts a parameter after the callback function for an icon URL, which can also accept a dashicons class.', 'wporg' ),
					'https://developer.wordpress.org/reference/functions/add_menu_page/'
				); ?></p>
<pre>&lt;?php
/**
* Register a menu page with a Dashicon.
*
* @see add_menu_page()
*/
function wpdocs_add_my_custom_menu() {
	// Add an item to the menu.
	add_menu_page(
		__( 'My Page', 'textdomain' ),
		__( 'My Title', 'textdomain' ),
		'manage_options',
		'my-page',
		'my_admin_page_function',
		'dashicons-admin-media'
	);
}</pre>
				<h3><?php _e( 'CSS/HTML Usage', 'wporg' ); ?></h3>
				<p><?php _e( "If you want to use dashicons in the admin outside of the menu, there are two helper classes you can use. These are <code>dashicons-before</code> and <code>dashicons</code>, and they can be thought of as setting up dashicons (since you still need your icon's class, too).", 'wporg' ); ?></p>
				<h4><?php _e( 'Examples', 'wporg' ); ?></h4>
				<p><?php _e( 'Adding an icon to a header, with the <code>dashicons-before</code> class. This can be added right to the element with text.', 'wporg' ); ?></p>
<pre>
&lt;h2 class="dashicons-before dashicons-smiley"&gt;<?php _e( 'A Cheerful Headline', 'wporg' ); ?>&lt;/h2&gt;
</pre>
				<p><?php _e( 'Adding an icon to a header, with the <code>dashicons</code> class. Note that here, you need extra markup specifically for the icon.', 'wporg' ); ?></p>
<pre>
&lt;h2&gt;&lt;span class="dashicons dashicons-smiley"&gt;&lt;/span&gt; <?php _e( 'A Cheerful Headline', 'wporg' ); ?>&lt;/h2&gt;
</pre>
				<h3><?php _e( 'Block Usage', 'wporg' ); ?></h3>
				<p><?php _e( 'The block editor supports use of dashicons as block icons and as its own component.', 'wporg' ); ?></p>
				<h4><?php _e( 'Examples', 'wporg' ); ?></h4>
				<p>
				<?php printf(
					/* translators: %s: URL to Block Editor Handbook for registering a block. */
					__( 'Adding an icon to a block. The <code>registerBlockType</code> function accepts a parameter "icon" which accepts the name of a dashicon. The provided example is truncated. See the <a href="%s">full example</a> in the Block Editor Handbook.', 'wporg' ),
					'https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/writing-your-first-block-type/#registering-the-block'
				); ?></p>
<pre>
registerBlockType( 'gutenberg-examples/example-01-basic-esnext', {
	apiVersion: 2,
	title: 'Example: Basic (esnext)',
	icon: 'universal-access-alt',
	category: 'design',
	example: {},
	edit() {},
	save() {},
} );
</pre>
				<p>
				<?php printf(
					/* translators: %s: URL to handbook page for Dashicon component. */
					__( 'Using an icon as a component. A dedicated <code>Dashicon</code> component is available. See the <a href="%s">related documentation</a> in the Block Editor Handbook.', 'wporg' ),
					'https://developer.wordpress.org/block-editor/reference-guides/components/dashicon/'
				); ?></p>
<pre>
import { Dashicon } from '@wordpress/components';

const MyDashicon = () =&gt; (
	&lt;div&gt;
		&lt;Dashicon icon="admin-home" /&gt;
		&lt;Dashicon icon="products" /&gt;
		&lt;Dashicon icon="wordpress" /&gt;
	&lt;/div&gt;
);
</pre>
			<h3><?php _e( 'Photoshop Usage', 'wporg' ); ?></h3>
			<p><?php _e( 'Use the .OTF version of the font for Photoshop mockups, the web-font versions won\'t work. For most accurate results, pick the "Sharp" font smoothing.', 'wporg' ); ?></p>
		</div><!-- /#instructions -->
	</main><!-- #main -->
	<!-- Required for the Copy Glyph functionality -->
	<div id="temp" style="display:none;"></div>
	<!--
	<script type="text/html" id="tmpl-glyphs">
	</script>
	-->
</div><!-- #primary -->
