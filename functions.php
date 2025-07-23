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
	add_editor_style( './assets/css/style-shared.min.css' );
	add_editor_style( 'admin-style.css' );

	/*
	 * Load additional block styles.
	 * See details on how to add more styles in the readme.txt.
	 */
	$styled_blocks = array(
		'button',
		'cover',
		'details',
		'image',
		'list',
		'navigation',
		'post-content',
		'quote',
		'search',
		'table',
		'group',
		'post-author',
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
	wp_enqueue_style(
		'trailhead-landing-shared-styles',
		get_template_directory_uri() . '/assets/css/style-shared.min.css',
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

function trailhead_landing_scripts() {
	wp_enqueue_script(
		'slick',
		get_template_directory_uri() . '/assets/js/dist/slick.min.js',
		array( 'jquery' ),
		TRAILHEAD_LANDING_VERSION,
		true
	);
	wp_enqueue_script(
		'slick-falls',
		get_template_directory_uri() . '/assets/js/dist/slick-slider.min.js',
		array( 'jquery', 'slick' ),
		TRAILHEAD_LANDING_VERSION,
		true
	);
	wp_enqueue_script(
		'site-falls',
		get_template_directory_uri() . '/assets/js/dist/site.min.js',
		array(),
		TRAILHEAD_LANDING_VERSION,
		true
	);

	// Register script for blog navigation - will be enqueued in shortcode.
	wp_register_script(
		'blog-navigation',
		get_stylesheet_directory_uri() . '/assets/js/dist/blog-nav.min.js',
		array( 'jquery' ),
		null,
		true
	);
}

add_action( 'admin_enqueue_scripts', 'trailhead_landing_editor_styles' );
add_action( 'admin_enqueue_scripts', 'trailhead_landing_scripts' );
add_action( 'wp_enqueue_scripts', 'trailhead_landing_styles' );
add_action( 'wp_enqueue_scripts', 'trailhead_landing_scripts' );

// Shortcodes
// require_once get_theme_file_path( 'inc/shortcodes.php' );

// Blocks
// require_once get_theme_file_path( 'inc/register-blocks.php' );

// Filters.
// require_once get_theme_file_path( 'inc/filters.php' );
