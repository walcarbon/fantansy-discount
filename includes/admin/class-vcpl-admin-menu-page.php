<?php

/**
 * Menupage of the plugin.
 * configures the menupage of the plugin.
 *
 * @package    Includes
 * @subpackage Includes/Admin
 * @version    1.0.0
 */

namespace Includes\Admin;

defined( 'ABSPATH' ) || exit;

class VCPL_Admin_Menu_Page
{
  /**
   * builder class instance.
   * This builder will be used to build the menupage.
   *
   * @var object
   */
  private $builder_menu_pages;

  /**
   * Constructor.
   * Initializes the menupage builder.
   *
   * @param object $builder - The menupage builder.
   */
  public function __construct( object $builder_menu_pages )
  {
    $this->builder_menu_pages = $builder_menu_pages;
  }

  /**
   * Render option page.
   * This method is used to render the menupage.
   *
   * @param string $name - The name of the view to render.
   */
  public function view( string $name ): void
  {
    if ( is_string( $name ) && !empty( $name ) ) {
      require_once( plugin_dir_path( __FILE__ ) . sprintf( 'views/html-admin-page-%s.php', $name ) );
    }
  }

  /**
   * Get the menupage data.
   * This method is used to get the menupage data.
   *
   * @return array
   */
  private function menu_pages_data(): array
  {
    return array(
       /**
       * all the menupages of the plugin.
       *
       * Vendors:       Vendors page of the plugin.
       * Customers:     Customers page of the plugin.
       * Shop Managers: Shop managers page of the plugin.
       * Analythics:    Analythics page of the plugin.
       */
      array(
        'Vendors',
        'Vendors',
        'manage_options',
        'vendor',
        array( $this, 'view', 'users' ),
        'dashicons-businessman',
        6
      ),
      array(
        'Customers',
        'Customers',
        'manage_options',
        'customer',
        array( $this, 'view', 'users' ),
        'dashicons-groups',
        7
      ),
      array(
        'Shop Managers',
        'Shop Managers',
        'manage_options',
        'shop_manager',
        array( $this, 'view', 'users' ),
        'dashicons-admin-users',
        8
      ),
      array(
        'Analythics',
        'Analytics',
        'manage_options',
        'analytics',
        array( $this, 'view', 'analytics' ),
        'dashicons-chart-area',
        8
      ),
    );
  }

  // Register the menupages.
  public function option_pages(): void
  {
    foreach ( $this->menu_pages_data() as $menu_page ) {
      list( $page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url, $position ) = $menu_page;
      $this->builder_menu_pages->add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url, $position );
    }

    $this->builder_menu_pages->run();
  }
}
