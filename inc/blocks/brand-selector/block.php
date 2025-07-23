<?php
/**
 * Brand Selector Block
 *
 * @package falls-co
 * @since 1.0.0
 * 
 * @param array $block The block settings and attributes.
 */

// Block Data and settings:
$data = $block['data'] ?? array();
$select_brands = $data['select_brands'] ?? array();
$link_brand_to = is_admin() ? 'none' : ( $data['link_brand_to'] ?? 'work' );
$read_more_text = $data['read_more_text'] ?? 'Visit Story';
$align_text = $block['alignText'] ?? '';
$max_logo_height = $data['max_logo_height'] ?? 200;
$image_class = '';

// ID and class
$anchor = '';
if ( ! empty( $block['anchor'] ) ) {
    $anchor = $block['anchor'];
}

// Block Styles and Customization:
$block_attributes = get_block_wrapper_attributes( array( "class" => "brand-selector" ) );

$text_color = $block['textColor'] ?? ( $block['style']['color']['text'] ?? false );
$background_color = $block['backgroundColor'] ?? ( $block['style']['color']['background'] ?? false );


/**
 * Mode
 *
 * @var string  $mode          choice of: preview/edit/auto
 * @var bool    $is_preview    whether or not we are rendering the block preview.
 */
$mode       = is_admin() ? ( $block['mode'] ?? 'edit' ) : 'preview';
$is_preview = $mode === 'preview' ? true : false;

/**
 * Start assembling Output
 * 
 * @var $output
 */
$output = '';
$admin_message = false;

// If block preview, add block attributes to wrapper.
if ( $is_preview ) { 
  $output .= sprintf(
    '<div id="%1$s" %2$s>',
    esc_attr( $anchor ),
    $block_attributes
  );
} 

// Handle block display when no brands selected.
// Work Post Type: defaults to showing the logo for the current work item.
// Other post types: Admin message shown.
if ( empty( $select_brands ) ) {

  global $post;
  $post_type = get_post_type( $post->ID );
  $preview_type = $post_type;

  if ( is_admin() ) {
    $preview_type .= "-admin";
    $image_class .= ' admin-preview-default';
  }
  
  switch( $preview_type ) {

    case 'work':
    case 'work-admin':
        $select_brands = array( $post->ID );
        print_r( $brands );
      break;

    case 'page-admin':
    case 'post-admin':
      $select_brands = false;
      $admin_message = true;
      break;
    
    default:
        $select_brands = false;
    break;
  }
}

$slides = '';

if ( $select_brands && is_array( $select_brands ) ) {
foreach( $select_brands as $brand_id ) {
  /**
   * @var $logo_id
   * @var $brand_name
   * @var $permalink
   * @var $ext_link
   * @var $alt_text
   */

    $brand_id = intval( $brand_id );

    $logo_id = intval( get_post_meta( $brand_id, 'brand_logo', true ) );
    $logo = wp_get_attachment_image_url( $logo_id, 'medium');

    if ( empty( $logo ) ) {
      $logo = FALLS_CO_URL . '/assets/images/brand-no-logo.png';
    }

    $brand_name = get_the_title( $brand_id );
    $permalink = get_the_permalink( $brand_id );

    $ext_link = get_post_meta( $brand_id, 'brand_external_link', true );
    $alt_text = get_post_meta( $logo_id , '_wp_attachment_image_alt', true );

    $link = '';
    switch( $link_brand_to ) {
      case 'none':
        $link = '';
        break;
      case 'ext':
        $link = $ext_link;
        break;
      case 'work':
        default:
        $link = $permalink; 
        break;
    }

    // Format Link text, if provided and if we are linking.
    $brand_link = '';
    if ( ! empty( $read_more_text ) && 'none' !== $link_brand_to ) {
      $brand_link = sprintf(
        '<div class="brand-link">%1$s</div>',
        $read_more_text
      );
    }
   
    // Format the actual link, if exists.
    $img_before = '';
    $img_after = '';
    if ( ! empty( $link ) ) {
      $img_before = sprintf(
        '<a href="%1$s" target="_blank">',
        esc_url( $link )
      );
      $img_after = '</a>';
    } 

    // Add Max height to image.
    $image_style = 'style="max-height:' . esc_attr( $max_logo_height) . 'px;"';
    
    $slides .= sprintf(
      '<div class="brand-image has-text-align-%1$s">%2$s<img alt="%3$s" src="%4$s" title="%5$s" class="%6$s" %7$s/>%8$s %9$s</div>',
      esc_attr( $align_text ),
      $img_before,
      esc_attr( $alt_text ),
      esc_url( $logo ),
      esc_attr( $brand_name ),
      esc_attr( $image_class ),
      $image_style,
      $brand_link,
      $img_after
    );
  }
} else {
  if ( is_admin() && ! empty( $admin_message ) ) {
    $slides .= '<div class="brand-selector-admin-msg">Please select brands to display.</div>';
  }
}
  
$output .= $slides;

if ( $is_preview ) { 
  $output .= '</div>';
} 

echo wp_kses_post( $output );


