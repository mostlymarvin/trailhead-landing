<?php
/**
 * Subtitle
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
 */
$anchor         = $block['anchor'] ?? false;
$data           = $block['data'] ?? array();
$content_id     = $context['postId'];
$subtitle       = get_post_meta( $content_id, 'falls_subtitle', true );

/**
 * Assemble output classes and modify block wrapper attributes.
 * 
 * @var string $block_class
 * @var string $block_attributes
 */
$block_class      = 'falls-subtitle';
$block_attributes = get_block_wrapper_attributes( array( 'class' => $block_class ) );

/**
 * Begin Output
 *
 * @var string $output;
 */
$output = '';

/**
 *  Add block wrapper attributes if preview:
 *
 * @var boolean $is_preview
 */
if ( ! $is_preview ) {
  $output .= sprintf(
		'<div id="%2$s" %3$s>',
    esc_attr( $subtitle ),
		esc_attr( $anchor ),
		get_block_wrapper_attributes(
			array(
				'class' => $output_class,
				'style' => $output_style,
			)
		)
	);
} 

// Add Subtitle to output.
$output .= sprintf( 
  '<span>%1$s</span>',
  wp_kses_post ( $subtitle )
);

if ( ! $is_preview ) {
  $output .= '</div>';
} 

// Echo output.
echo $output;