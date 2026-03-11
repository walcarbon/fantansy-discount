<?php

/**
 * This file is read by WordPress to generate the plugin information in the plugin administration area.
 * This file also includes all the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link
 * @since 1.0.0
 * @package plm
 *
 * Plugin Name: Vc Profit Lost
 * Plugin URI:
 * Description: Addons for the Woocommerce plugin. Manage your store by creating sellers and getting a profit and loss report.
 * Version: 1.0.0.1
 * Requires at least: 5.2
 * Requires PHP:7.2
 * Author: Santil Darwin
 * Author URI:
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:
 * Text Domain: vcpl
 * Domain Path: /i18n
 */

// If WPINC is not defined, we terminate the script execution.
if ( !defined( 'WPINC' ) ) {
  die;
}

// We define the text domain for the plugin translations.
if ( !defined( 'VCPL_TEXT_DOMAIN' ) ) {
  define( 'VCPL_TEXT_DOMAIN', 'vcpl' );
}

// We define the plugin version.
if ( !defined( 'VCPL_VERSION' ) ) define( 'VCPL_VERSION', '1.0.0.1' );

// We define the plugin file path.
if ( !defined( 'VCPL_PLUGIN_FILE' ) ) define( 'VCPL_PLUGIN_FILE', __FILE__ );


// We include the Composer autoloader to load the plugin dependencies.
require_once plugin_dir_path( VCPL_PLUGIN_FILE ) . 'autoload.php';

//We import the necessary classes for the plugin operation.
use Includes\VC_Profit_Lost;
use Includes\VCPL_Activator;
use Includes\VCPL_Deactivator;

/**
 * VCPL Object
 *
 * @return VC_Profit_Lost
 */
if ( ! function_exists( 'VCPL' ) ) {

  function VCPL() {
    return VC_Profit_Lost::instance();
  }

}

// We define the function to load the plugin.
if ( !function_exists( 'vcpl_load_plugin' ) ) {

  function vcpl_load_plugin()
  {
    // We load the plugin text domain for translations.
    load_plugin_textdomain( VCPL_TEXT_DOMAIN );

    // If WooCommerce is not loaded, we display a notice in the admin panel.
    if ( !did_action( 'woocommerce_loaded' ) ) {
      add_action( 'admin_notices', 'vcpl_fail_load' );
      return;
    } else {

      // If WooCommerce is loaded, we start the plugin.
      vcpl_run_plugin();

    }
  }

  // We add the plugin load function to the 'plugins_loaded' hook.
  add_action( 'plugins_loaded', 'vcpl_load_plugin' );

}

// We define the function to display the notice in case WooCommerce is not activated.
if ( !function_exists( 'vcpl_fail_load' ) ) {
  function vcpl_fail_load()
  {
    $screen = get_current_screen();
    if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) return;

    $woo = 'woocommerce/woocommerce.php';

    if ( !current_user_can( 'activate_plugins' ) ) return;
    if ( _is_woocommerce_installed() ) {
      $error = esc_html__( 'Vc Profit Lost requires the activation of Woocommerce plugin', VCPL_TEXT_DOMAIN );
      $description = esc_html__( 'Please activate the Woocommerce plugin', VCPL_TEXT_DOMAIN );
      $action = sprintf(
        '<a href="%s" class="button-primary">%s</a>',
        wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $woo . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $woo ),
        esc_html__( 'Activate Now', VCPL_TEXT_DOMAIN )
      );
    } else {
      $error = esc_html__( 'Vc Profit Lost requires the installation and activation of Woocommerce plugin', VCPL_TEXT_DOMAIN );
      $description = esc_html__( 'Please install and activate the Woocommerce plugin', VCPL_TEXT_DOMAIN );
      $action = sprintf(
        '<a href="%s" class="button-primary">%s</a>',
        wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' ),
        esc_html__( 'Install Now', VCPL_TEXT_DOMAIN )
      );
    }

    printf( '<div class="error"><h3>%s</h3><p>%s</p><p>%s</p></div>', $error, $description, $action );
  }
}

// We define the function to check if WooCommerce is installed.
if ( !function_exists( '_is_woocommerce_installed' ) ) {
  function _is_woocommerce_installed()
  {
    return isset( get_plugins()[ 'woocommerce/woocommerce.php' ] );
  }
}

// We define the function to redirect if the plugin is active.
if ( !function_exists( 'redirect_if_plugin_active' ) ) {
  function redirect_if_plugin_active()
  {
    if ( ! is_admin() || ! current_user_can( 'activate_plugins' ) ) {
      return;
    }

    global $pagenow;

    $is_woo_install_request = 'update.php' === $pagenow
      && 'install-plugin' === vcpl_get_var( 'action', false, 'get' )
      && 'woocommerce' === vcpl_get_var( 'plugin', false, 'get' );

    if ( $is_woo_install_request && is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
      wp_safe_redirect( admin_url( 'plugins.php' ) );
      exit;
    }
  }

  add_action( 'admin_init', 'redirect_if_plugin_active' );
}

// We register the plugin activation function.
register_activation_hook( VCPL_PLUGIN_FILE, function () {
  VCPL_Activator::activate();
} );

// We register the plugin deactivation function.
register_deactivation_hook( VCPL_PLUGIN_FILE, function () {
  VCPL_Deactivator::deactivate();
} );

// We define the function to start the plugin.
if ( ! function_exists( 'vcpl_run_plugin' ) ) {

  function vcpl_run_plugin()
  {
    VC_Profit_Lost::instance()->run();
  }

}

