<?php

/**
 * Abstract class VCPL_Admin_List_Table
 * Base class for the Admin Table User
 *
 * @package    Includes
 * @subpackage Includes/Admin
 * @extends    \WP_List_Table
 * @abstract
 */

namespace Includes\Abstracts;

// Load WC_Admin_List_Table if not loaded already.
require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

abstract class VCPL_Admin_List_Table_User extends \WP_List_Table
{

  /**
   * Table label.
   *
   * @var string
   */
  protected $label;

  /**
   * User role.
   *
   * @var array
   */
  protected $roles;

  /**
   * Constructor.
   * Set the singular and plural labels, and call the parent constructor.
   *
   * @param array $label Singular and plural label.
   * @param array $roles  User roles.
   */
  protected function __construct ()
  {

    $this->roles = VCPL()->get_current_user_page_args()['roles'] ?: array();
    $this->label = VCPL()->get_current_user_page_args()['title'] ?: "";

    parent::__construct( array(
      'singular' => __( $this->label, VCPL_TEXT_DOMAIN ),
      'plural'   => __( "{$this->label}s", VCPL_TEXT_DOMAIN ),
    ) );

  }

  // Display Message when no data is available.
  public function no_items () : void
  {
    _e( sprintf( 'No %s found.', $this->label ), VCPL_TEXT_DOMAIN );
  }

  /**
   * This function is used to get data for the list table.
   *
   * @global        $wpdb
   * @global        $plugin_page
   * @param  string $order_by    Column name to order by.
   * @param  string $order       Order to sort by.
   * @param  string $search_term Search term.
   * @param  string $registered  Registered date.
   * @param  string $post_action Post action.
   * @param  array  $post_ids    Post ids.
   * @return array  List table data.
   */
  public function wp_list_table_data ( $order_by = "", $order = "", $search_term = "", $registered = "", $post_action = "", $post_ids = array() ) : array
  {

    global $wpdb;
    global $plugin_page;

    if ( ! empty( $post_action ) && $post_action === "delete" && ! empty( $post_ids ) ) {
      $post_ids = array_values( array_filter( array_map( 'intval', (array) $post_ids ) ) );

      if ( empty( $post_ids ) ) {
        return array();
      }

      foreach ( $post_ids as $post_id ) {
        if ( ! current_user_can( 'delete_user', $post_id ) ) {
          wp_die( esc_html__( 'You are not allowed to delete one or more selected users.', VCPL_TEXT_DOMAIN ) );
        }
      }

      wp_safe_redirect( admin_url( "admin.php?page={$plugin_page}&action=delete_user&user_id=" . implode( ',', $post_ids ) ) );
      exit;
    }

    $like_clauses = array_map( fn( $role ) => "meta_value LIKE '%%" . $wpdb->esc_like( $role ) . "%%'", $this->roles );
    $like_query   = implode(' OR ', $like_clauses);

    // Filter by Registered Date.
    $query = $wpdb->prepare( "
      SELECT ID FROM $wpdb->users
      WHERE DATE_FORMAT( user_registered, '%%Y%%m' ) = %s
      AND ID IN (
          SELECT user_id FROM $wpdb->usermeta
          WHERE meta_key = '{$wpdb->prefix}capabilities'
          AND {$like_query}
      )", $registered );

    return get_users(
      array_merge(
        empty( $registered ) ? array() : array( 'include' => $wpdb->get_col( $query ) ),
        array(
          'role__in' => $this->roles,
          'orderby'  => $order_by,
          'order'    => $order,
          'search'   => "*{$search_term}*",
        )
      )
    );

  }

  /**
   * Prepare the list table items.
   * Defines all the columns and hidden columns.
   *
   * @return array List table items.
   */
  public function prepare_items () : array
  {

    $order_by     = vcpl_get_var( 'orderby', 'ID' );
    $order        = vcpl_get_var( 'order', 'DESC' );
    $search_term  = vcpl_get_var( 's', '' );
    $registered   = vcpl_get_var( 'filter_date', '' );
    $post_action  = vcpl_get_var( 'action', '', 'post' );
    $post_ids     = vcpl_get_var( 'bulk_action_' . $this->label, array() );
    $items        = $this->wp_list_table_data( $order_by, $order, $search_term, $registered, $post_action, $post_ids );
    $per_page     = 20;
    $pagenum      = $this->get_pagenum();

    $this->set_pagination_args( array( 'total_items' => count( $items ), 'per_page' => $per_page ) );

    $this->items           = array_slice( $items, ( ( $pagenum - 1 ) * $per_page ), $per_page );
    $this->_column_headers = array( $this->get_columns(), $this->get_hidden_columns(), $this->get_sortable_columns() );

    return $items;

  }

  /**
   * Display extra tablenav.
   *
   * @param  string $which Position of the extra tablenav.
   * @return void
   */
  public function extra_tablenav ( $which ) : void
  {

    if ( empty( $this->prepare_items() ) ) return;

    if ( $which === 'top' ) {
      $users    = get_users( array( 'role__in' => $this->roles ) );

      $dates = array();
      foreach ( $users as $user ) {
        $date  = date( 'F Y', strtotime( $user->user_registered ) );
        $value = date( 'Ym', strtotime( $user->user_registered ) );
        if ( ! in_array( $value, array_keys( $dates ) ) ) {
          $dates[$value] = $date;
        }
      }

      ksort( $dates ); ?>

      <div class="alignleft actions bulkactions">
        <select name="filter_date">
          <option value=""><?php _e( 'All dates', VCPL_TEXT_DOMAIN ); ?></option>
          <?php foreach ( $dates as $value => $date ) : ?>
            <option value="<?= $value; ?>" <?php selected( vcpl_get_var( 'filter_date', '' ), $value ); ?>><?= __( $date, VCPL_TEXT_DOMAIN ); ?></option>
          <?php endforeach; ?>
        </select>
        <?php submit_button( 'Filter', 'action', 'filter_action', false ); ?>
      </div>
    <?php }
  }

  /**
   * Set value default for the columns.
   *
   * @param  object $item          Current Item.
   * @param  string $column_name   Current column name.
   * @return array  Columns
   */
  public function column_default ( $item, $column_name ) : string
  {

    switch ( $column_name ) {
      case 'ID':
      case 'display_name':
      case 'user_email':
        return $item->$column_name;
    }

    /**
     * Filter the default columns.
     *
     * @param  object $item        Current Item.
     * @param  string $column_name Current column name.
     * @return mixed  The value of the column.
     */
    return apply_filters( "vcpl_user_table_columns_default", $item, $column_name );

  }

  /**
   * Set Hidden Columns.
   *
   * @return array - The hidden columns.
   */
  public function get_hidden_columns () : array
  {

    /**
     * Filter the hidden columns.
     *
     * @param  array $columns Hidden Columns.
     * @return array Hidden Columns.
     */
    return apply_filters( 'vcpl_user_table_hidden_columns', array( 'ID' ) );

  }

  /**
   * Set Sortable Columns.
   * Excludes Columns are not sortable.
   *
   * @return array Sortable Columns.
   */
  public function get_sortable_columns () : array
  {

    return array_reduce(
      array_keys( $this->get_columns() ),
      function ( $acc, $key ) {

        /**
         * Filter the not sortable columns.
         *
         * @param  array $columns Not Sortable Columns.
         * @return array Not Sortable Columns.
         */
        if ( ! in_array( $key, apply_filters( 'vcpl_user_table_not_sortable_columns', array( 'ID', 'cb' ) ) ) ) {
          $acc[$key] = array( $key, false );
        }

        return $acc;

      },
      array()
    );

  }

  /**
   * Set the columns.
   *
   * @return array - The columns.
   */
  public function get_columns () : array
  {

    $columns = array(
      'cb'           => '<input type="checkbox" />',
      'ID'           => 'id',
      'user_login'   => __( 'Username', VCPL_TEXT_DOMAIN ),
      'display_name' => __( 'Name', VCPL_TEXT_DOMAIN ),
      'user_email'   => __( 'Email', VCPL_TEXT_DOMAIN ),
    );

    /**
     * Filter the columns.
     *
     * @param  array $columns Columns.
     * @return array Columns.
     */
    return apply_filters ( 'vcpl_user_table_columns', $columns );

  }

  /**
   * Set Column cb.
   *
   * @return string
   */
  public function column_cb( $item ) : string
  {
    return sprintf( '<input type="checkbox" name="bulk_action_%s[]" value="%s" />', $this->label, $item->ID );
  }

  /**
   * Actions User Login Column.
   * Edit and Delete.
   *
   * @return array - The columns.
   */
  public function column_user_login( $item ) : string
  {

    return sprintf(
      '<div class="vcpl-user-login-table"><img src="%s" width="30" height= "30" alt="img-profile"><div class="vcpl-tablelist-users-actions">%s %s</div></div>',
      vcpl_get_img_profile_url( $item->ID ),
      $item->user_login,
      $this->row_actions(
        array(
          'edit'   => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=' . VCPL()->get_current_user_page() . '&action=edit_user&user_id=' . $item->ID ), __( 'Edit', VCPL_TEXT_DOMAIN ) ),
          'delete' => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=' . VCPL()->get_current_user_page() . '&action=delete_user&user_id=' . $item->ID ), __( 'Delete', VCPL_TEXT_DOMAIN ) )
        )
      )
    );

  }

  /**
   * Bulk Actions.
   *
   * @return array
   */
  public function get_bulk_actions() : array
  {
    return array( 'delete' => __( 'Delete', VCPL_TEXT_DOMAIN ) );
  }
}
