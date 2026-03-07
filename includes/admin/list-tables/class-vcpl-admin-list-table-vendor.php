<?php

/**
 * List Table for the vendors.
 * display the principal features of the vendors.
 *
 * @package    Includes
 * @subpackage Includes/Admin/List_Tables
 * @version    1.0.0
 * @uses       VCPL_Admin_List_Table_User
 */

namespace Includes\Admin\List_Tables;

use Includes\Abstracts\VCPL_Admin_List_Table_User;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class VCPL_Admin_List_Table_Vendor extends VCPL_Admin_List_Table_User
{

  // The role of the user. Constructor.
  public function __construct()
  {

    parent::__construct();
    add_filter( 'vcpl_user_table_columns', array( $this, 'get_columns_vendor' ) );
    add_filter( 'vcpl_user_table_not_sortable_columns', array( $this, 'get_not_sortable_columns_vendor' ) );
    add_filter( 'vcpl_user_table_columns_default', array( $this, 'column_default_vendor' ), 10, 2 );

  }

  /**
   * Get the columns of the list table of the vendors.
   *
   * @param  array $columns Columns of the list table of the vendors.
   * @return array
   */
  public function get_columns_vendor( array $columns ): array
  {

    $columns[ 'customers' ]  = __( 'Customers', VCPL_TEXT_DOMAIN );
    $columns[ 'commission' ] = __( 'Commission', VCPL_TEXT_DOMAIN );
    $columns[ 'salary' ]     = __( 'Salary', VCPL_TEXT_DOMAIN );

    /**
     * Filter the columns of the list table of the vendors.
     *
     * @since 1.0.0
     * @param array  $columns  Columns of the list table of the vendors.
     * @return array Columns of the list table of the vendors.
     */
    return apply_filters( 'vcpl_vendor_table_columns', $columns );

  }

  /**
   * Get the not sortable columns for the list table of the vendors
   *
   * @param  array $columns Not sortable columns of the list table of the vendors.
   * @return array
   */
  public function get_not_sortable_columns_vendor( array $columns ): array
  {

    /**
     * Filter the not sortable columns of the list table of the vendors.
     *
     * @since 1.0.0
     * @param array  $columns  Not sortable columns of the list table of the vendors.
     * @return array Not sortable columns.
     */
    return apply_filters( 'vcpl_vendor_table_not_sortable_columns', array_merge( $columns, array( 'commission' ) ) );

  }

  /**
   * Get the default columns of the list table of the vendors.
   *
   * @param  object $item        The current user.
   * @param  string $column_name The name of the column.
   * @return mixed
   */
  public function column_default_vendor( object $item, string $column_name ): mixed
  {

    switch ( $column_name ) {
      case 'customers':
        $customers = get_user_meta( $item->ID, 'customers', true );
        return count( $customers ?: array() );
      case 'commission':
        $commission = get_user_meta( $item->ID, 'commission', true ) ?: array();
        $last_commission = end( $commission );

        return ( empty( $last_commission ) || ! isset( $last_commission['value'] ) 
          ? '0.00'
          : number_format( ( float ) $last_commission['value'], 2, '.', '' ) ) . ' %';

      case 'salary':
        $salary = get_user_meta( $item->ID, 'salary', true ) ?: array();
        $last_salary = end( $salary );

        return ( empty( $last_salary ) || ! isset( $last_salary['value'] )
          ? '0.00'
          : number_format( ( float ) $last_salary['value'], 2, '.', '' ) ) . ' ' . get_woocommerce_currency();
    }

    /**
     * Filter the default columns of the list table of the vendors.
     *
     * @since 1.0.0
     * @param  object $item        Current Item.
     * @param  string $column_name Current column name.
     * @return mixed  The value of the column.
     */
    return apply_filters( 'vcpl_vendor_table_columns_default', $item, $column_name );

  }

}
