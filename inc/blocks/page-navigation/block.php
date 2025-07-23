<?php
/**
 * Page Navigation Block
 * Creates a grid or list based menu of links to pages or taxonomy terms, with featured images.
 *
 * @package falls-co
 * @since 1.0.0
 *
 * @param array $block The block settings and attributes.
 */

/**
 * Dependencies
 *
 * - Heloer functions for this Block.
 */
require_once FALLS_CO_PATH . '/inc/blocks/helper-functions.php';

/**
 * Attributes
 *
 * @var array   $data            block attributes
 *
 * @var string  $anchor          block anchor.
 * @var integer $columns    number of columns to display - default:4
 * @var integer $current_page_id current page ID,
 * @var string  $display    choice of: grid, list
 * @var string  $item_type     choice of: subpages, selectpages, taxonomy
 * @var string  $max_title_units
 * @var integer $max_title_width
 * @var string  $min_height
 * @var string  $row_gap
 * @var string  $col_gap
 * @var string  $show_titles
 */
$anchor          = $block['anchor'] ?? false;
$data            = $block['data'] ?? array();
$columns         = $data['columns'];
$current_page_id = get_the_ID();
$display         = $data['display_as'];
$item_type       = $data['pull_item_options_from'];
$min_height      = $block['style']['dimensions']['minHeight'] ?? false;
$max_title_width = $data['max_title_width'] ?? false;
$max_title_units = $data['max_title_units'] ?? '%';
$row_gap         = $data['grid_spacing_row_gap'];
$col_gap         = $data['grid_spacing_column_gap'];
$show_titles     = $data['show_titles'] ?? 'hover';
$anchor_el       = $data['anchor_el'] ?? false;

//echo falls_debug( $block );
/**
 * Mode
 *
 * @var string  $mode          choice of: preview/edit/auto
 * @var bool    $is_preview    whether or not we are rendering the block preview.
 */
$mode       = is_admin() ? ( $block['mode'] ?? 'edit' ) : 'preview';
$is_preview = $mode === 'preview' ? true : false;

/**
 * Assemble output classes.
 */
$output_class = 'falls-page-navigation wp-block-group is-layout-constrained';
// Title display:
$output_class .= ' show-titles-' . esc_attr( $show_titles );
// Display
$output_class .= ' is_display_' . $display;
// Style rules
$output_style = '';
// Grid styles
if ( 'grid' === $display ) {
	$output_style .= sprintf(
		'grid-template-columns: repeat(%1$s, minmax(0, 1fr)); gap:%2$srem %3$srem;',
    esc_attr( $columns ),
		esc_attr( $data['grid_spacing_row_gap'] ?? 0 ),
		esc_attr( $data['grid_spacing_column_gap'] ?? 0 ),
	);
}
// List Styles
if ( 'list' === $display ) {
	$output_style .= sprintf(
		'columns: %1$s; gap:%2$srem %3$srem;',
    esc_attr( $columns ),
		esc_attr( $data['grid_spacing_row_gap'] ?? 0 ),
		esc_attr( $data['grid_spacing_column_gap'] ?? 0 ),
	);
}
$title_span_style = '';
if ( ! empty( $max_title_width ) ) {
  $title_span_style = 'style="width:' . esc_attr( $max_title_width ) . esc_attr( $max_title_units ) . ';"';
}

// Handle empty admin
if ( empty( $post_parent ) && 'subpages' === $item_type && is_admin() ) {
  $item_type = 'dummy';
}

/**
 * Get necessary data to output menu items
 *
 * @var mixed bool/array $items - individual page navigation items
 */
$items = false;
switch ( $item_type ) {

  case 'dummy' : 
    $items = falls_get_dummy_page_navigation_items( $columns );
    break;
	case 'selectpages': // Items created from specific pages
		$select_pages = $data['select_pages'] ?? array();
		$item_ids     = is_array( $select_pages ) ? $select_pages : false;

		$items = falls_get_page_navigation_data( $item_ids, $current_page_id );

		break;

	case 'taxonomy': // Items created from specific taxonomy
		$taxonomy_slug  = $data['taxonomy_slug'] ?? '';
		$taxonomy_terms = $data['taxonomy_terms'] ?? false;

		$item_ids = is_array( $taxonomy_terms ) ? $taxonomy_terms : false;

		$items = falls_get_page_navigation_data( $item_ids, $current_page_id, $taxonomy_slug );

		break;
  case 'thispage': 

    $post_parent = get_the_ID();
    $page_args = array(
      'fields'         => 'ids',
      'order'          => 'ASC',
      'orderby'        => 'title',
      'post_parent'    => intval( $post_parent ),
      'post_status'    => 'publish',
      'post_type'      => 'page',
      'posts_per_page' => -1
    );

    $page_query = new WP_Query( $page_args );

    $item_ids = is_array( $page_query->posts ) ? $page_query->posts : false;
    $items    = falls_get_page_navigation_data( $item_ids, $current_page_id );

    wp_reset_postdata();
    break;
  case 'subpages':// Child Pages: pull by post_parent/curent page ID (default)
  default:
    $post_parent = $data['post_parent'] ?? '';

    if ( empty( $post_parent ) ) {
      $parent_page = wp_get_post_parent_id( $current_page_id );
      // If there is a parent page, use that.
      if ( ! empty( $parent_page ) ) {
        $post_parent = $parent_page;
      } else {
        // Otherwise, use this page.
        $post_parent = get_the_ID();
      }
    }
    $page_args = array(
      'fields'         => 'ids',
      'order'          => 'ASC',
      'orderby'        => 'title',
      'post_parent'    => intval( $post_parent ),
      'post_status'    => 'publish',
      'post_type'      => 'page',
      'posts_per_page' => -1
    );

    $page_query = new WP_Query( $page_args );

    $item_ids = is_array( $page_query->posts ) ? $page_query->posts : false;
    $items    = falls_get_page_navigation_data( $item_ids, $current_page_id );

    wp_reset_postdata();
    break;
  }

/**
 * Build menu
 *
 * @var string $menu formatted menu items.
 */
$menu = '';
$grid_count = '';
switch ( $display ) {

	case 'grid':
		if ( ! empty( $items ) && is_array( $items ) ) {

      $grid_count = count( $items );
      $output_class .= ' grid-items-' . esc_attr( $grid_count );

      $odd = $grid_count % 2;
      if ( ! empty( $odd ) ) {
        $output_class .= ' grid-items-odd';
      }
      $i = 1;

			foreach ( $items as $item ) {

				$item_url   = $item['item_url'];
        if ( ! empty( $anchor_el ) ){
          $anchor_el = str_replace( '#', '', $anchor_el );
          $item_url .= '#' . esc_attr( $anchor_el );
        }
				$item_title = $item['item_title'] ?? '';
				$item_img   = $item['item_img'] ?? '';
				$item_id    = $item['item_id'] ?? '';

				$item_class = 'falls-page-item item-' . esc_attr( $i );
        if ( $i === $grid_count ) {
          $item_class .= ' grid-item-last';
        }

        $item_style = '';
        if ( ! empty( $min_height ) ) {
          $item_style = 'style="min-height:' . $min_height . ';"';
        }
				// Is this the active page?
				$is_current = $current_page_id === intval( $item_id );
				if ( $is_current ) {
					$item_class .= ' current-item';
				}

				$menu .= sprintf(
					'<div %7$s class="wp-block-group %6$s" id="%5$s"><figure class="wp-block-image size-full"><img src="%2$s" alt="Link to: %3$s"/></figure><a href="%1$s" class="slide-title"><span %8$s>%4$s</span></a></div>',
					esc_url( $item_url ),
					esc_url( $item_img ),
					esc_attr( $item_title ),
					esc_html( $item_title ),
					esc_attr( 'ftn-item-' . $item_id ),
					esc_attr( $item_class ),
          $item_style,
          $title_span_style
				);
        // Increment item counter.
        $i++;
			}
		}
		break;
	case 'list':
		if ( ! empty( $items ) && is_array( $items ) ) {
			foreach ( $items as $item ) {
				$item_url   = $item['item_url'] ?? '';
        if ( ! empty( $anchor_el ) ){
          $anchor_el = str_replace( '#', '', $anchor_el );
          $item_url .= '#' . esc_attr( $anchor_el );
        }
				$item_title = $item['item_title'] ?? '';
				$item_img   = $item['item_img'] ?? '';
				$item_id    = $item['item_id'] ?? '';

				$is_current = $current_page_id === intval( $item_id );
				if ( $is_current ) {
					$item_class .= ' current-item';
				}


				$menu .= sprintf(
					'<div class="wp-block-group %1$s" id="%2$s">
              <p class="wp-block-paragraph"><a href="%3$s">%4$s</a></p>
            </div>
          <!-- /wp:group -->',
					esc_attr( $item_class ),
					esc_attr( 'ftn-item-' . $item_id ),
					esc_url( $item_uri ),
					esc_html( $item_title )
				);
			}
		}
		break;
}

/**
 * Output menu
 * 
 * @var string $output
 */
$output = '';


if ( $is_preview ) {
  // Add block wrapper attributes if preview:
	$output = sprintf(
		'<div id="%1$s" %2$s>',
		esc_attr( $anchor ),
		get_block_wrapper_attributes(
			array(
				'class' => $output_class,
				'style' => $output_style,
			)
		)
	);
}

if( is_admin() ) {
  $output .= '<div class="fpn-disable"></div>';
}

$output .= $menu;

if ( $is_preview ) {
  $output .= '</div>';
}

echo $output;