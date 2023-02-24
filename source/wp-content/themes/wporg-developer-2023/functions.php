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
require_once __DIR__ . '/inc/parsed-content.php';

if ( ! function_exists( 'loop_pagination' ) ) {
	require __DIR__ . '/inc/loop-pagination.php';
}

if ( ! function_exists( 'get_breadcrumbs' ) ) {
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
require __DIR__ . '/inc/explanations.php';

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
 * Shortcodes.
 */
require __DIR__ . '/inc/shortcodes.php';

/**
 * Shortcode: Dashicons.
 */
require __DIR__ . '/inc/shortcode-dashicons.php';

/**
 * Block Hooks.
 */
require __DIR__ . '/inc/block-hooks.php';

// Block files
require_once __DIR__ . '/src/chapter-list/block.php';
require_once __DIR__ . '/src/cli-command-table/block.php';
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
require_once __DIR__ . '/src/code-short-title/index.php';
require_once __DIR__ . '/src/code-summary/block.php';
require_once __DIR__ . '/src/code-table/block.php';
require_once __DIR__ . '/src/code-title/block.php';
require_once __DIR__ . '/src/code-type-usage-info/index.php';
require_once __DIR__ . '/src/code-comments/block.php';
require_once __DIR__ . '/src/code-comment-edit/block.php';
require_once __DIR__ . '/src/code-comment-form/block.php';
require_once __DIR__ . '/src/form-wrapper/block.php';
require_once __DIR__ . '/src/command-content/block.php';
require_once __DIR__ . '/src/command-github/block.php';
require_once __DIR__ . '/src/command-title/block.php';
require_once __DIR__ . '/src/command-subcommand/block.php';
require_once __DIR__ . '/src/search-filters/index.php';
require_once __DIR__ . '/src/search-results-context/index.php';
require_once __DIR__ . '/src/version-select/index.php';

add_action( 'init', __NAMESPACE__ . '\\init' );
add_filter( 'wporg_block_site_breadcrumbs', __NAMESPACE__ . '\set_site_breadcrumbs' );
add_filter( 'single_template_hierarchy', __NAMESPACE__ . '\add_handbook_templates' );
add_filter( 'next_post_link', __NAMESPACE__ . '\get_adjacent_handbook_post_link', 10, 5 );
add_filter( 'previous_post_link', __NAMESPACE__ . '\get_adjacent_handbook_post_link', 10, 5 );

// Priority must be lower than 5 to precede table of contents filter.
// See: https://github.com/WordPress/wporg-mu-plugins/blob/trunk/mu-plugins/blocks/table-of-contents/index.php#L70
add_filter( 'the_content', __NAMESPACE__ . '\filter_code_content', 4 );
add_filter( 'wporg_table_of_contents_post_content', __NAMESPACE__ . '\filter_code_content' );
add_filter( 'the_content', __NAMESPACE__ . '\filter_command_content', 4 );
add_filter( 'wporg_table_of_contents_post_content', __NAMESPACE__ . '\filter_command_content' );


// Remove table of contents.
add_filter( 'wporg_handbook_toc_should_add_toc', '__return_false' );

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
	add_filter( 'breadcrumb_trail_items', __NAMESPACE__ . '\\breadcrumb_trail_items_remove_reference', 11, 2 );
	add_filter( 'breadcrumb_trail_items', __NAMESPACE__ . '\\breadcrumb_trail_items_for_handbook_root', 10, 2 );
	add_filter( 'breadcrumb_trail_items', __NAMESPACE__ . '\\breadcrumb_trail_for_note_edit', 10, 2 );
	add_filter( 'breadcrumb_trail_items', __NAMESPACE__ . '\\breadcrumb_trail_for_since_view', 10, 2 );

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
 * Remove the 'Reference' part of the breadcrumb trail.
 *
 * @param  array $items The breadcrumb trail items.
 * @param  array $args  Original args.
 * @return array
 */
function breadcrumb_trail_items_remove_reference( $items, $args ) {
	if ( ! is_singular() && ! is_single() && ! is_post_type_archive() && ! is_archive() ) {
		return $items;
	}

	return array_filter(
		$items,
		function( $item ) {
			// Remove the 'reference' parent based on the presence of its URL.
			// We can't use the label because of internationalization.
			$result = (bool) preg_match( '!href="[^"]+/reference/"!', $item );
			return ( false === $result );
		}
	);
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
 * Fix the breadcrumb trail for the edit note page.
 *
 * @param array $items
 * @return array
 */
function breadcrumb_trail_for_note_edit( $items ) {
	if ( empty( get_query_var( 'edit_user_note' ) ) || ! is_single() ) {
		return $items;
	}

	$comment_id  = get_query_var( 'edit_user_note' );
	$comment     = get_comment( $comment_id );
	$post        = get_queried_object();
	$post_id     = get_queried_object_id();
	$post_url    = get_permalink( $post_id );
	$post_title  = single_post_title( '', false );
	$post_types  = \DevHub\get_parsed_post_types( 'labels' );
	$type_single = get_post_type_object( $post->post_type )->labels->singular_name;
	$type_url    = get_post_type_archive_link( $post->post_type );
	$type_label  = $post_types[ $post->post_type ];

	$breadcrumbs   = array( $items[0] ); // Ie: Home
	$breadcrumbs[] = sprintf( '<a href="%s">%s</a>', esc_url( $type_url ), $type_label );
	$breadcrumbs[] = sprintf( '<a href="%s">%s</a>', esc_url( $post_url ), $post_title );
	$breadcrumbs[] = sprintf(
		'<a href="%s">%s</a>',
		esc_url( $post_url . '#comment-' . $comment_id ),
		sprintf(  /* translators: %d: comment ID */
			$comment->comment_parent ? __( 'feedback %d', 'wporg' ) : __( 'note %d', 'wporg' ),
			$comment_id
		)
	);
	$breadcrumbs[] = __( 'Edit', 'wporg' );
	return $breadcrumbs;
}

/**
 * Fix breadcrumb for wp-parser-since archive.
 *
 * @param  array $items The breadcrumb trail items.
 * @param  array $args  Original args.
 * @return array
 */
function breadcrumb_trail_for_since_view( $items, $args ) {

	if ( ! is_archive() || ! get_query_var( 'wp-parser-since' ) ) {
		return $items;
	}

	// Remove the last item
	unset( $items[ count( $items ) - 1 ] );

	$items[] = sprintf(
		/* translators: %s: WordPress version  */
		__( 'New and updated in %s', 'wporg' ),
		get_query_var( 'wp-parser-since' )
	);

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
			'devhub-menu'        => __( 'Developer Resources Menu', 'wporg' ),
			'devhub-cli-menu'    => __( 'WP-CLI Commands Menu', 'wporg' ),
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

/**
 * Filters breadcrumb items for the site-breadcrumb block.
 *
 * @return array
 */
function set_site_breadcrumbs() {
	$breadcrumbs = array();

	foreach ( get_breadcrumbs()->items as $crumb ) {
		// Get the link and title from the breadcrumb.
		preg_match( '!<a[^>]+href="(?P<href>[^"]+)"[^>]*>(?P<title>.+)</a>!', $crumb, $matches );

		$breadcrumbs[] = array(
			'url'   => $matches['href'] ?? '',
			'title' => $matches['title'] ?? $crumb,
		);
	}

	return $breadcrumbs;
}

/**
 * Filter the template heiarchy to add in a general handbook & github handbook template.
 *
 * @param string[] $templates A list of template candidates, in descending order of priority.
 * @return string[] Updated list of templates.
 */
function add_handbook_templates( $templates ) {
	$is_handbook      = wporg_is_handbook();
	$is_github_source = ! empty( get_post_meta( get_the_ID(), 'wporg_markdown_source', true ) );
	if ( $is_handbook ) {
		array_unshift( $templates, 'single-handbook.php' );
	}
	if ( $is_github_source ) {
		array_unshift( $templates, 'single-handbook-github.php' );
	}
	return $templates;
}

/**
 * Filters content for the code reference blocks so Table of Contents can be added.
 *
 * Note: This filter is added and removed in src/code-description/block.php to prevent infinite loops.
 * Any update to the function name should be reflected there.
 *
 * @param string $content
 * @return string
 */
function filter_code_content( $content ) {
	$post = get_post();

	if ( ! is_single() || ! is_parsed_post_type( $post->post_type ) ) {
		return $content;
	}

	return do_blocks(
		'
		<!-- wp:wporg/code-reference-summary /-->
		<!-- wp:wporg/code-reference-description /-->
		<!-- wp:wporg/code-reference-parameters /-->
		<!-- wp:wporg/code-reference-return-value /-->
		<!-- wp:wporg/code-reference-explanation /-->
		<!-- wp:wporg/code-reference-source /-->
		<!-- wp:wporg/code-reference-hooks /-->
		<!-- wp:wporg/code-reference-related /-->
		<!-- wp:wporg/code-reference-changelog /-->
		<!-- wp:wporg/code-reference-comments /-->
		<!-- wp:pattern {"slug":"wporg-developer-2023/article-meta"} /-->
	'
	);
}


/**
 * Filters content for the command content blocks so Table of Contents can be added.
 *
 * @param string $content
 * @return string
 */
function filter_command_content( $content ) {
	$post_type = get_post_type();

	if ( ! is_single() || ! ( 'command' == $post_type ) ) {
		return $content;
	}

	return do_blocks(
		'
		<!-- wp:wporg/command-title /-->
		<!-- wp:wporg/command-github /-->
		<!-- wp:wporg/command-content /-->
		<!-- wp:wporg/command-subcommand /-->
		'
	);
}

/**
 * Switch out the destination for next/prev links to mirror the Chapter List order.
 *
 * @param string  $output   The adjacent post link.
 * @param string  $format   Link anchor format.
 * @param string  $link     Link permalink format.
 * @param WP_Post $post     The adjacent post.
 * @param string  $adjacent Whether the post is previous or next.
 *
 * @return string Updated link tag.
 */
function get_adjacent_handbook_post_link( $output, $format, $link, $post, $adjacent ) {
	if ( ! wporg_is_handbook() ) {
		return $output;
	}

	$post_id = get_the_ID();
	$pages   = get_pages(
		array(
			'sort_column' => 'menu_order, title',
			'post_type'   => get_post_type( $post_id ),
		)
	);

	foreach ( $pages as $i => $page ) {
		if ( $page->ID === $post_id ) {
			$adj_index = 'previous' === $adjacent ? $i - 1 : $i + 1;
			break;
		}
	}

	if ( $adj_index < count( $pages ) && $adj_index > 0 ) {
		$post = $pages[ $adj_index ];
	} else {
		return '';
	}

	$title = apply_filters( 'the_title', $post->post_title, $post->ID );
	$url   = get_permalink( $post );

	$inlink = sprintf(
		'<a href="%1$s" rel="%2$s">%3$s</a>',
		$url,
		'previous' === $adjacent ? 'prev' : 'next',
		str_replace( '%title', $title, $link )
	);

	$output = str_replace( '%link', $inlink, $format );

	return $output;
}
