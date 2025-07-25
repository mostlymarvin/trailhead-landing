<?php
/**
 * Trailhead Landing Custom Blocks
 *
 * @link [fallsandco.com]
 * @since 1.0.0
 * @package Trailhead Landing
 * @subpackage trailhead-landing/blocks
 */

/**
 * Adds custom blocks
 *
 * @since      1.0.0
 * @package    Trailhead Landing
 * @subpackage trailhead-landing/blocks
 * @author Laird Sapir
 */

/**
 * We use WordPress's init hook to make sure
 * our blocks are registered early in the loading
 * process.
 *
 * @link https://developer.wordpress.org/reference/hooks/init/
 */
function trailhead_landing_register_acf_blocks() {

	$blocks = array(
		'hero-slider',
    'news-slider'
	);

	foreach ( $blocks as $block_slug ) {

		/**
		* We register our block's with WordPress's handy
		* register_block_type();
		*
		* @link https://developer.wordpress.org/reference/functions/register_block_type/
		*/
		register_block_type( __DIR__ . '/blocks/' . $block_slug );
	}
}

// Call our falls_register_acf_block() function on init.
add_action( 'init', 'trailhead_landing_register_acf_blocks' );
