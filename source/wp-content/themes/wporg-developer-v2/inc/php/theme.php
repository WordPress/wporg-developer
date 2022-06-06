<?php

if ( ! function_exists( 'wporg_developer_v2_support' ) ) :

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
     *
	 * @return void
	 */
	function wporg_developer_v2_support() {
		// Add support for block styles.
		add_theme_support( 'wp-block-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style.css' );
	}

endif;

add_action( 'after_setup_theme', 'wporg_developer_v2_support' );

if ( ! function_exists( 'wporg_developer_v2_styles' ) ) :

	/**
	 * Enqueue styles.
	 *
	 * @return void
	 */
	function wporg_developer_v2_styles() {
		// Register theme stylesheet.
		$theme_version = wp_get_theme()->get( 'Version' );

		$version_string = is_string( $theme_version ) ? $theme_version : false;
		wp_register_style(
			'wporg-developer-v2-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$version_string
		);

		// Enqueue theme stylesheet.
		wp_enqueue_style( 'wporg-developer-v2-style' );

	}

endif;

add_action( 'wp_enqueue_scripts', 'wporg_developer_v2_styles' );
