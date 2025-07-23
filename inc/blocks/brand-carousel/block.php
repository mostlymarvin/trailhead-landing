<?php
/**
 * Brand Carousel Block
 *
 * @package falls-co
 * @since 1.0.0
 *
 * @param array $block The block settings and attributes.
 */

/**
 * Dependencies
 *
 * - Helper functions for this Block.
 */
require_once FALLS_CO_PATH . '/inc/blocks/helper-functions.php';

/**
 * Attributes
 *
 * @var string   $anchor                 block anchor.
 * @var array    $data            block attributes
 *
 * @var integer  $add_logos
 * @var array    $add_brand_data
 * @var string   $background_color
 * @var string   $brand_name_position
 * @var string   $hide_name_unless
 * @var boolean  $hide_name_unless
 * @var array    $selected_brands
 * @var boolean  $show_brand_logo
 * @var boolean  $show_brand_name
 * @var string   $text_color
 */
$anchor     = $block['anchor'] ?? false;
$data       = $block['data'] ?? array();
$text_align = $block['alignText'] ?? 'left';

$background_color    = $block['backgroundColor'] ?? ( $block['style']['color']['background'] ?? false );
$brand_name_position = 'slick-title-' . sanitize_title( $data['brand_name_position'] ?? 'center' );
$hide_name_unless    = $data['hide_name_unless'] ?? false;
$link_to             = $data['link_brand_to'] ?? 'work';
$link_target         = $data['link_target'] ?? 'new_tab';
$selected_brands     = $data['select_brands'] ?? array();
$slides_to_show      = $data['slides_to_show'] ?? 8;
$autoplay            = $data['autoplay'] ?? false;
$show_brand_image    = $data['show_brand_image'] ?? 'logo';
$show_brand_name     = $data['show_brand_name'] ?? false;
$text_color          = $block['textColor'] ?? ( $block['style']['color']['text'] ?? false );
// echo falls_debug( $data );
/** Extra Brands */
$add_logos      = $data['additional_logos'] ?? 0;
$add_brand_data = array();
if ( ! empty( $add_logos ) ) {

	for ( $i = 0; $i < intval( $add_logos ); $i++ ) {

		// For each Logo: logo, alt_text, title, url
		$prefix = 'additional_logos_' . $i;

		$alt_text   = $data[ $prefix . '_alt_text' ] ?? '';
		$brand_name = $data[ $prefix . '_title' ] ?? '';
		$permalink  = $data[ $prefix . '_url' ] ?? '';
		$ext_url    = $permalink; // For custom brands added, any link shown will be the custom link entered here.

		$image_id = $data[ $prefix . '_logo' ] ?? '';
		$brand_id = get_the_ID() . '-add-' . $logo_id;

		$image_url = '';
		if ( ! empty( $image_id ) ) {
			$image_src = wp_get_attachment_image_src( intval( $image_id ), 'full' );
			$image_url = $image_src[0] ?? '';
		}

		$add_brand        = array(
			'alt_text'   => $alt_text,
			'brand_id'   => $brand_id,
			'brand_name' => $brand_name,
			'permalink'  => get_the_permalink( intval( $brand_id ) ),
			'ext_url'    => $ext_url,
			'image_url'  => $image_url,
		);
		$add_brand_data[] = $add_brand;
	}
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
 * Assemble output classes and modify block wrapper attributes.
 *
 * @var string $block_attributes
 */


$block_class = 'slick-carousel link-to-' . esc_attr( $link_to );
if ( ! empty( $autoplay ) ) {
	$block_class .= ' autoplay';
}
$block_attributes = get_block_wrapper_attributes( array( 'class' => $block_class ) );
$image_class      = 'carousel-image';
$image_class     .= ' carousel-image-' . esc_attr( $show_brand_image );
$title_class      = array( 'carousel-title-text' );
$title_style      = array();
$title_wrap_class = 'carousel-title ' . $brand_name_position;

// Title class
if ( 'none' !== $show_brand_image && $show_brand_name ) {

	// Title wrap class.
	if ( $hide_name_unless ) {
		$title_wrap_class .= ' only-hover';
	} else {
		$title_wrap_class .= ' always-vis';
	}

	if ( ! empty( $text_color ) ) {
		$title_class[] = 'has-text-color';

		if ( ! empty( $block['textColor'] ) ) {
			$title_class[] = 'has-' . esc_attr( $block['textColor'] ) . '-color';
		} else {
			$title_style[] = 'color: ' . sanitize_hex_color( $text_color ) . ';';
		}
	}
	// Background color classes
	if ( ! empty( $background_color ) ) {
		$title_class[] = 'has-background';

		if ( ! empty( $block['backgroundColor'] ) ) {
			$title_class[] = ' has-' . esc_attr( $block['backgroundColor'] ) . '-background-color';
		} else {
			$title_style[] = ' background-color: ' . sanitize_hex_color( $background_color ) . ';';
		}
	}
	// Strip the color and style tags that we are replicating to the title element.
	$block_attributes = str_replace( $title_class, ' ', $block_attributes );
	$block_attributes = str_replace( $title_style, ' ', $block_attributes );
}

// Add text-alignment to titles
$title_class[] = 'has-text-align-' . $text_align;
// if the title class is an array, flatten it.
if ( is_array( $title_class ) && ! empty( $title_class ) ) {
	$title_class = implode( ' ', $title_class );
}

$block_data = '';
if ( is_array( $selected_brands ) && ! empty( $selected_brands ) ) {
	// Make sure we have at least {8} slides. Add class if fewer.
	$slide_count = count( $selected_brands );

	// If we added additional brands, count them and add them to the total count.
	if ( ! empty( $add_brand_data ) && is_array( $add_brand_data ) ) {
		$slide_count += count( $add_brand_data );
	}
	// How many slides to shpw?
	if ( $slide_count <= intval( $slides_to_show ) ) {
		$block_data = $slide_count;
	}
}

/**
 * Get Dummy Data, if we are in the admin and there are no brands selected.
 *
 * @var mixed bool/array $items - individual page navigation items
 */
if ( is_admin() && ( empty( $selected_brands ) && empty( $add_brand_data ) ) ) {

	$args        = array(
		'post_type'      => 'work',
		'posts_per_page' => $slides_to_show,
		'post_status'    => 'publish',
		'fields'         => 'ids',
	);
	$brand_query = new WP_Query( $args );
	$brands      = $brand_query->posts ?? array();

	if ( $brands && is_array( $brands ) ) {
		$selected_brands = $brands;
	}
	wp_reset_postdata();
}

/**
 * Get data needed for selected brands and format the output.
 *
 * @var string $slides
 * @var array  $brand_data
 */
$slides     = '';
$brand_data = falls_get_carousel_data( $selected_brands, $show_brand_image );
$brand_data = array_merge( $brand_data, $add_brand_data );

if ( ! empty( $brand_data ) && is_array( $brand_data ) ) {

	foreach ( $brand_data as $brand ) {
		$add_class  = '';
		$alt_text   = $brand['alt_text'] ?? '';
		$brand_id   = $brand['brand_id'] ?? '';
		$brand_name = $brand['brand_name'] ?? '';
		$ext_url    = $brand['ext_url'] ?? '';
		$image_url  = $brand['image_url'] ?? '';
		$permalink  = $brand['permalink'] ?? '';

		/**
		 * Add link to approrpirate URL, based on link type:
		 * If in the admin, link to nothing.
		 *
		 * @var string $link_to
		 */
		switch ( $link_to ) {
			case 'none':
				$img_before = '<div class="img-wrap-no-link">';
				$img_after  = '</div>';
				break;
			case 'ext':
				$target = '';
				if ( 'new_tab' === $link_target ) {
					$target = 'target="_blank"';
				}
				// if ( ! empty( $ext_url ) ) {
				$img_before = sprintf(
					'<a class="carousel-link" href="%1$s" %2$s>',
					esc_url( $ext_url ),
					$target
				);
				$img_after  = '</a>';
				// }

				break;
			case 'work':
			default:
				$target = '';
				if ( 'new_tab' === $link_target ) {
					$target = 'target="_blank"';
				}
				// if ( ! empty( $permalink ) ) {
				$img_before = sprintf(
					'<a class="carousel-link" href="%1$s" %2$s>',
					esc_url( $permalink ),
					$target
				);
				$img_after  = '</a>';
				// }
				break;
		}

		/**
		 * Slide title
		 *
		 * @var string $slide_title
		 */
		$slide_title = '';
		// If we are showing the brand name:
		if ( ! empty( $show_brand_name ) ) {

			// If there is no logo ID and we are supposed to show a logo, add a class.
			if ( empty( $image ) && 'none' !== $show_brand_image ) {
				$add_class = ' empty-logo';
			}

			// Assemble the title
			$slide_title = sprintf(
				'<div class="%1$s"><span class="%2$s %3$s" style="%4$s"><div>%5$s</div></span></div>',
				esc_attr( $title_wrap_class ),
				esc_attr( $title_class ),
				esc_attr( $add_class ),
				implode( ' ', $title_style ),
				wp_kses_post( $brand_name )
			);
		}

		$post_type = get_post_type( get_the_ID() );
		// If this is a single work item and the brand id of this slide = the current page id, skip the slide.
		$skip_this_item = 'work' === $post_type && get_the_ID() === intval( $brand_id );
		if ( ! $skip_this_item ) {
			// Put it all together.
			$slides .= sprintf(
				'%6$s<img alt="%1$s" src="%2$s" id="brand-%3$s" title="%4$s" class="%5$s"/> %7$s %8$s',
				esc_attr( $alt_text ),
				esc_url( $image_url ),
				esc_attr( $brand_id ),
				esc_attr( $brand_name ),
				esc_attr( $image_class ),
				$img_before,
				$slide_title,
				$img_after
			);
		}
	}
} else {
	if ( is_admin() ) {
		$slides = '<h5 class="action-required">Please select Brands to display in this carousel.</h5>';
	} else {
		$slides = false;
	}
}

/**
 * Begin output
 *
 * @var string $output
 */
$output = '';

if ( $is_preview ) {

	$output .= sprintf(
		'<div id="%1$s" %2$s data-brands="%3$s" data-count="%4$s">',
		esc_attr( $anchor ),
		$block_attributes,
		$block_data,
		esc_attr( $slides_to_show )
	);
}

$output .= $slides;

if ( $is_preview ) {
	$output .= '</div>';
}

// Do not output empty slider on front end.
if ( ! empty( $slides ) ) {
	echo $output;
}


