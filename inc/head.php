<?php
/**
 * HTML head markup and customizations.
 *
 * @package wporg-developer
 */

/**
 * Class to handle HTML head markup.
 */
class DevHub_Head {

	/**
	 * Initializes module.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'do_init' ) );
	}

	/**
	 * Handles adding/removing hooks as needed.
	 */
	public static function do_init() {
		add_filter( 'document_title_parts', array( __CLASS__, 'document_title' ) );
		add_filter( 'document_title_separator', array( __CLASS__, 'document_title_separator' ) );
		add_action( 'wp_head', array( __CLASS__, 'output_head_tags' ), 2 );
	}

	/**
	 * Filters document title to add context based on what is being viewed.
	 *
	 * @param array $parts The document title parts.
	 * @return array The document title parts.
	 */
	public static function document_title( $parts ) {
		global $wp_query;

		$parts['site']  = __( 'Developer.WordPress.org', 'wporg' );
		$post_type      = get_query_var( 'post_type' );
		$sep            = '–';

		if ( is_front_page() || is_feed() ) {
			$parts['title'] = 'WordPress Developer Resources';
			return $parts;
		}

		if ( is_singular() && ( \DevHub\is_parsed_post_type( $post_type ) ) ) {
			// Add post type to title if it's a parsed item.
			if ( get_post_type_object( $post_type ) ) {
				$parts['title'] .= " $sep " . get_post_type_object( $post_type )->labels->singular_name;
			}
		} elseif ( ( is_singular() || is_post_type_archive() ) && false !== strpos( $post_type, 'handbook' ) ) {
			// Add handbook name to title if relevant.
			if ( get_post_type_object( $post_type ) ) {
				$handbook_label = get_post_type_object( $post_type )->labels->name;

				// Replace title with handbook_label(CPT name) if they have too many repeated words. Otherwise, append the handbook_label.
				if ( strpos( $parts['title'], $handbook_label ) !== false ) {
					$parts['title'] = $handbook_label;
				} else {
					$parts['title'] .= " $sep " . $handbook_label;
				}
			}
		} elseif ( is_singular( 'command' ) ) {
			// Add "WP-CLI Command" to individual CLI command pages.
			$parts['title'] .= " $sep WP-CLI Command";
		}

		// If results are paged and the max number of pages is known.
		if ( is_paged() && $wp_query->max_num_pages ) {
			$parts['page'] = sprintf(
				// translators: 1: current page number, 2: total number of pages
				__( 'Page %1$s of %2$s', 'wporg' ),
				get_query_var( 'paged' ),
				$wp_query->max_num_pages
			);
		}

		return $parts;
	}

	/**
	 * Customizes the document title separator.
	 *
	 * @param string $separator Current document title separator.
	 * @return string
	 */
	public static function document_title_separator( $separator ) {
		return '|';
	}

	/**
	 * Outputs tags for the page head.
	 */
	public static function output_head_tags() {
		$fields = [
			// FYI: 'description' and 'og:description' are set further down.
			'og:title'       => wp_get_document_title(),
			'og:site_name'   => get_bloginfo( 'name' ),
			'og:type'        => 'website',
			'og:url'         => home_url( '/' ),
			'og:image'       => get_theme_file_uri( 'images/opengraph-image.png' ),
			'twitter:card'   => 'summary_large_image',
			'twitter:site'   => '@WordPress',
			'twitter:image'  => get_theme_file_uri( 'images/opengraph-image.png' ),
		];

		$desc = '';

		// Customize description and any other tags.
		if ( is_front_page() ) {
			$desc = __( 'Official WordPress developer resources including a code reference, handbooks (for APIs, plugin and theme development, block editor), and more.', 'wporg' );
		} elseif ( is_page( 'reference' ) ) {
			$desc = __( 'Want to know what&#8217;s going on inside WordPress? Find out more information about its functions, classes, methods, and hooks.', 'wporg' );
		} elseif ( DevHub\is_parsed_post_type() ) {
			if ( is_singular() ) {
				$desc = DevHub\get_summary();
			} elseif ( is_post_type_archive() ) {
				$post_type_items = get_post_type_object( get_post_type() )->labels->all_items;
				/* translators: %s: translated label for all items of a post type. */
				$desc = sprintf( __( 'Code Reference archive for WordPress %s.', 'wporg' ), strtolower( $post_type_items ) );
			} elseif ( is_archive() ) {
				$desc = get_the_archive_title();
			}
		} elseif ( function_exists( 'wporg_is_handbook' ) && wporg_is_handbook() ) {
			// Exclude search pages, otherwise the blurb is the first search result.
			if ( ! is_search() ) {
				$desc = get_the_excerpt();
			}
		} elseif ( is_archive( 'command' ) && ! is_search() ) {
			// CLI handbook homepage.
			$desc = __( 'Documentation for all currently available WP-CLI commands, including usage and subcommands.', 'wporg' );
		} elseif ( is_singular( 'command' ) ) {
			// Individual command pages.
			$desc = get_the_excerpt();
		} elseif ( is_singular() ) {
			$post = get_queried_object();
			if ( $post ) {
				$desc = $post->post_content;
			}
		}

		// Actually set field values for description.
		if ( $desc ) {
			$desc = wp_strip_all_tags( $desc );
			$desc = str_replace( '&nbsp;', ' ', $desc );
			$desc = preg_replace( '/\s+/', ' ', $desc );

			// Trim down to <150 characters based on full words.
			if ( strlen( $desc ) > 150 ) {
				$truncated = '';
				$words = preg_split( "/[\n\r\t ]+/", $desc, -1, PREG_SPLIT_NO_EMPTY );

				while ( $words ) {
					$word = array_shift( $words );
					if ( strlen( $truncated ) + strlen( $word ) >= 141 ) { /* 150 - strlen( ' &hellip;' ) */
						break;
					}

					$truncated .= $word . ' ';
				}

				$truncated = trim( $truncated );

				if ( $words ) {
					$truncated .= '&hellip;';
				}

				$desc = $truncated;
			}

			$fields['description'] = $desc;
			$fields['og:description'] = $desc;
		}

		// Output fields.
		foreach ( $fields as $property => $content ) {
			$attribute = 0 === strpos( $property, 'og:' ) ? 'property' : 'name';
			printf(
				'<meta %s="%s" content="%s" />' . "\n",
				esc_attr( $attribute ),
				esc_attr( $property ),
				esc_attr( $content )
			);
		}
	}

} // DevHub_Head

DevHub_Head::init();

