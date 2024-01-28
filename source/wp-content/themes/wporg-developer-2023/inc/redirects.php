<?php
/**
 * Code Reference redirects.
 *
 * @package wporg-developer
 */

/**
 * Class to handle redirects.
 */
class DevHub_Redirects {

	/**
	 * Initializer
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'do_init' ) );
	}

	/**
	 * Handles adding/removing hooks to perform redirects as needed.
	 */
	public static function do_init() {
		add_action( 'template_redirect', array( __CLASS__, 'redirect_blog' ) );
		add_action( 'template_redirect', array( __CLASS__, 'redirect_single_search_match' ) );
		add_action( 'template_redirect', array( __CLASS__, 'redirect_handbook' ) );
		add_action( 'template_redirect', array( __CLASS__, 'redirect_resources' ) );
		add_action( 'template_redirect', array( __CLASS__, 'redirect_singularized_handbooks' ), 1 );
		add_action( 'template_redirect', array( __CLASS__, 'redirect_pluralized_reference_post_types' ), 1 );
		add_action( 'template_redirect', array( __CLASS__, 'paginated_home_page_404' ) );
		add_action( 'template_redirect', array( __CLASS__, 'user_note_edit' ) );
	}

	/**
	 * Redirects a search query with only one result directly to that result.
	 *
	 * @globals \WP_Query $wp_query Global WP_Query instance.
	 */
	public static function redirect_single_search_match() {
		global $wp_query;

		if ( is_search() && ! $wp_query->is_handbook && 1 == $wp_query->found_posts ) {
			wp_redirect( get_permalink( get_post() ) );
			exit();
		}
	}

	/**
	 * Redirects a naked handbook request to home.
	 */
	public static function redirect_handbook() {
		if (
			// Naked /handbook/ request
			( 'handbook' == get_query_var( 'name' ) && ! get_query_var( 'post_type' ) )
		) {
			wp_redirect( home_url() );
			exit();
		}
	}

	/**
	 * Redirects a /blog request to the Developer blog at /news/.
	 */
	public static function redirect_blog() {
		$path = trailingslashit( $_SERVER['REQUEST_URI'] );

		if ( 0 === strpos( $path, '/blog' ) ) {
			wp_redirect( '/news/', 301 );
			exit();
		}
	}

	/**
	 * Redirects a naked /resource/ request to dashicons page.
	 *
	 * Temporary until a resource page other than dashicons is created.
	 */
	public static function redirect_resources() {
		if ( is_page( 'resource' ) ) {
			$args = array(
				'post_type' => 'page',
				'name' => 'dashicons',
				'posts_per_page' => 1,
			);
		
			$query = new WP_Query( $args );
		
			if ( $query->have_posts() ) {
				$post = $query->posts[0];
				wp_redirect( get_permalink( $post->ID ) );
				exit();
			}
		}
	}

	/**
	 * Redirects requests for the singularized form of handbook slugs to the
	 * pluralized version.
	 */
	public static function redirect_singularized_handbooks() {
		$path = trailingslashit( $_SERVER['REQUEST_URI'] );

		// '/plugin' => '/plugins'
		if ( 0 === strpos( $path, '/plugin/' ) ) {
			$path = get_post_type_archive_link( 'plugin-handbook' ) . substr( $path, 8 );
			wp_redirect( $path, 301 );
			exit();
		}

		// '/theme' => '/themes'
		if ( 0 === strpos( $path, '/theme/' ) ) {
			$path = get_post_type_archive_link( 'theme-handbook' ) . substr( $path, 7 );
			wp_redirect( $path, 301 );
			exit();
		}
	}

	/**
	 * Redirects requests for the pluralized slugs of the code reference parsed
	 * post types.
	 *
	 * Note: this is a convenience redirect and not a fix for any officially
	 * deployed links.
	 */
	public static function redirect_pluralized_reference_post_types() {
		$path = trailingslashit( $_SERVER['REQUEST_URI'] );

		$post_types = array(
			'class'    => 'classes',
			'function' => 'functions',
			'hook'     => 'hooks',
			'method'   => 'methods',
		);

		// '/reference/$singular(/*)?' => '/reference/$plural(/*)?'
		foreach ( $post_types as $post_type_slug_singular => $post_type_slug_plural ) {
			if ( 0 === stripos( $path, "/reference/{$post_type_slug_singular}/" ) ) {
				$path = "/reference/{$post_type_slug_plural}/" . substr( $path, strlen( "/reference/{$post_type_slug_singular}/" ) );
				wp_redirect( $path, 301 );
				exit();
			}
		}
	}

	/**
	 * Returns 404 response to requests for non-first pages of the front page.
	 */
	public static function paginated_home_page_404() {
		// Paginated front page.
		if ( is_front_page() && is_paged() ) {
			// Add the usual 404 page body class so that styles are applied.
			add_filter(
				'body_class',
				function( $classes ) {
					$classes[] = 'error404';

					return $classes;
				}
			);

			include( get_404_template() );
			exit;
		}
	}

	/**
	 * Loads the appropriate template for editing user notes.
	 */
	public static function user_note_edit() {
		$path = trailingslashit( $_SERVER['REQUEST_URI'] );

		if ( strpos( $path, '/reference/comment/edit' ) !== false ) {
			$comment_id = get_query_var( 'edit_user_note' );
			$can_user_edit = \DevHub\can_user_edit_note( $comment_id );

			if ( $can_user_edit ) {
				$template_slug = 'comment-edit';

				// This is returned by locate_block_template if no block template is found
				$fallback = locate_template( dirname( __FILE__ ) . "/templates/$template_slug.html" );

				// This internally sets the $template_slug to be the active template.
				$template = locate_block_template( $fallback, $template_slug, array() );

				if ( ! empty( $template ) ) {
					load_template( $template );
					exit;
				}
			} else {
				// TODO: improve this error handling
				wp_redirect( home_url() );
				exit();
			}
		}
	}

} // DevHub_Redirects

DevHub_Redirects::init();
