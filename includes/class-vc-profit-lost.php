<?php

/**
 * The core plugin class.
 *
 * Define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since      1.0.0
 * @package    Includes
 * @subpackage Includes/VCPL
 */

namespace Includes;

use Includes\VCPL_Loader;
use Includes\Admin\VCPL_Admin;
use Includes\VCPL_Role;
use Includes\VCPL_Ajax;
use Includes\VCPL_Build_Menu_Page;
use Includes\Admin\VCPL_Admin_Menu_Page;
use Includes\VCPL_Product_Meta;
use Includes\VCPL_Product_Csv;
use Includes\VCPL_Product_Config;
use Includes\Public\VCPL_Public;
use Includes\Admin\VCPL_Admin_Profile;
use Includes\VCPL_Form_Handler;
use Includes\VCPL_Session_Handler;

defined( 'ABSPATH' ) || exit;

class VC_Profit_Lost
{
  /**
   * The name of the plugin.
   *
   * @const
   */
  private const PLUGIN_NAME = 'vc-profil-lost';

  /**
   * Version of the plugin.
   *
   * @const
   */
  private const PLUGIN_VERSION = VCPL_VERSION;

  /**
   * Loader that's responsible for registering all hooks that power the plugin.
   *
   * @var VCPL_Loader
   */
  private $loader;

  /**
   * Defines all Actions that occur in the admin area.
   *
   * @var VCPL_Admin
   */
  private $admin;

  /**
   * Configure the user roles of the plugin.
   *
   * @var VCPL_Role
   */
  private $role;

  /**
   * @since 1.0.0
   * @access private
   * @var $ajax - The class that defines the ajax requests of the plugin.
   */
  private $ajax;

  /**
   * Build the menupage of the plugin.
   *
   * @var VCPL_Build_Menupage
   */
  private $build_menu_page;

  /**
   * Admin menupage of the plugin. Configures the admin menupage of the plugin.
   *
   * @var VCPL_Admin_Menupage
   */
  private $admin_menu_page;

  /**
   * Set the product metas and save them.
   *
   * @var VCPL_Product_Meta
   */
  private $product_meta;

  /**
   * Set the product csv metas and save them.
   *
   * @var VCPL_Product_Csv
   */
  private $product_csv;

  /**
   * Set the product configurations.
   *
   * @var VCPL_Product_Config
   */
  private $product_configs;

  /**
   * Defines the frontend of the plugin.
   *
   * @var VCPL_Public
   */
  private $public;

  /**
   * Defines the admin_profile of the plugin.
   *
   * @var VCPL_Admin_Profile
   */
  private $admin_profile;

  /**
   * Defines the form handler of the plugin.
   *
   * @var VCPL_Form_Handler
   */
  private $form_handler;

  /**
   * Defines the profile of the plugin.
   *
   * @var VCPL_Session_Handler
   */
  private $session_handler;

  /**
   * Define the core functionality of the plugin.
   *
   * Load the dependencies, define the locale, and set the hooks for the admin area and
   * the public-facing side of the site.
   *
   * @return void
   */
  public function __construct()
  {
    $this->load_dependencies();
    $this->define_admin_hooks();
    $this->define_public_hooks();
  }

  /**
   * Load the required dependencies for this plugin.
   *
   * Create an instance of the loader which will be used to register the hooks
   * with WordPress.
   *
   * @return void
   */
  private function load_dependencies(): void
  {
    $this->loader          = new VCPL_Loader();
    $this->admin           = new VCPL_Admin( self::PLUGIN_NAME, self::PLUGIN_VERSION );
    $this->role            = new VCPL_Role();
    $this->ajax            = new VCPL_Ajax();
    $this->build_menu_page = new VCPL_Build_Menu_Page();
    $this->admin_menu_page = new VCPL_Admin_Menu_Page( $this->build_menu_page );
    $this->product_meta    = new VCPL_Product_Meta();
    $this->product_csv     = new VCPL_Product_Csv();
    $this->product_configs = new VCPL_Product_Config();
    $this->public          = new VCPL_Public( self::PLUGIN_NAME, self::PLUGIN_VERSION );
    $this->admin_profile   = new VCPL_Admin_Profile();
    $this->form_handler    = new VCPL_Form_Handler( $this->role );
    $this->session_handler = new VCPL_Session_Handler();
  }

  // Register all of the hooks related to the admin area functionality of the plugin.
  private function define_admin_hooks(): void
  {
    // Actions that are defined in the admin class.
    $this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_styles' );
    $this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_scripts' );
    $this->loader->add_filter( 'script_loader_tag', $this->admin, 'add_module_attribute', 10, 2 );

    // Actions that are defined in the roles class.
    $this->loader->add_action( 'init', $this->role, 'set_roles' );
    $this->loader->add_filter( 'editable_roles', $this->role, 'hide_vcroles_wpuser_page' );
    $this->loader->add_action( 'pre_get_users', $this->role, 'hide_users_vcroles_wpuser_table' );
    $this->loader->add_action( 'views_users', $this->role, 'hide_vcroles_tabnav_wpuser_table' );

    //Actions that are defined in the Admin_Menupage.
    $this->loader->add_action( 'admin_menu', $this->admin_menu_page, 'option_pages' );

    //Actions that are defined in the Ajax class.
    $this->loader->add_action( 'wp_ajax_actions_before_delete_user', $this->ajax, 'actions_before_delete_user' );

    //Actions that are defined in the Product_Meta class.
    $this->loader->add_action( 'woocommerce_product_options_sku', $this->product_meta, 'metadata_of_admin_inventory_product_option' );
    $this->loader->add_action( 'woocommerce_product_options_pricing', $this->product_meta, 'metadata_of_admin_general_option' );
    $this->loader->add_action( 'woocommerce_admin_process_product_object', $this->product_meta, 'save_metadata' );

    //Actions that are defined in the Product_csv class.
    $this->loader->add_filter( 'woocommerce_product_export_meta_keys', $this->product_csv, 'add_custom_field_to_export_meta_keys' );
    $this->loader->add_filter( 'woocommerce_product_import_mapping_default_columns', $this->product_csv, 'add_custom_field_to_import_mapping' );

    //Actions that are defined in the Product_Configs class.
    $this->loader->add_filter( 'woocommerce_get_price_html', $this->product_configs, 'change_html_price' );
    $this->loader->add_filter( 'woocommerce_is_purchasable', $this->product_configs, 'disable_shopping' );
    $this->loader->add_filter( 'woocommerce_quantity_input_args', $this->product_configs, 'items_per_product', 10, 2 );

    //Actions that are defined in the user_meta class.
    $this->loader->add_filter( 'vcpl_new_user_fields', $this->admin_profile, 'get_user_meta_fields' );
    $this->loader->add_filter( 'vcpl_edit_profile_fields', $this->admin_profile, 'get_user_meta_fields' );

    //Actions that are defined in the Form_Handler class.
    $this->loader->add_action( 'admin_init', $this->form_handler, 'save_new_user' );
    $this->loader->add_action( 'admin_init', $this->form_handler, 'save_edit_profile' );
    $this->loader->add_action( 'admin_init', $this->form_handler, 'delete_user' );
  }

  // Register all of the hooks related to the public-facing functionality of the plugin.
  private function define_public_hooks()
  {

    // Actions that are defined in the public class.
    $this->loader->add_action( 'wp_enqueue_scripts', $this->public, 'enqueue_scripts' );
    $this->loader->add_action( 'wp_enqueue_scripts', $this->public, 'enqueue_styles' );
    $this->loader->add_filter( 'script_loader_tag', $this->public, 'add_module_attribute', 10, 2 );


    // Actions that are defined in the Session Handler class.
    $this->loader->add_action( 'woocommerce_created_customer', $this->session_handler, 'set_customer_by_shop_role' );
    $this->loader->add_action( 'template_redirect', $this->session_handler, 'redirect_user_not_customer_to_home' );
    $this->loader->add_action( 'template_redirect', $this->session_handler, 'redirect_shop_manager_to_dashboard' );
    $this->loader->add_action( 'template_redirect', $this->session_handler, 'restrict_pages' );
    $this->loader->add_action( 'init', $this->session_handler, 'add_endpoints' );
    $this->loader->add_filter( 'query_vars', $this->session_handler, 'add_query_vars', 0 );
    $this->loader->add_filter( 'woocommerce_account_menu_items', $this->session_handler, 'new_menu_items' );
    $this->loader->add_action( 'woocommerce_account_customers_endpoint', $this->session_handler, 'add_content_endpoint' );
    $this->loader->add_action( 'woocommerce_account_orders_vendor_endpoint', $this->session_handler, 'add_content_endpoint' );
    $this->loader->add_action( 'woocommerce_account_analytics_endpoint', $this->session_handler, 'add_content_endpoint' );
    $this->loader->add_action( 'the_title', $this->session_handler, 'endpoint_title' );
    $this->loader->add_filter( 'woocommerce_locate_template', $this->session_handler, 'custom_dashboard_template', 20, 3 );
    $this->loader->add_filter( 'vcpl_dashboard_vendor_cards', $this->session_handler, 'cards_dashboard_vendor' );
    $this->loader->add_filter( 'vcpl_analysis_tables', $this->session_handler, 'analysis_table_items' );
    $this->loader->add_filter( 'woocommerce_checkout_billing', $this->session_handler, 'customer_fields' );
    $this->loader->add_filter( 'woocommerce_checkout_process', $this->session_handler, 'validate_customer_assign_order' );
    $this->loader->add_filter( 'gettext', $this->session_handler, 'change_headline_checkout', 20, 3 );
    $this->loader->add_filter( 'woocommerce_checkout_customer_id', $this->session_handler, 'change_checkout_customer_id' );
    $this->loader->add_filter( 'woocommerce_login_required_message', $this->session_handler, 'change_login_required_message' );
    $this->loader->add_action( 'template_redirect', $this->session_handler, 'woo_custom_redirect_after_purchase' );

    // Actions that are defined in the Form_Handler class.
    $this->loader->add_action( 'template_redirect', $this->form_handler, 'save_new_user' );
    $this->loader->add_action( 'template_redirect', $this->form_handler, 'save_edit_profile' );
    $this->loader->add_action( 'template_redirect', $this->form_handler, 'delete_user' );

  }

  // Run the loader to execute all of the hooks with WordPress.
  public function run(): void
  {
    $this->loader->run();
  }

  /**
   * The reference to the class that orchestrates the hooks with the plugin.
   *
   * @return VC_Profit_Lost
   */
  public static function instance () : VC_Profit_Lost
  {
    return new self();
  }

  /**
   * Admin current page
   *
   * @return string Current page name
   */
  public function get_current_user_page() : string
  {
    global $plugin_page;
    return $plugin_page;
  }

  /**
   * Admin current action page
   *
   * @return string Current action page name
   */
  public function get_current_action_page() : string
  {
    return vcpl_get_var( 'action', false, 'get' );
  }

  /**
   * Current Page Args
   *
   * @return array Args
   */
  public function get_current_user_page_args() : array
  {

    $args = array( 'title' => ucwords( str_replace( '_', ' ', $this->get_current_user_page() ) ) );

    return match ( $this->get_current_user_page() ) {
      'shop_manager' => $args + array( 'roles' => array( 'shop_manager' ) ),
      'vendor'       => $args + array( 'roles' => array( 'vendor' ) ),
      'customer'     => $args + array( 'roles' => array( 'cbs', 'cbv', 'cw' ) ),
      default        => false
    };

  }

  /**
   * Get current user ids to action page
   *
   * @return array ids
   */
  public function get_current_user_id_action_page() : array
  {
    $user_id = vcpl_get_var( 'user_id', strval( false ), 'get' );
    return $user_id ? explode( ',', $user_id ) : array();
  }

}
