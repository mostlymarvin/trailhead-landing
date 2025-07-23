<?php
/**
 * Shortcodes
 *
 * @package falls-co
 * @since 1.0.0
 */

// Auto-update the copyright year in footer

/**
 * [falls_year_shortcode description]
 *
 * @return  [type]  [return description]
 */
function falls_year_shortcode() {
	$year = date( 'Y' );
	return $year;
}

add_shortcode( 'year', 'falls_year_shortcode' );

/**
 * Blog Search Bar Shortcode [blog_search]
 * Adds Category select and keyword search.
 *
 * @param   array  $atts     array of shortcode attributes (optional)
 * @param   string $content  shortcode content (optional)
 *
 * @return  string           shortcode output
 */
function falls_blog_search( $atts, $content ) {

	wp_enqueue_script( 'blog-navigation' );

	$categories = get_categories(
		array(
			'orderby' => 'name',
			'order'   => 'ASC',
			'parent'  => 0,
			'exclude' => array( 1 ),
		)
	);

	$options = '';
	foreach ( $categories as $category ) {
		$options .= '<option value="' . $category->slug . '">' . $category->name . '</option>';
	}

	$output = sprintf(
		'<div class="blog-cat-nav"><div class="nav-container">
        <select id="blog-categories"><option value="0">Select Category</option>%1$s</select><form role="search" method="POST" action="%2$s" id="blog-search"><input type="search" name="s" value="%3$s" placeholder="Keyword Search"><button type="submit" form="blog-search">Submit</button></form>
      </div>        
    </div>',
		$options,
		esc_url( home_url( '/' ) ),
		esc_attr( get_search_query() )
	);

	return $output;
}
add_shortcode( 'blog_search', 'falls_blog_search' );


/**
 * Convert related posts to shortcode - disable in settings, add to template via shortcode.
 *
 * @return  [type]  [return description]
 */
function falls_related_posts() {
  $output = '';

  if ( function_exists( 'rp4wp_children' ) ) {
    ob_start();
    rp4wp_children();
    $output = ob_get_contents();
    ob_end_clean();
  }
 
  return $output;
}
add_shortcode( 'falls_related_posts', 'falls_related_posts' );
