<?php
/**
 * @author Nacin
 */
class WPorg_Handbook_Glossary {
	const capability_type = 'handbook_page';

	static function init() {
		// Disable glossary functionality for multi-handbook sites until
		// such support is added.
		if ( count( WPorg_Handbook_Init::get_post_types() ) > 1 ) {
			return;
		}

		add_action( 'init', array( __CLASS__, 'init_hook' ) );
		add_action( 'load-edit.php', array( __CLASS__, 'load_edit_php' ) );
		add_filter( 'edit_glossary_per_page', array( __CLASS__, 'edit_posts_per_page' ) );
		add_filter( 'post_type_link', array( __CLASS__, 'post_type_link' ), 10, 2 );
		add_action( 'wporg_email_changes_for_post_types', array( __CLASS__, 'wporg_email_changes_for_post_types' ) );
		add_action( 'wporg_handbook_glossary', array( __CLASS__, 'page_content' ) );
		add_action( 'manage_glossary_posts_columns', array( __CLASS__, 'manage_glossary_posts_columns' ) );
	}

	static function edit_posts_per_page() {
		$per_page = get_user_option( 'edit_glossary_per_page' );
		if ( ! $per_page || $per_page < 1 )
			$per_page = 100;
		return $per_page;
	}

	static function init_hook() {
		add_shortcode( 'glossary', array( __CLASS__, 'shortcode' ) );

		register_post_type( 'glossary', array(
			'labels' => array(
				'name' => _x( 'Glossary', 'glossary', 'wporg' ),
				'singular_name' => _x( 'Entry', 'glossary', 'wporg' ),
				'add_new' => _x( 'Add New', 'glossary', 'wporg' ),
				'add_new_item' => _x( 'Add New Entry', 'glossary', 'wporg' ),
				'edit_item' => _x( 'Edit Entry', 'glossary', 'wporg' ),
				'new_item' => _x( 'New Entry', 'glossary', 'wporg' ),
				'view_item' => _x( 'View Entry', 'glossary', 'wporg' ),
				'search_items' => _x( 'Search Glossary', 'glossary','wporg' ),
				'not_found' => _x( 'No entries found', 'glossary', 'wporg' ),
				'not_found_in_trash' => _x( 'No entries found in Trash', 'glossary', 'wporg' ),
				'parent_item_colon' => _x( 'Parent Entry:', 'glossary', 'wporg' ),
				'menu_name' => _x( 'Glossary', 'glossary', 'wporg' ),
				'name_admin_bar' => _x( 'Glossary Entry', 'glossary', 'wporg' ),
			),
			'public' => true,
			'show_ui' => true,
			'hierarchical' => false,
			# 'has_archive' => true,
			'rewrite' => array( 'slug' => 'handbook/glossary' ),
			'supports' => array( 'title', 'editor', 'revisions' ),
			'capability_type' => self::capability_type,
			'map_meta_cap' => true,
			'menu_position' => 12,
		) );
	}

	static function load_edit_php() {
		if ( get_current_screen()->post_type !== 'glossary' )
		   return;
		if ( ! isset( $_GET['mode'] ) )
			$_GET['mode'] = $_REQUEST['mode'] = 'excerpt';
		if ( ! isset( $_GET['orderby'] ) && ! isset( $_GET['order'] ) ) {
			$_GET['order']   = $_REQUEST['order']   = 'ASC';
			$_GET['orderby'] = $_REQUEST['orderby'] = 'title';
		}
	}

	static function manage_glossary_posts_columns( $columns ) {
		$columns['author'] = __( 'Created by', 'wporg' );
		return $columns;
	}

	static function post_type_link( $link, $post ) {
		if ( $post->post_type !== 'glossary' )
			return $link;
		return untrailingslashit( str_replace( '/glossary/', '/glossary/#', $link ) );
	}

	static function wporg_email_changes_for_post_types( $post_types ) {
		if ( ! in_array( 'glossary', $post_types ) )
			$post_types[] = 'glossary';
		return $post_types;
	}

	static function shortcode() {
		$glossary = new WP_Query( array( 'post_status' => 'publish', 'post_type' => 'glossary', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );

		$content = "<style>dl.glossary-list dd { margin-top: -50px; padding-top: 50px; }</style>\n";
		$content .= '<dl class="glossary-list">';
		foreach ( $glossary->posts as $post ) {
			$entry = '<dt id="' . esc_attr( $post->post_name ) . '">' . apply_filters( 'the_title', $post->post_title ) . '</dt>';
			$entry .= '<dd>' . $post->post_content;

			$edit = get_edit_post_link( $post );
			if ( $edit ) {
				$edit = ' - <a href="' . esc_url( $edit ) . '">' . esc_html__( 'Edit', 'wporg' ) . '</a>';
			}
			$entry .= ' <a href="' . esc_url( get_permalink( $post ) ) . '">#</a>' . $edit . '</dd>';

			$entry = apply_filters( 'the_content', $entry );

			$content .= $entry . "\n";
		}
		$content .= '</dl>';

		if ( current_user_can( get_post_type_object('glossary')->cap->edit_posts ) )
			$content .= '<p><a href="' . admin_url( 'post-new.php?post_type=glossary' ) . '">' . esc_html__( 'Add new entry', 'wporg' ) . '</a>';

		return $content;
	}

	static function page_content() {
		echo self::shortcode();
	}
}
