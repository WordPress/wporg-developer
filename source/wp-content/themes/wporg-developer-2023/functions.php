<?php

namespace DevHub;

/**
 * Registrations (post type, taxonomies, etc).
 */
require __DIR__ . '/inc/registrations.php';

/**
 * HTML head tags and customizations.
 */
require __DIR__ . '/inc/head.php';

/**
 * Custom template tags for this theme.
 */
require __DIR__ . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require __DIR__ . '/inc/extras.php';

/**
 * Load Jetpack compatibility file.
 */
require __DIR__ . '/inc/jetpack.php';

/**
 * Class for editing parsed content on the Function, Class, Hook, and Method screens.
 */
require_once( __DIR__ . '/inc/parsed-content.php' );

if ( ! function_exists( 'loop_pagination' ) ) {
	require __DIR__ . '/inc/loop-pagination.php';
}

if ( ! function_exists( 'breadcrumb_trail' ) ) {
	require __DIR__ . '/inc/breadcrumb-trail.php';
}

/**
 * User-submitted content (comments, notes, etc).
 */
require __DIR__ . '/inc/user-content.php';

/**
 * User-submitted content preview.
 */
require __DIR__ . '/inc/user-content-preview.php';

/**
 * Voting for user-submitted content.
 */
require __DIR__ . '/inc/user-content-voting.php';

/**
 * Editing for user-submitted content.
 */
require __DIR__ . '/inc/user-content-edit.php';

/**
 * CLI commands custom post type and importer.
 */
require __DIR__ . '/inc/cli.php';

/**
 * Docs importer.
 */
if ( class_exists( '\\WordPressdotorg\\Markdown\\Importer' ) ) {
	// Docs Importer base class.
	require __DIR__ . '/inc/import-docs.php';

	// Block Editor handbook.
	require __DIR__ . '/inc/import-block-editor.php';

	// Coding Standards handbook.
	require __DIR__ . '/inc/import-coding-standards.php';

	// REST API handbook.
	require __DIR__ . '/inc/rest-api.php';
}

/**
 * Explanations for functions. hooks, classes, and methods.
 */
require( __DIR__ . '/inc/explanations.php' );

/**
 * Handbooks.
 */
require __DIR__ . '/inc/handbooks.php';

/**
 * Redirects.
 */
require __DIR__ . '/inc/redirects.php';

/**
 * Content formatting.
 */
require __DIR__ . '/inc/formatting.php';

/**
 * Autocomplete.
 */
require __DIR__ . '/inc/autocomplete.php';

/**
 * Search query.
 */
require __DIR__ . '/inc/search.php';

/**
 * Parser customizations.
 */
require __DIR__ . '/inc/parser.php';

/**
 * CLI commands.
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require __DIR__ . '/inc/cli-commands.php';
}

/**
 * Admin area customizations.
 */
if ( is_admin() ) {
	require __DIR__ . '/inc/admin.php';
}

/**
 * Block Hooks.
 */
require __DIR__ . '/inc/block-hooks.php';

// Block files
require_once __DIR__ . '/src/code-changelog/block.php';
require_once __DIR__ . '/src/code-deprecated/block.php';
require_once __DIR__ . '/src/code-description/block.php';
require_once __DIR__ . '/src/code-explanation/block.php';
require_once __DIR__ . '/src/code-hooks/block.php';
require_once __DIR__ . '/src/code-parameters/block.php';
require_once __DIR__ . '/src/code-private-access/block.php';
require_once __DIR__ . '/src/code-related/block.php';
require_once __DIR__ . '/src/code-return-value/block.php';
require_once __DIR__ . '/src/code-source/block.php';
require_once __DIR__ . '/src/code-summary/block.php';
require_once __DIR__ . '/src/code-title/block.php';
require_once __DIR__ . '/src/code-user-notes/block.php';
require_once __DIR__ . '/src/search-filters/index.php';
require_once __DIR__ . '/src/search-results-context/index.php';
require_once __DIR__ . '/src/search-title/index.php';
require_once __DIR__ . '/src/search-usage-info/index.php';

add_action( 'init', __NAMESPACE__ . '\\init' );

/**
 * Set up the theme.
 */
function init() {

	register_nav_menus();

	add_action( 'pre_get_posts', __NAMESPACE__ . '\\pre_get_posts' );
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\theme_scripts_styles' );
	add_action( 'add_meta_boxes', __NAMESPACE__ . '\\rename_comments_meta_box', 10, 2 );

	add_filter( 'post_type_link', __NAMESPACE__ . '\\method_permalink', 11, 2 );
	add_filter( 'term_link', __NAMESPACE__ . '\\taxonomy_permalink', 10, 3 );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );

	// Modify default breadcrumbs.
	add_filter( 'breadcrumb_trail_items', __NAMESPACE__ . '\\breadcrumb_trail_items_for_hooks', 10, 2 );
	add_filter( 'breadcrumb_trail_items', __NAMESPACE__ . '\\breadcrumb_trail_items_for_handbook_root', 10, 2 );

	add_filter( 'mkaz_code_syntax_force_loading', '__return_true' );
	add_filter( 'mkaz_prism_css_path', __NAMESPACE__ . '\\update_prism_css_path' );
}

/**
 * Fix breadcrumb for hooks.
 *
 * A hook has a parent (the function containing it), which causes the Breadcrumb
 * Trail plugin to introduce trail items related to the parent that shouldn't
 * be shown.
 *
 * @param  array $items The breadcrumb trail items.
 * @param  array $args  Original args.
 * @return array
 */
function breadcrumb_trail_items_for_hooks( $items, $args ) {
	$post_type = 'wp-parser-hook';

	// Bail early when not the single archive for hook
	if ( ! is_singular() || get_post_type() !== $post_type || ! isset( $items[4] ) ) {
		return $items;
	}

	$post_type_object = get_post_type_object( $post_type );

	// Replaces 'Functions' archive link with 'Hooks' archive link
	$items[2] = '<a href="' . get_post_type_archive_link( $post_type ) . '">' . $post_type_object->labels->name . '</a>';
	// Replace what the plugin thinks is the parent with the hook name
	$items[3] = $items[4];
	// Unset the last element since it shifted up in trail hierarchy
	unset( $items[4] );

	return $items;
}

/**
 * Fix breadcrumb for handbook root pages.
 *
 * The handbook root/landing pages do not need a duplicated breadcrumb trail
 * item that simply links to the currently loaded page. The trailing breadcrumb
 * item is already the unlinked handbook name, which is sufficient.
 *
 * @param  array $items The breadcrumb trail items.
 * @param  array $args  Original args.
 * @return array
 */
function breadcrumb_trail_items_for_handbook_root( $items, $args ) {
	// Bail early if not a handbook landing page.
	if ( ! function_exists( 'wporg_is_handbook_landing_page' ) || ! wporg_is_handbook_landing_page() ) {
		return $items;
	}

	// Unset link to current handbook.
	unset( $items[1] );

	return $items;
}

/**
 * Modify the default query.
 *
 * @param \WP_Query $query
 */
function pre_get_posts( $query ) {

	if ( $query->is_main_query() && $query->is_post_type_archive() ) {
		$query->set( 'orderby', 'title' );
		$query->set( 'order', 'ASC' );
	}

	if ( $query->is_main_query() && $query->is_tax() && $query->get( 'wp-parser-source-file' ) ) {
		$query->set( 'wp-parser-source-file', str_replace( array( '.php', '/' ), array( '-php', '_' ), $query->query['wp-parser-source-file'] ) );
	}

	// For search query modifications see DevHub_Search.
}

/**
 * Regiser navigation menu area.
 */
function register_nav_menus() {
	\register_nav_menus(
		array(
			'devhub-menu' => __( 'Developer Resources Menu', 'wporg' ),
			'devhub-cli-menu' => __( 'WP-CLI Commands Menu', 'wporg' ),
			'reference-home-api' => __( 'Reference API Menu', 'wporg' ),
		)
	);
}

/**
 * Filters the permalink for a wp-parser-method post.
 *
 * @param string   $link The post's permalink.
 * @param \WP_Post $post The post in question.
 * @return string
 */
function method_permalink( $link, $post ) {
	global $wp_rewrite;

	if ( ! $wp_rewrite->using_permalinks() || ( 'wp-parser-method' !== $post->post_type ) ) {
		return $link;
	}

	$parts  = explode( '-', $post->post_name );
	$method = array_pop( $parts );
	$class  = implode( '-', $parts );

	return home_url( user_trailingslashit( "reference/classes/$class/$method" ) );
}

/**
 * Filer the permalink for a taxonomy.
 */
function taxonomy_permalink( $link, $term, $taxonomy ) {
	global $wp_rewrite;

	if ( ! $wp_rewrite->using_permalinks() ) {
		return $link;
	}

	if ( 'wp-parser-source-file' === $taxonomy ) {
		$slug = $term->slug;
		if ( substr( $slug, -4 ) === '-php' ) {
			$slug = substr( $slug, 0, -4 ) . '.php';
			$slug = str_replace( '_', '/', $slug );
		}
		$link = home_url( user_trailingslashit( "reference/files/$slug" ) );
	} elseif ( 'wp-parser-since' === $taxonomy ) {
		$link = str_replace( $term->slug, str_replace( '-', '.', $term->slug ), $link );
	}

	return $link;
}

/**
 * Register and enqueue the theme assets.
 */
function theme_scripts_styles() {
	// The parent style is registered as `wporg-parent-2021-style`, and will be loaded unless
	// explicitly unregistered. We can load any child-theme overrides by declaring the parent
	// stylesheet as a dependency.
	wp_enqueue_style(
		'wporg-developer-2023-style',
		get_stylesheet_directory_uri() . '/build/style/style-index.css',
		array( 'wporg-parent-2021-style', 'wporg-global-fonts' ),
		filemtime( __DIR__ . '/build/style/style-index.css' )
	);
}

/**
 * Rename the 'Comments' meta box to 'User Contributed Notes' for reference-editing screens.
 *
 * @param string  $post_type Post type.
 * @param WP_Post $post      WP_Post object for the current post.
 */
function rename_comments_meta_box( $post_type, $post ) {
	if ( is_parsed_post_type( $post_type ) ) {
		remove_meta_box( 'commentsdiv', $post_type, 'normal' );
		add_meta_box( 'commentsdiv', __( 'User Contributed Notes', 'wporg' ), 'post_comment_meta_box', $post_type, 'normal', 'high' );
	}
}

/**
 * Customize the syntax highlighter style.
 * See https://github.com/PrismJS/prism-themes.
 *
 * @param string $path Path to the file to override, relative to the theme.
 * @return string
 */
function update_prism_css_path( $path ) {
	return '/stylesheets/prism.css';
}
