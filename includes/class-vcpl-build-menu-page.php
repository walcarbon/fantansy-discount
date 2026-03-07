<?php

/**
 * Class Buildmenupage
 *
 * This class is used to build menu and submenu pages in the WordPress admin area.
 *
 * @package    Includes
 * @subpackage Includes/VCPL_Build_Menupage
 */

namespace Includes;

defined( 'ABSPATH' ) || exit;

class VCPL_Build_Menu_Page
{
  /**
   * The Menus registered with WordPress.
   *
   * @var array
   */
  private $menus;

  /**
   * The Submenus registered with WordPress.
   *
   * @var array
   */
  private $submenus;


  // Initialize the collections used to maintain the menus and submenus.
  public function __construct()
  {
    $this->menus    = array();
    $this->submenus = array();
  }

  /**
   * Get the view.
   *
   * @param array $args The arguments.
   */
  private function view( array $args ): void
  {
    if ( is_array( $args ) ) {
      list( $object, $method, $view ) = ( $args );
      $object->$method( $view );
    }
  }

  /**
   * Add a menu.
   *
   * @param  array  $menus The existing menus.
   * @param  string $page_title The text to be displayed in the title tags of the page when the menu is selected.
   * @param  string $menu_title The text to be used for the menu.
   * @param  string $capability The capability required for this menu to be displayed to the user.
   * @param  string $menu_slug The slug name to refer to this menu by.
   * @param  array  $args The arguments.
   * @param  string $icon_url The URL to the icon to be used for this menu.
   * @param  int    $position The position in the menu order this one should appear.
   * @return array  The updated menus.
   */
  private function add_menu( array $menus, string $page_title, string $menu_title, string $capability, string $menu_slug, array $args, string $icon_url, int $position ): array
  {
    $menus[] = array(
      "page_title" => $page_title,
      "menu_title" => $menu_title,
      "capability" => $capability,
      "menu_slug"  => $menu_slug,
      "args"       => $args,
      "icon_url"   => $icon_url,
      "position"   => $position
    );
    return $menus;
  }

  /**
   * Add a menu page.
   *
   * @param  string $page_title The text to be displayed in the title tags of the page when the menu is selected.
   * @param  string $menu_title The text to be used for the menu.
   * @param  string $capability The capability required for this menu to be displayed to the user.
   * @param  string $menu_slug The slug name to refer to this menu by.
   * @param  array  $args The arguments.
   * @param  string $icon_url The URL to the icon to be used for this menu.
   * @param  int    $position The position in the menu order this one should appear.
   * @return self
   */
  public function add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, array $args, string $icon_url, int $position ): self
  {
    $this->menus = $this->add_menu( $this->menus, $page_title, $menu_title, $capability, $menu_slug, $args, $icon_url, $position );
    return $this;
  }

  /**
   * Add a submenu.
   *
   * @param  array  $submenus The existing submenus.
   * @param  string $parent_slug The slug name for the parent menu.
   * @param  string $page_title The text to be displayed in the title tags of the page when the menu is selected.
   * @param  string $menu_title The text to be used for the menu.
   * @param  string $capability The capability required for this menu to be displayed to the user.
   * @param  string $menu_slug The slug name to refer to this menu by.
   * @param  array  $args The arguments.
   * @return array  The updated submenus.
   */
  private function add_submenu( array $submenus, string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, array $args ): array
  {
    $submenus[] = array(
      'parent_slug'   => $parent_slug,
      'page_title'    => $page_title,
      'menu_title'    => $menu_title,
      'capability'    => $capability,
      'menu_slug'     => $menu_slug,
      'args'          => $args
    );
    return $submenus;
  }

  /**
   * Add a submenu page.
   *
   * @param  string $parent_slug The slug name for the parent menu.
   * @param  string $page_title The text to be displayed in the title tags of the page when the menu is selected.
   * @param  string $menu_title The text to be used for the menu.
   * @param  string $capability The capability required for this menu to be displayed to the user.
   * @param  string $menu_slug The slug name to refer to this menu by.
   * @param  array  $args The arguments.
   * @return self
   */
  public function add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, array $args ): self
  {
    $this->submenus = $this->add_submenu( $this->submenus, $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $args );
    return $this;
  }

  // Run the class. Add all the menus and submenus to the WordPress admin area.
  public function run(): void
  {
    foreach ( $this->menus as $menu ) {
      extract( $menu, EXTR_OVERWRITE );
      add_menu_page(
        $page_title,
        $menu_title,
        $capability,
        $menu_slug,
        function () use ( $args ) {
          $this->view( $args );
        },
        $icon_url,
        $position
      );
    }

    foreach ( $this->submenus as $submenu ) {
      extract( $submenu, EXTR_OVERWRITE );
      add_submenu_page(
        $parent_slug,
        $page_title,
        $menu_title,
        $capability,
        $menu_slug,
        function () use ( $args ) {
          $this->view( $args );
        }
      );
    }
  }
}
