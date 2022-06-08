<?php

class Advanced_Search_Filters {

	public static function init() {
		add_filter( 'the_posts', array( __CLASS__, 'modify_search' ), 10, 2 );
	}

	/**
	 * Returns a tax query array for appending wp-parse-source-file taxonomy searches
	 *
	 * @param string $name
	 * @return array
	 */
	public static function get_file_tax_query( $name ) {
		// Get the tax IDs first
		$tax_ids = get_terms(
			array(
				'taxonomy'   => 'wp-parser-source-file',
				'name__like' => $name,
				'fields'     => 'ids',
			)
		);

		return array(
			'taxonomy' => 'wp-parser-source-file',
			'terms'    => $tax_ids,
		);
	}

	/**
	 * Returns a tax query array for appending wp-parser-since taxonomy searches
	 *
	 * @param string $term
	 * @return array
	 */
	public static function get_version_tax_query( $term ) {
		return array(
			'taxonomy' => 'wp-parser-since',
			'field'    => 'name',
			'terms'    => $term,
		);
	}

	/**
	 * Returns a post type string. wp-parser-{post_type}
	 *
	 * @param string $post_type_partial
	 * @return string
	 */
	public static function get_post_type_string( $post_type_partial ) {
		return 'wp-parser-' . strtolower( $post_type_partial );
	}

	/**
	 * Modifies the query if user has used advanced search filters
	 *
	 * @param \WP_Query $query
	 * @return void
	 */
	public static function modify_query( $query ) {
		$post_types  = array();
		$tax_queries = array();

		// Clean up keyword.
		$keyword = trim( $query->get( 's' ) );

		// Split on spaces.
		$groups = explode( ' ', $keyword );

		// Loop through groups
		foreach ( $groups as $group ) {
			// Check if {qualifier}:{keyword} is used.
			$split = explode( ':', $group );

			// We have no qualifier.
			if ( ! isset( $split[1] ) ||
				empty( $split[0] ) || // Missing a qualifier. Ie: :init
				$split[1][0] == ':'  // Searching for a class method. Ie: {Class}::init()
			) {

				// If user has '()' at end of a search string, assume they want a specific function/method.
				$s = htmlentities( $split[0] );
				if ( str_contains( $s, '()' ) ) {
					// Modify the search query to omit the parentheses.
					$keyword = str_replace( '()', '', $keyword );

					// Restrict search to function-like content.
					$post_types[] = 'wp-parser-function';
					$post_types[] = 'wp-parser-method';
				}

				continue;
			}

			switch ( strtolower( $split[0] ) ) {
				case 'type':
					$type = self::get_post_type_string( $split[1] );

					if ( in_array( $type, DevHub\get_parsed_post_types() ) ) {
						$post_types[] = $type;

						// Remove "type"
						$keyword = str_replace( $split[0] . ':', '', $keyword );
					}

					break;
				case 'file':
					$tax_queries[] = self::get_file_tax_query( $split[1] );

					// Remove everything from the query.
					$keyword = str_replace( $group, '', $keyword );
					break;
				case 'version':
					$tax_queries[] = self::get_version_tax_query( $split[1] );

					// Remove everything from the query.
					$keyword = str_replace( $group, '', $keyword );
					break;
			}
		}

		// Add relevant tax queries
		$query->set( 'tax_query', array_merge( $query->get( 'tax_query' ) ?: array(), $tax_queries ) );

		// Add relevant post_types
		$query->set( 'post_type', array_merge( $query->get( 'post_type' ) ?: array(), $post_types ) );

		// Reset the keyword
		$query->set( 's', $keyword );
	}

	/**
	 * Reset the keyword back to the original keyword after we retrieve the posts.
	 * We need to because we removed parts in modify_query().
	 *
	 * @param WP_Post[] $posts
	 * @param WP_Query  $query
	 * @return WP_Post[]
	 */
	public static function modify_search( $posts, $query ) {
		// Reset the keyword so we don't lose the qualifiers
		$query->set( 's', $query->query['s'] );
		return $posts;
	}
}

Advanced_Search_Filters::init();
