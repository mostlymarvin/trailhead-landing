<?php
/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package trailhead-landing
 * @since 1.0.0
 */


/**
 * The theme version.
 *
 * @since 1.0.0
 */
define( 'TRAILHEAD_LANDING_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'TRAILHEAD_LANDING_URL', get_template_directory_uri() );
define( 'TRAILHEAD_LANDING_PATH', get_theme_file_path() );


/**
 * Add theme support for block styles and editor style.
 *
 * @since 1.0.0
 *
 * @return void
 */
function trailhead_landing_setup() {
	add_editor_style( 'admin-style.css' );

	/*
	 * Load additional block styles.
	 * See details on how to add more styles in the readme.txt.
	 */
	$styled_blocks = array(
		'navigation',
	);
	foreach ( $styled_blocks as $block_name ) {
		$args = array(
			'handle' => "trailhead-landing-$block_name",
			'src'    => get_theme_file_uri( "assets/css/blocks/$block_name.min.css" ),
			'path'   => get_theme_file_path( "assets/css/blocks/$block_name.min.css" ),
		);
		// Replace the "core" prefix if you are styling blocks from plugins.
		wp_enqueue_block_style( "core/$block_name", $args );
	}

}
add_action( 'after_setup_theme', 'trailhead_landing_setup' );

/**
 * Enqueue the CSS files.
 *
 * @since 1.0.0
 *
 * @return void
 */
function trailhead_landing_styles() {

	wp_enqueue_style(
		'trailhead-landing-style',
		get_template_directory_uri() . '/style.css',
		array(),
		TRAILHEAD_LANDING_VERSION
	);
}

function trailhead_landing_editor_styles() {
	wp_enqueue_style(
		'falls-co-admin-styles',
		get_template_directory_uri() . '/admin-style.css',
		array(),
		TRAILHEAD_LANDING_VERSION
	);
}

add_action( 'admin_enqueue_scripts', 'trailhead_landing_editor_styles' );
add_action( 'wp_enqueue_scripts', 'trailhead_landing_styles' );


// Blocks
require_once get_theme_file_path( 'inc/register-blocks.php' );
