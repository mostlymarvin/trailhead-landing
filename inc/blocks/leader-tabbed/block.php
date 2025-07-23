<?php
/**
 * Leader: Tabbed Output
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
 * @var string   $leader_name"
 * @var string   $leader_title
 * @var string   $leader_photo
 * @var string   $tab_1_title
 * @var string   $tab_1_text
 * @var string   $tab_2_title
 * @var string   $tab_2_text
 * @var string   $tab_3_title
 * @var string   $tab_3_text
 * @var string   $background_color
 * @var string   $text_color
 * 
 */
$anchor              = $block['anchor'] ?? false;
$data                = $block['data'] ?? array();
$text_align          = $block['alignText'] ?? 'left';

// Leader Name
$leader_name            = $data['leader_name'] ?? false;
$leader_name_tag        = $data['leader_name_tag'] ?? 'p';
$leader_name_font_size  = $data['leader_name_font_size'] ?? 'default';
// Leader Title
$leader_title           = $data['leader_title'] ?? false;
$leader_title_tag       = $data['leader_title_tag'] ?? 'p';
$leader_title_font_size = $data['leader_title_font_size'] ?? 'default';
// Leader Photo
$leader_photo        = $data['leader_photo'] ?? false;

// Content font size
$tab_content_font_size = $data['tab_content_font_size'] ?? 'default';

// Block styles and colors
$background_color      = $block['backgroundColor'] ?? ( $block['style']['color']['background'] ?? false );
$text_color            = $block['textColor'] ?? ( $block['style']['color']['text'] ?? false );

/**
 * Assemble output classes and modify block wrapper attributes.
 * 
 * @var string $block_class
 * @var string $block_attributes
 */
$block_class      = 'falls-leader';
$block_attributes = get_block_wrapper_attributes( array( 'class' => $block_class ) );


/**
 * Format Tabs
 */
$tab_content = array();
$tab_nav     = array();
for ( $i = 1; $i < 4; $i++ ) {

  $tab_title   = $data['tab_' . $i . '_title'] ?? 'false';
  $tab_text    = apply_filters( 'acf_the_content', $data['tab_' . $i . '_text'] ?? false );
 
  if ( ! empty( $tab_title ) && ! empty( $tab_text ) ) {

    $tab_nav[] = sprintf(
      '<li><a href="#leader-tab-%1$s">%2$s</a></li>',
      esc_attr( $i ),
      wp_kses_post( $tab_title )
    );

    $tab_content[] = sprintf(
      '<div id="leader-tab-%1$s">%2$s</div>',
      esc_attr( $i ),
      wp_kses_post( $tab_text )
    );
  } 
}

$tabs = '';
if ( ! empty( $tab_nav ) && ! empty( $tab_content ) ) {

  $tabs = sprintf(
    '<div class="leader-tabs">
      <ul>%1$s</ul>
      <div class="tabgroup has-%2$s-font-size">%3$s</div>
    </div>',
    implode( '', $tab_nav ),
    esc_attr( $tab_content_font_size ),
    implode( '', $tab_content )
  );
}

/**
 * Format Leader 
 */
$leader_name_class  = array( 'leader-name' );
$leader_title_class = array( 'leader-title' );
$leader_name_style  = '';

$leader  .= '<div class="leader-meta"><div class="leader-meta-inner has-text-align-' . $text_align . '">';
// Format Name
if ( ! empty( $leader_name ) ) {
  switch( $leader_name_font_size ) {
    case 'default':
    case 'inherit':
      $leader_name_class[] = 'font-size-' . esc_attr( $leader_name_font_size );
    break;
    default:
      $leader_name_class[] = 'has-' . esc_attr( $leader_name_font_size ) . '-font-size';
    break;
  }
  $leader .= sprintf( 
    '<%1$s class="%2$s">%3$s</%1$s>',
    esc_html( $leader_name_tag ),
    esc_attr( implode( ' ', $leader_name_class ) ),
    wp_kses_post( $leader_name )
  );
}
// Format Title
if ( ! empty( $leader_title ) ) {
  switch( $leader_title_font_size ) {
    case 'default':
    case 'inherit':
      $leader_title_class[] = 'font-size-' . esc_attr( $leader_title_font_size );
    break;
    default:
      $leader_title_class[] = 'has-' . esc_attr( $leader_title_font_size ) . '-font-size';
    break;
  }
  $leader .= sprintf( 
    '<%1$s class="%2$s">%3$s</%1$s>',
    esc_html( $leader_title_tag ),
    esc_attr( implode( ' ', $leader_title_class ) ),
    wp_kses_post( $leader_title )
  );
}
// End leader meta inner
$leader .= '</div>';
// Photo
if ( ! empty( $leader_photo ) ) {
  $leader_img = wp_get_attachment_image_src( intval( $leader_photo ), 'full' );

  if ( ! empty( $leader_img[0] ) ) {
    $leader .= sprintf(
      '<div class="img-wrap"><img src="%1$s" class="leader-image"/></div>',
      esc_url( $leader_img[0] )
    );
  }
}
// End leader
$leader .= '</div>';


/**
 * Begin output
 *
 * @var string $output
 */
$output = sprintf(
    '<div id="%1$s" %2$s><div class="leader-inner">%3$s%4$s</div></div>',
    esc_attr( $anchor ),
    $block_attributes,
    $leader,
    $tabs
  );

echo $output;



