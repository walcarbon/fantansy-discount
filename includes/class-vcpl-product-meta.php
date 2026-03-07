<?php

/**
 * Set up the product meta and configurations.
 *
 * @package    Includes
 * @subpackage Includes/Product_Meta
 * @version    1.0.0
 */

namespace Includes;

defined( 'ABSPATH' ) || exit;

class VCPL_Product_Meta
{
  // Product meta for the admin inventory section of the product.
  public function metadata_of_admin_inventory_product_option() : void
  {
    global $product_object;

    $args = array(
      array(
        'id'          => '_product_location',
        'label'       => __( 'Product Location', 'woocommerce' ),
        'description' => __( 'Set Product\'s Location .', 'woocommerce' ),
        'value'       => $product_object->get_meta( '_product_location', true ),
      )
    );

    foreach ( $args as $arg ) {
      woocommerce_wp_text_input( $arg );
    }
  }

  // Product meta for the admin general section of the product.
  public function metadata_of_admin_general_option() : void
  {
    global $product_object;

    $args = array(
      array(
        'id'        => '_items_per_products',
        'label'     => __( 'Items Per Products', 'woocommerce' ),
        'data_type' => 'items',
        'type'      => 'number',
        'step'      => '1',
        'max'       => 100,
        'value'     => $product_object->get_meta( '_items_per_products', true ),
      )
    );

    foreach ( $args as $arg ) {
      woocommerce_wp_text_input( $arg );
    }
  }

  /**
   * Save the meta data of the product.
   *
   * @param    object $product - The product object.
   */
  public function save_metadata( object $product ): void
  {
    $meta_keys = array(
      '_product_location',
      '_items_per_products',
    );

    foreach ( $meta_keys as $key ) {
      if ( isset( $_POST[$key] ) ) {
        $product->update_meta_data( $key, sanitize_text_field( $_POST[$key] ) );
      }
    }
  }
}
