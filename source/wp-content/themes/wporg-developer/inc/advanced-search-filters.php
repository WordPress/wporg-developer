<?php

class Advanced_Search_Filters {

	public static function init() {
		add_filter( 'the_posts', array( __CLASS__, 'modify_search' ), 10, 2 );
	}

	/**
	 * Modifies the query if user has used advanced search filters
	 *
	 * @param \WP_Query $query
	 * @return void
	 */
	public static function modify_query( $query ) {
		$post_types = [];
		$tax_queries = [];

		// Clean up keyword
		$keyword = trim( $query->get( 's' ) );

		// Split on spaces
		$groups = explode( " ", $keyword );

		// Loop through groups
		foreach( $groups as $group ){
			// Check if {qualifier}:{keyword} is used;
			$split = explode( ':', $group );

			// We have mo qualifier
			if( count( $split ) < 2 ) {
				continue;
			}
	
			$qualifier = strtolower( $split[ 0 ] );
		
			switch ( $qualifier ) {
				case 'type':
					// TO DO Validate the type
					$post_types[] = 'wp-parser-'. strtolower($split[1]);

					// Remove "type"	
					$keyword = str_replace( $qualifier . ':' , '', $keyword );
					break;
				case 'file':
					// Get the tax ids first
					$tax_ids = get_terms( array(
							'taxonomy' => 'wp-parser-source-file',
							'name__like' => $split[1],
							'fields' => 'ids'
						) );

					$tax_queries[] = array(
							'taxonomy' => 'wp-parser-source-file',
							'terms' => $tax_ids
					);
				
					// Remove everything from the query
					$keyword = str_replace( $group , '', $keyword );
					break;
				case 'version':
					$tax_queries[] = array(
							'taxonomy' => 'wp-parser-since',
							'field'    => 'name',
							'terms'    => $split[1],
					);

					// Remove everything from the query
					$keyword = str_replace( $group , '', $keyword );
					break;
			}
		}

		if( count( $tax_queries ) > 0 ) {
			$query->set( 'tax_query', $tax_queries );
		}

		// Add relevant post_types
		$query->set( 'post_type', array_merge( $query->get( 'post_type' ) ?: [], $post_types ) );

		// Reset the keyword
		$query->set( 's', $keyword );

		// If user has '()' at end of a search string, assume they want a specific function/method.
		$s = htmlentities( $query->get( 's' ) );
		if ( '()' === substr( $s, -2 ) ) {
			// Enable exact search.
			$query->set( 'exact',     true );
			// Modify the search query to omit the parentheses.
			$query->set( 's',         substr( $s, 0, -2 ) ); // remove '()'
			// Restrict search to function-like content.
			$query->set( 'post_type', array( 'wp-parser-function', 'wp-parser-method' ) );
		}
	}

	public function modify_search( $posts, $query ) {
		$query->set('s', $query->query["s"]);
		return $posts;
	}
}

Advanced_Search_Filters::init();