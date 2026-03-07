<?php

/**
 * List Table for the shop managers.
 * display the principal features of the shop managers.
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

class VCPL_Admin_List_Table_Shop_Manager extends VCPL_Admin_List_Table_User
{

  // The role of the user. Constructor.
  public function __construct()
  {

    parent::__construct();
    add_filter( 'vcpl_user_table_columns', array( $this, 'get_columns_shop_manager' ) );
    add_filter( 'vcpl_user_table_columns_default', array( $this, 'column_default_shop_manager' ), 10, 2 );

  }

  /**
   * Get the columns of the list table of the shop managers.
   *
   * @param  array $columns Columns of the list table of the shop managers.
   * @return array
   */
  public function get_columns_shop_manager( array $columns ): array
  {

    $columns['salary'] = __( 'Salary', VCPL_TEXT_DOMAIN );

    /**
     * Filter the columns of the list table of the shop managers.
     *
     * @param array  $columns  Columns of the list table of the shop managers.
     * @return array Columns of the list table of the shop managers.
     */
    return apply_filters( 'vcpl_shop_manager_table_columns', $columns );

  }

  /**
   * Get the default columns of the list table of the shop managers.
   *
   * @param  \WP_User $item        The current user.
   * @param  string   $column_name The name of the column.
   * @return mixed
   */
  public function column_default_shop_manager( \WP_User $item, string $column_name ): mixed
  {

    switch ( $column_name ) {
      case 'salary':
        $salary = get_user_meta( $item->ID, 'salary', true ) ?: array();
        $last_salary = end( $salary );

        if ( empty( $last_salary ) || ! isset( $last_salary['value'] ) ) {
          return "0.00 " . get_woocommerce_currency();
        } else {
          return number_format( ( float ) $last_salary['value'], 2, '.', '' ) . ' ' . get_woocommerce_currency();
        }
    }

    return apply_filters( 'vcpl_shop_manager_table_columns_default', $item, $column_name );

  }

}
