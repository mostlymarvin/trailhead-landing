<?php
/**
 * News Slider Block
 *
 * @package trailhead-landing
 * @since 1.0.0
 *
 * @param array $block The block settings and attributes.
 */

/**
 * Attributes
 *
 * @var string   $anchor           block anchor.
 * @var array    $block_class      block class
 * @var string.  $block_attributes block attributes
 * @var int      $slider_images    Number of slider images added
 *
 */
$anchor           = $block['anchor'] ?? false;
$block_class      = 'slick-news';
$block_attributes = get_block_wrapper_attributes( array( 'class' => $block_class ) );
$max_posts        = $block['data']['max_posts'] ?? 0;
$include_sticky   = isset( $block['data']['include_sticky'] ) ? 1 : 0;

$slide_data = '';

/**
 * Query
 */

$args = array(
  'post_type'           => 'post',
  'post_status'         => 'publish',
  'posts_per_page'      => intval( $max_posts ),
  'ignore_sticky_posts' => $include_sticky ? false : true,
  'fields'              => 'ids'
);

$news_query = new WP_Query( $args );
$articles      = $news_query->posts ?? array();

$slide_data = '';

if ( $articles && is_array( $articles ) ) {

  if ( count( $articles ) === 1 )  {
    $block_class .= ' hide-nav';
  }

  foreach( $articles as $article_id ) {
   
    $slide_class    = 'thl-news-slide text-only';
    $news_content   = apply_filters( 'the_content', get_the_content( '', true, $article_id ) );
    $news_title     = get_the_title( $article_id );
    $news_date      = get_the_date( '', $article_id );
    $news_image          = '';

    $limit = 200; // Word Limit
     // Post Thumbnail 
    if ( has_post_thumbnail( $article_id ) ) {

      $thumbnail    = get_the_post_thumbnail( $article_id, 'full' );
      $news_image   = '<div class="news-image">' . $thumbnail . '</div>'; // Wrap the image in a div to make it easier to style
      $slide_class = 'thl-news-slide has-image';

      $limit = 100;
    }

    //$news_content = wp_trim_words( $news_content, $limit, null );

    $slide_data .= sprintf(
        '<div class="%1$s" id="slide-%2$s"><h3 class="news-title">%3$s</h3><div class="news-date">%4$s</div><div class="news-main"><div class="news-content">%5$s</div>%6$s</div></div>',
        esc_attr( $slide_class ),
        esc_attr( $article_id ),
        wp_kses_post( $news_title ),
        wp_kses_post( $news_date ),
        wp_kses_post( $news_content ),
        wp_kses_post( $news_image )
      );
  }

  wp_reset_postdata();

} else {
  return false;
}

/**
 * Mode
 *
 * @var string  $mode          choice of: preview/edit/auto
 * @var bool    $is_preview    whether or not we are rendering the block preview.
 */
$mode       = is_admin() ? ( $block['mode'] ?? 'edit' ) : 'preview';
$is_preview = $mode === 'preview' ? true : false;


/**
 * Begin output
 *
 * @var string $output
 */
$output = '';

if ( $is_preview ) {
	$output .= sprintf(
		'<div id="%1$s" %2$s>',
		esc_attr( $anchor ),
		$block_attributes
	);
}

$output .= $slide_data;

if ( $is_preview ) {
	$output .= '</div>';
}

// Do not output empty slider on front end.
if ( ! empty( $slide_data ) ) {
	echo $output;
}
