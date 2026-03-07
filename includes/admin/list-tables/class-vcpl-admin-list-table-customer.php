<?php

/**
 * List Table for the customers.
 * display the principal features of the customers.
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

class VCPL_Admin_List_Table_Customer extends VCPL_Admin_List_Table_User
{
  /**
   * Constructor.
   * Set the singular and plural labels, and call the parent constructor.
   * Role is set to the current plugin page without "s" at the end.
   */
  public function __construct ()
  {

    parent::__construct();

    add_filter( 'vcpl_user_table_columns', array( $this, 'get_columns_customer' ) );
    add_filter( 'vcpl_user_table_not_sortable_columns', array( $this, 'get_not_sortable_columns_customer' ) );
    add_filter( 'vcpl_user_table_columns_default', array( $this, 'column_default_customer' ), 10, 2 );

  }

  /**
   * Get the columns for the list table of the customers.
   *
   * @param  array $columns Columns of the list table of the customers.
   * @return array
   */
  public function get_columns_customer ( array $columns ) : array
  {

    $columns['vendor'] = __( 'Vendor', VCPL_TEXT_DOMAIN );
    $columns['role']   = __( 'Role', VCPL_TEXT_DOMAIN );
    $columns['total']  = __( 'Total Purchases', VCPL_TEXT_DOMAIN );

    /**
     * Filter the columns of the list table of the customers.
     *
     * @param array  $columns  Columns of the list table of the customers.
     * @return array Columns of the list table of the customers.
     */
    return apply_filters( 'vcpl_customer_table_columns', $columns );

  }

  /**
   * Get the not sortable columns for the list table of the customers.
   *
   * @param  array $columns Not sortable columns of the list table of the customers.
   * @return array
   */
  public function get_not_sortable_columns_customer ( array $columns ) : array
  {
     /**
     * Filter the not sortable columns of the list table of the customers.
     *
     * @param array  $columns  Not sortable columns of the list table of the customers.
     * @return array Not sortable columns
     */
    return apply_filters( 'vcpl_customer_table_not_sortable_columns', array_merge( $columns, array( 'role' ) ) );

  }

  /**
   * Get the default columns for the list table of the customers.
   *
   * @param  WP_User $item        The current user.
   * @param  string  $column_name The name of the column.
   * @return mixed   The value of the column.
   */
  public function column_default_customer ( object $item, string $column_name ) : mixed
  {

    switch ( $column_name ) {
      case 'vendor':
        $vendor_id = get_user_meta( $item->ID, 'vendor', true );
        return get_user_by( 'id', $vendor_id )->user_login ?? __( 'No vendor', VCPL_TEXT_DOMAIN );
      case 'role':
        return wp_roles()->roles[trim( implode( '', get_userdata( $item->ID )->roles ) )]['name'] ?: __( 'No role', VCPL_TEXT_DOMAIN );
      case 'total':
        return wc_get_customer_total_spent( $item->ID ) . ' ' . get_woocommerce_currency();
    }

    /**
     * Action for the default columns of the list table of the customers.
     *
     * @param  WP_User $item        The current user.
     * @param  string  $column_name The name of the column.
     * @return mixed   The value of the column.
     */
    return apply_filters( 'vcpl_customer_table_columns_default', $item, $column_name );

  }
}
