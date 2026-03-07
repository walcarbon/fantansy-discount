<?php
/**
 * Set up the CSV product.
 *
 * @package    Includes
 * @subpackage Includes/Product_Csv
 * @version    1.0.0
 */

namespace Includes;

defined( 'ABSPATH' ) || exit;

class VCPL_Product_Csv
{

  /**
   * Add custom fields to export meta keys.
   *
   * @param array $meta_keys
   * @return array
   */
  public function add_custom_field_to_export_meta_keys( $meta_keys ) {

    return array_merge( $meta_keys, array(
      '_product_location',
      '_items_per_products',
    ) );

  }

  /**
   * Add custom fields to import mapping.
   *
   * @param array $columns
   * @return array
   */
  public function add_custom_field_to_import_mapping( $columns ) {

    return array_reduce( array(
      '_product_location',
      '_items_per_products',
    ), fn( $columns, $key ) => $columns + array( trim( str_replace('_', ' ', ucwords( $key ) ) ) => $key ), $columns );

  }

}