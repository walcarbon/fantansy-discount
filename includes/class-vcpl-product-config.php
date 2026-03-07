<?php

/**
 * Set up the product configurations.
 *
 * @package    Includes
 * @subpackage Includes/Product_Config
 * @version    1.0.0
 */

namespace Includes;

defined( 'ABSPATH' ) || exit;

class VCPL_Product_Config
{
  /**
   * Change the price html of the product when the user is not logged in.
   *
   * @param    string $price_html - The price html of the product.
   * @return   string - The price html of the product.
   */
  public function change_html_price( string $price_html )
  {
    return is_user_logged_in()
      ? $price_html
      : '<span class="woocommerce-Price-amount amount-hidden">' . __( 'Login to view prices', VCPL_TEXT_DOMAIN ) . '</span>';
  }

  /**
   * Disable the shopping of the product when the user is not logged in.
   *
   * @param    mixed $purchasable - The purchasable of the product.
   * @return   mixed - The purchasable of the product.
   */
  public function disable_shopping( mixed $purchasable )
  {
    return ( $purchasable = ! is_user_logged_in() ? false : $purchasable );
  }

  /**
   * change the add to cart text of the product when the user is not logged in.
   *
   * @param    string $text - The add to cart text of the product.
   * @return   string - The add to cart text of the product.
   */
  public function items_per_product( array $args, object $product )
  {
    $item_per_products = $product->get_meta( '_items_per_products', true );
    $args['min_value'] = !empty( $item_per_products ) ? $item_per_products : 1;
    $args['step']      = !empty( $item_per_products ) ? $item_per_products : 1;

    if ( !is_user_logged_in() ) {
      $args['input_value'] = 1;
      $args['min_value']   = 1;
      $args['max_value']   = 1;
      $args['style']       = 'display:none;';
    }

    return $args;
  }
}