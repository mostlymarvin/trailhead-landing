<?php
/**
 * Filters
 *
 * @package falls-co
 * @since 1.0.0
 */

/**
 * Show '(No title)' in admin if a post has no title.
 *
 * On single work pages, use the cover title set via ACF, if available.
 *
 * @since 1.0.0
 */
function falls_filter_title( $title, $post_id ) {

  $title = str_replace( array('<sup>®</sup>','®'),'<sup>®</sup>', $title );

	// If there is no title set in Admin:
	if ( ! is_admin() && empty( $title ) ) {
		$title = _x( '(No title)', 'Used if posts or pages has no title', 'falls-co' );
	}

	// Make sure we are not in the admin, and that we are on the single page or post for this post ID.
	if ( ! empty( $post_id ) && ! is_admin() && ( is_single( $post_id ) || is_page( $post_id ) ) ) {

		if ( ! is_admin() && is_page( $post_id ) || is_single( $post_id ) ) {

			$cover_title = get_post_meta( get_the_ID(), 'cover_title', true );
			$hide_title  = get_post_meta( $post_id, 'falls_hide_title', true );

			// If there is a cover title set, use that.
			if ( ! empty( $cover_title ) ) {
				$title = $cover_title;
			}

			// If the title is set to hidden, kill it.
			if ( 1 === intval( $hide_title ) ) {
				$title = '';
			}
		}
	}
	return $title;
}
add_filter( 'the_title', 'falls_filter_title', 99, 2 );


/**
 * Replace the default [...] excerpt more with an elipsis.
 *
 * @since 1.0.0
 */
function falls_more( $more ) {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'falls_more' );

// Add the page slug to the body classes
function falls_page_slug_to_body_class( $classes ) {
	if ( is_singular() ) {
		global $post;
		if ( isset( $post->post_name ) ) {
			$classes[] = $post->post_name;
		}
	}
	return $classes;
}
add_filter( 'body_class', 'falls_page_slug_to_body_class' );


// Disable automatic related posts - use shortccode instead.
add_filter( 'rp4wp_append_content', '__return_false' );

/**
 * Only show posts in same category as related posts.
 * 
 * @see https://www.relatedpostsforwp.com/documentation/only-link-posts-in-same-category/
 *
 * @param   [type]  $sql        [$sql description]
 * @param   [type]  $post_id    [$post_id description]
 * @param   [type]  $post_type  [$post_type description]
 *
 * @return  [type]              [return description]
 */
function rp4wp_force_same_category( $sql, $post_id, $post_type ) {
	global $wpdb;

	if ( 'post' !== $post_type ) {
		return $sql;
	}

	$sql_replace = "
	INNER JOIN " . $wpdb->term_relationships . " ON (R.`post_id` = " . $wpdb->term_relationships . ".object_id)
	INNER JOIN " . $wpdb->term_taxonomy . " ON (" . $wpdb->term_relationships . ".term_taxonomy_id = " . $wpdb->term_taxonomy . ".term_taxonomy_id)
	WHERE 1=1
	AND " . $wpdb->term_taxonomy . ".taxonomy = 'category'
	AND " . $wpdb->term_taxonomy . ".term_id IN ( SELECT TT.term_id FROM " . $wpdb->term_taxonomy . " TT INNER JOIN " . $wpdb->term_relationships . " TR ON TR.term_taxonomy_id = TT.term_taxonomy_id WHERE TR.object_id = " . $post_id . " )
	";

	return str_ireplace( 'WHERE 1=1', $sql_replace, $sql );
}

add_filter( 'rp4wp_get_related_posts_sql', 'rp4wp_force_same_category', 11, 3 );


/**
 * Enable ACF Shortcode.
 *
 * @return  null
 */
function set_acf_settings() {

  if ( function_exists( 'acf_update_setting' ) ) {
	  acf_update_setting( 'enable_shortcode', true );
  }
}
add_action( 'acf/init', 'set_acf_settings' );