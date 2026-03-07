<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Includes
 * @subpackage Includes/Loader
 * @version    1.0.0
 */

namespace Includes\Admin;

if ( !defined( 'ABSPATH' ) ) {
  exit;
}

class VCPL_Admin
{
  /**
   * The name of the plugin.
   *
   * @var string
   */
  private $plugin_name;

  /**
   * The version of the plugin.
   *
   * @var string
   */
  private $plugin_version;

  /**
   * Initialize the class
   *
   * @param string $plugin_name The name of the plugin.
   * @param string $plugin_version The version of the plugin.
   */
  public function __construct( string $plugin_name, string $plugin_version )
  {
    $this->plugin_name = $plugin_name;
    $this->plugin_version = $plugin_version;
  }

  // Register the stylesheets for the admin area.
  public function enqueue_styles(): void
  {
    wp_enqueue_style( 'woocommerce_admin_styles' );
    wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __DIR__ ) ) . 'assets/css/vcpl-admin.css', array(), $this->plugin_version, 'all' );
  }

  public function enqueue_scripts( string $hook ): void
  {
    wp_enqueue_script( 'selectWoo' );
    wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __DIR__ ) ) . 'assets/js/vcpl-admin.js', array( 'jquery' ), $this->plugin_version, true );

    wp_localize_script( $this->plugin_name, 'vc', array(
      'ajax_url' => admin_url( 'admin-ajax.php' ),
      'nonce'    => wp_create_nonce( 'vcpl_nonce' ),
      'hook'     => $hook
    ) );

    wp_localize_script( $this->plugin_name, 'vci18n', array(
      'validate_text_shipping_zone' => esc_html__( 'Please select a shipping zone', VCPL_TEXT_DOMAIN ),
      'validate_text_amt_over_100'  => esc_html__( 'The percentage must be less than or equal to 100', VCPL_TEXT_DOMAIN ),
      'validate_text_lt_zero'       => esc_html__( 'The amount must be greater than or equal to 0', VCPL_TEXT_DOMAIN ),
      'validate_text_tax_id_ein'    => esc_html__( 'Please enter a valid tax id', VCPL_TEXT_DOMAIN ),
    ) );

    if ( preg_match( '/toplevel_page_(vendor|customer|shop_manager)/', $hook ) ) {

      wp_enqueue_media();
      wp_enqueue_script( 'password-strength-meter' );
      wp_enqueue_script( 'user-profile' );
      wp_enqueue_script( 'wc-enhanced-select' );
      wp_enqueue_script( 'wc-users', WC()->plugin_url() . '/assets/js/admin/users.js', array( 'jquery', 'wc-enhanced-select', 'selectWoo' ), $this->plugin_version, true );

      wp_localize_script(
        'wc-users',
        'wc_users_params',
        array(
          'countries'              => wp_json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
          'i18n_select_state_text' => esc_attr__( 'Select an option&hellip;', VCPL_TEXT_DOMAIN ),
        )
      );

    }
  }

  /**
   * Add module attribute to script tags.
   *
   * @param string $tag The script tag.
   * @param string $handle The script handle.
   * @return string The modified script tag.
   */
  public function add_module_attribute( string $tag, string $handle ): string
  {
    if ( !in_array( $handle, array( 'syf-script', $this->plugin_name ) ) )  return $tag;
    return str_replace( '<script', '<script type="module"', $tag );
  }
}
