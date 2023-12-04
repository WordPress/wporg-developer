<?php
/**
 * wporg-developer Theme Customizer
 *
 * @package wporg-developer
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function wporg_developer_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}
add_action( 'customize_register', 'wporg_developer_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function wporg_developer_customize_preview_js() {
	wp_enqueue_script(
		'wporg_developer_customizer',
		get_template_directory_uri() . '/js/customizer.js',
		array( 'customize-preview' ),
		filemtime( dirname( __DIR__ ) . '/js/customizer.js' ),
		true
	);
}
add_action( 'customize_preview_init', 'wporg_developer_customize_preview_js' );
