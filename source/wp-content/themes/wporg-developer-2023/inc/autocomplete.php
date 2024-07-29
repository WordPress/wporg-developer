<?php
/**
 * Code Reference autocomplete for the search form.
 *
 * @package wporg-developer
 */

/**
 * Class to handle autocomplete for the search form.
 */
class DevHub_Search_Form_Autocomplete {

	public function __construct() {
		$this->init();
	}

	/**
	 * Initialization
	 *
	 * @access public
	 */
	public function init() {
		add_action(
			'rest_api_init',
			function () {
				register_rest_route(
					'wporg-developer/v1',
					'/autocomplete',
					array(
						'methods'             => 'POST',
						'callback'            => array( $this, 'autocomplete_rest_handler' ),
						'permission_callback' => '__return_true',
						'schema' => function () {
							return array(
								'html' => array(
									'description' => 'The HTML to process.',
									'type' => 'string',
									'required' => true,
								),
								'quirksMode' => array(
									'description' => 'Process the document in quirks mode.',
									'type' => 'boolean',
								),
							);
						},
					)
				);
			}
		);

		// Enqueue scripts and styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts_and_styles' ), 11 );
	}

	/**
	 * Enqueues scripts and styles.
	 *
	 * @access public
	 */
	public function scripts_and_styles() {

		// Handbook searches don't have autocomplete.
		/*if ( function_exists( 'wporg_is_handbook' ) && wporg_is_handbook() ) {*/
		/*	return;*/
		/*}*/
		/**/
		/*wp_enqueue_style(*/
		/*	'awesomplete-css',*/
		/*	get_stylesheet_directory_uri() . '/stylesheets/awesomplete.css',*/
		/*	array(),*/
		/*	filemtime( dirname( __DIR__ ) . '/stylesheets/awesomplete.css' )*/
		/*);*/
		/*wp_enqueue_style(*/
		/*	'autocomplete-css',*/
		/*	get_stylesheet_directory_uri() . '/stylesheets/autocomplete.css',*/
		/*	array(),*/
		/*	filemtime( dirname( __DIR__ ) . '/stylesheets/autocomplete.css' )*/
		/*);*/
		/**/

		wp_enqueue_script(
			'awesomplete',
			get_stylesheet_directory_uri() . '/js/awesomplete.min.js',
			array(),
			filemtime( dirname( __DIR__ ) . '/js/awesomplete.min.js' ),
			true
		);
		wp_enqueue_script_module(
			'@wporg-developer/autocomplete',
			get_stylesheet_directory_uri() . '/js/autocomplete.js',
			array(),
			filemtime( get_stylesheet_directory() . '/js/autocomplete.js' ),
		);

		/*wp_register_script(*/
		/*	'autocomplete',*/
		/*	get_stylesheet_directory_uri() . '/js/autocomplete.js',*/
		/*	array( 'awesomplete' ),*/
		/*	filemtime( dirname( __DIR__ ) . '/js/autocomplete.js' ),*/
		/*	true*/
		/*);*/
		/*wp_localize_script(*/
		/*	'autocomplete',*/
		/*	'autocomplete',*/
		/*	array(*/
		/*		'ajaxurl'   => admin_url( 'admin-ajax.php' ),*/
		/*		'nonce'     => wp_create_nonce( 'autocomplete_nonce' ),*/
		/*		'post_type' => get_post_type(),*/
		/*	)*/
		/*);*/

		/*wp_enqueue_script( 'autocomplete' );*/

		add_filter(
			"script_module_data_@wporg-developer/autocomplete",
			function ( $data ) {
				return array_merge( $data, array(
					'rest_url' => rest_url('wporg-developer/v1/autocomplete'),
					'nonce'       => wp_create_nonce( 'wporg/autocomplete' ),
					'post_type'   => get_post_type(),
				) );
			}
		);
	}

	public function autocomplete_rest_handler( WP_REST_Request $request ) {
		$req_json = $request->get_json_params();

		$nonce_ok = wp_verify_nonce( $req_json['nonce'], 'wporg/autocomplete' );
		if ( ! $nonce_ok ) {
			return new WP_REST_Response( array( 'error' => __( 'Invalid nonce' ) ), 403 );
		}

		// No search query.
		$req_search = $req_json['s'] ?? '';
		if ( '' === $req_search ) {
			return new WP_REST_Response(
				array( 'error' => __( 'Missing or invalid "s" search.', 'wporg' ) ),
				400
			);
		}

		$parser_post_types = DevHub\get_parsed_post_types();

		$req_post_types = $req_json['post_types'];
		if ( ! is_array( $req_post_types ) ) {
			$req_post_types = array();
		} else {
			$req_post_types = array_intersect( $parser_post_types, $req_post_types );
		}

		if ( array() === $req_post_types ) {
			$req_post_types = $parser_post_types;
		}

		$args = array(
			'posts_per_page'       => -1,
			'post_type'            => $req_post_types,
			's'                    => $req_search,
			'orderby'              => '',
			'search_orderby_title' => 1,
			'order'                => 'ASC',
			'_autocomplete_search' => true,
		);

		$search_result = get_posts( $args );

		if ( empty( $search_result ) ) {
			return new WP_REST_Response( null, 200 );
		} else {
			$post_types_function_like = array( 'wp-parser-function', 'wp-parser-method' );

			foreach ( $search_result as $post ) {
				$permalink = get_permalink( $post->ID );
				$title     = $post->post_title;

				if ( in_array( $post->post_type, $post_types_function_like ) ) {
					$title .= '()';
				}

				if ( $post->post_type == 'wp-parser-class' ) {
					$title = 'class ' . $title . ' {}';
				}

				$request_data['posts'][ $title ] = $permalink;
			}
		}

		return new WP_REST_Response( $request_data, 200 );
	}
}

$autocomplete = new DevHub_Search_Form_Autocomplete();
