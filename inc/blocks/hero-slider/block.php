<?php
/**
 * Hero Slider Block
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
$block_class      = 'slick-hero';
$block_attributes = get_block_wrapper_attributes( array( 'class' => $block_class ) );
$slides_no        = $block['data']['slider_images'] ?? 0;

$slide_data = '';

if ( ! empty( $slides_no ) ) {

  for( $i = 0; $i <= $slides_no; $i++ ) {

    $image_id = $block['data']['slider_images_' . $i . '_image'] ?? 0;
    $image_alt = $block['data']['slider_images_' . $i . '_alt_text'] ?? '';

    if ( ! empty( $image_id ) ) {
      $image_src = wp_get_attachment_image_src( intval( $image_id ), 'full' );
			$image_url = $image_src[0] ?? '';

      $slide_data .= sprintf(
        '<div class="thl-hero-slide" id="slide-%1$s"><img src="%2$s" alt="%3$s" /></div>',
        esc_attr( $image_id ),
        esc_url( $image_url ),
        esc_attr( $image_alt )
      );
    }
  }
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
