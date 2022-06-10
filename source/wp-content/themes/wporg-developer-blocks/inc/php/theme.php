<?php

function wporg_developer_blocks_support() {
	// Add support for block styles.
	add_theme_support( 'wp-block-styles' );

	// Enqueue editor styles.
	add_editor_style( 'style.css' );

	// Apply syntax highlighter custom theme.
	add_filter( 'syntaxhighlighter_cssthemeurl', function() {
		return get_template_directory_uri() . '/inc/css/syntax-highlighter-theme.css';
	} );
}
add_action( 'after_setup_theme', 'wporg_developer_blocks_support' );

function wporg_developer_blocks_styles() {
	// Register theme stylesheet.
	$theme_version = wp_get_theme()->get( 'Version' );

	$version_string = is_string( $theme_version ) ? $theme_version : false;
	wp_register_style(
		'wporg-developer-blocks-style',
		get_template_directory_uri() . '/style.css',
		array(),
		$version_string
	);

	// Enqueue theme stylesheet.
	wp_enqueue_style( 'wporg-developer-blocks-style', '', [ 'dashicons'] );
	wp_enqueue_script( 'wporg-developer-blocks-script', get_template_directory_uri() . '/inc/helper.js' );
}
add_action( 'wp_enqueue_scripts', 'wporg_developer_blocks_styles' );
