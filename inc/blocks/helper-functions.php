<?php 
/**
 * Helper Functions
 *
 * @package falls-co
 * @since 1.0.0
 */

function falls_get_page_navigation_data( $item_ids = array(), $current_page_id, $use_tax = false ) {

  $data = array(); // item_id, item_url, image_url, item_title

  if ( ! empty( $item_ids ) && is_array( $item_ids ) )  {

    foreach( $item_ids as $item_id ) {

      $is_current_item = false;
      // Is this the active page?
      if ( $current_page_id === $item_id ) {
        $is_current_item = true;
      }


      // If type is subpage or selectpage, we are not using a tax. Get the page information.
      if ( empty( $use_tax ) ) {

        $item_title = get_the_title( $item_id );
        $item_uri = get_the_permalink( $item_id );

        // Image URL
        if ( has_post_thumbnail( $item_id ) ) {
            $featured_img = get_post_thumbnail_id( $item_id );
            $featured_img_src = wp_get_attachment_image_src( $featured_img, array( '768', '511' ) );
            $featured_img_uri = $featured_img_src[0];
        } else {
            // If no featured image is set, load the default:
            $featured_img_uri = FALLS_CO_URL . '/assets/images/placeholder.jpeg';
        }

        $item = array(
          'item_id' => $item_id,
          'item_title' => $item_title, 
          'item_url' => $item_uri,
          'item_img' => $featured_img_uri
        );

        $data[] = $item;
      } else {
        // We are using a taxonomy, so get the data from the term IDS
        $term = get_term_by( 'id', intval( $item_id ), $use_tax );

        if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
    
          $term_link = get_term_link( intval( $item_id ), $use_tax );
          $img_id = get_term_meta( $item_id, 'term_image', true );
          $img_src = wp_get_attachment_image_src( intval( $img_id ), array( '768', '511' ) );
          $img_url = $img_src[0];

          $item = array(
            'item_id' => $item_id,
            'item_title' => $term->name ?? '', 
            'item_url' => $term_link,
            'item_img' => $img_url
          );

          $data[] = $item;
        }
      }
    }
  }
  return $data;
}

/**
 * Get data for carousel items.
 *
 * @param   array  $selected_brands  [$selected_brands description]
 * @param   string $brand_image_type one of: logo, thumbnail, none
 *
 * @return  array                    carousel data.
 */
function falls_get_carousel_data( $selected_brands, $brand_image_type ) {

  $data = array();

  if ( ! empty( $selected_brands ) && is_array( $selected_brands ) ) {

    foreach ( $selected_brands as $brand_id ) {

      $logo_id = intval( get_post_meta( $brand_id, 'brand_logo', true ) );

      $alt_text   = get_post_meta( $logo_id , '_wp_attachment_image_alt', true );
      $brand_name = get_the_title( $brand_id );
      $ext_link   = get_post_meta( $brand_id, 'brand_external_link', true );
      $image_url   = falls_get_brand_image( $brand_id, $brand_image_type );
      $permalink  = get_the_permalink( $brand_id );

      /**
       * Get slide data
       * 
       * @var $alt_text
       * @var $brand_id
       * @var $brand_name
       * @var $ext_url
       * @var $logo_url
       *
       * @var [type]
       */
      $brand = array(
        'alt_text'   => $alt_text,
        'brand_id'   => $brand_id,
        'brand_name' => $brand_name,
        'permalink'  => get_the_permalink( intval( $brand_id ) ),
        'ext_url'    => $ext_link,
        'image_url'   => $image_url,
      );

      $data[] = $brand;
    }
  }

  return $data;
}

/**
 * Get Brand Logo URL
 * Returns default image if no logo set.
 *
 * @param  integer  $brand_id  Work post type item ID.
 *
 * @return string           logo url
 */
function falls_get_brand_image( $brand_id, $brand_image_type ) {

  $image_id = '';
  $image    = '';

  switch( $brand_image_type ) {
    case 'logo':
      $image_id = intval( get_post_meta( $brand_id, 'brand_logo', true ) );
      $image   = wp_get_attachment_image_url( $image_id, 'medium');
      break;
    case 'thumbnail':
      $image_id = intval( get_post_meta( $brand_id, 'brand_thumbnail', true ) );
      $image   = wp_get_attachment_image_url( $image_id, 'medium');
      break;
    case 'none':
      break;
  }

  // If no image found: 
  if ( empty( $image_id ) || empty( $image ) ) {

    if ( is_admin() ) {
      $image = 'https://placehold.co/500x500?text=Logo%20Placeholder';
    } else {
      $image = 'https://placehold.co/500x500/transparent/transparent/?text=Logo%20Placeholder';
    }
  }

  return $image;
}

/**
 * Generates dummy data for admin preview of page navigation
 *
 * @param  integer  $columns  number of columns needed
 *
 * @return  array            array of dummy data to format.
 */
function falls_get_dummy_page_navigation_items( $columns = '4' ) {
  
  // Get some default items
  $data = array();

  $default_img = FALLS_CO_URL . '/assets/images/placeholder.jpeg';

  $item = array(
    'item_id' => '',
    'item_title' => 'Placeholder Title', 
    'item_url' => home_url(),
    'item_img' =>  $default_img
  );

  for( $i = 0; $i < (2 * $columns); $i++ ) {
    $data[] = $item;
  }

  return $data;
}

function falls_debug( $data ) {

  ob_start();

  echo '<pre style="color:black !important;">';
  print_r( $data );
  echo '</pre>';

  return ob_get_clean();
}