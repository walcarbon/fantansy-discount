<?php

/**
 * The role class. Manages the roles of the users.
 * Configures the roles of the users.
 *
 * @package    Includes
 * @subpackage Includes/Role
 * @version    1.0.0
 */

namespace Includes;

defined( 'ABSPATH' ) || exit;

class VCPL_Role
{
   /**
   * The array of roles registered with WordPress.
   *
   * @var  array - The roles registered with WordPress to fire when the plugin loads.
   */
  private const ROLES = [
    'vendor'       => 'Vendor',
    'cbv'          => 'Customer by Vendor',
    'cbs'          => 'Customer by Shop',
    'cw'           => 'Customer Wholesaler',
    'shop_manager' => 'Shop Manager'
  ];


  // Set the new roles and configure the default roles
  public function set_roles() : void
  {
    $wp_roles = new \WP_Roles;

    foreach ( self::ROLES as $role => $name ) {
      if ( $role === 'shop_manager' ) continue;
      $caps = array_merge( array( 'read' => true ), ( 'vendor' === $role ? array( 'delete_users' => true ) : array() ) );
      if ( ! $wp_roles->is_role( $role ) ) $wp_roles->add_role( $role, $name, $caps );
    }

    $wp_roles->remove_role('customer');
  }

  /**
   * Hide the roles from the wp user page.
   *
   * @param  array $roles - The roles of the users.
   * @return array
   */
  public function hide_vcroles_wpuser_page( array $roles ) : array
  {
    foreach ( self::ROLES as $role => $name ) {
      if ( isset( $roles[ $role ] ) ) unset( $roles[$role] );
    }
    return $roles;
  }

  /**
   * Hide the users from the wp user table.
   *
   * @param  object $query - The query object.
   */
  public function hide_users_vcroles_wpuser_table( object $query ) : void
  {
    global $pagenow;
    if ( is_admin() && 'users.php' === $pagenow ) {
      $query->set( 'role__not_in', array_keys( self::ROLES ) );
    }
  }

  /**
   * Hide the roles from the wp user table.
   *
   * @param  array $views - The views of the users.
   * @return array
   */
  public function hide_vcroles_tabnav_wpuser_table ( array $views ) {
    foreach ( self::ROLES as $role => $name ) {
      if ( isset( $views[$role] ) ) {
        unset( $views[$role] );
      }
    }
    return $views;
  }
}
