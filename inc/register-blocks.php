<?php
/**
 * Falls Custom Blocks
 *
 * @link [fallsandco.com]
 * @since 1.0.0
 * @package Falls & Co
 * @subpackage falls-co/blocks
 */

/**
 * Adds custom blocks
 *
 * @since      1.0.0
 * @package    Falls & Co
 * @subpackage falls-co/blocks
 * @author Laird Sapir
 */

/**
 * We use WordPress's init hook to make sure
 * our blocks are registered early in the loading
 * process.
 *
 * @link https://developer.wordpress.org/reference/hooks/init/
 */
function falls_register_acf_blocks() {

  $blocks = array(
    'brand-carousel',
    'brand-selector',
    'page-navigation',
    'leader-tabbed',
    'falls-subtitle'
  );

  foreach( $blocks as $block_slug ) {

     /**
     * We register our block's with WordPress's handy
     * register_block_type();
     *
     * @link https://developer.wordpress.org/reference/functions/register_block_type/
     */
    register_block_type( __DIR__ . '/blocks/' . $block_slug );
  }
}

// Call our falls_register_acf_block() function on init.
add_action( 'init', 'falls_register_acf_blocks' );

/**
 * Populate Taxonomy Select List for Page Navigation Block Field
 *
 * @param  array $field
 * @return array updated $field
 */
function acf_load_taxonomy_slug_field_choices( $field ) {
    
  // Reset choices
  $field['choices'] = array();

  /**
   * Get list of taxonomies to use for options:
   * 
   * @var array $args 
   * @var array $taxes
   */
    $args = array(
      'public' => true
    );

    $taxes = get_taxonomies( $args, $output = 'objects', $operator = 'or' );

    /**
     * Add results to field choices
     */
    if( ! empty( $taxes ) && is_array( $taxes ) ) {
          
      foreach( $taxes as $tax ) {

        $slug = $tax->name ?? '';
        $label = $tax->labels->name ?? '';

        if ( $slug && $label ) {
          $field['choices'][ $slug  ] = $label;
        }
      }
    }

  // Return the field
  return $field;
  
}

add_filter('acf/load_field/name=taxonomy_slug', 'acf_load_taxonomy_slug_field_choices');