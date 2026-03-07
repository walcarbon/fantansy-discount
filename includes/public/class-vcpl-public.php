<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Includes
 * @subpackage Includes/Public
 * @version    1.0.0
 */

namespace Includes\Public;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class VCPL_Public
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

  // Register the styles for the public-facing side of the site.
  public function enqueue_styles(): void
  {
    wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __DIR__ ) ) . 'assets/css/vcpl.css', array(), $this->plugin_version, 'all' );
  }

  /**
   * Register the scripts for the public-facing side of the site.
   *
   * @param string $hook The current page.
   */
  public function enqueue_scripts( string $hook ): void
  {
	wp_enqueue_script( 'font-awesome', 'https://kit.fontawesome.com/1b7a4777b0.js', array(), '6.0.0-beta3', true);  
    wp_enqueue_script( 'selectWoo' );
    wp_enqueue_media();
    wp_enqueue_script( 'password-strength-meter' );
    wp_enqueue_script( 'user-profile' );
    wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __DIR__ ) ) . 'assets/js/vcpl.js', array( 'jquery' ), $this->plugin_version, true );
    wp_localize_script( $this->plugin_name, 'vci18n', array(
      'validate_text_tax_id_ein' => esc_html__( 'Please enter a valid tax id', VCPL_TEXT_DOMAIN ),
    ) );

    if ( is_checkout() ) {
      wp_localize_script( $this->plugin_name, 'customer_checkout', array(
        'customers' => self::get_my_customers_checkout_data(),
      ) );
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
    if ( !in_array( $handle, array( $this->plugin_name ) ) )  return $tag;
    return str_replace( '<script', '<script type="module"', $tag );
  }

  private static function get_my_customers_checkout_data() : array
  {
    return array_reduce(
      vcpl_get_my_customers( wp_get_current_user()->ID ) ?: array(),
      fn ( $customers, $customer ) =>
        $customers + array(
          $customer->ID => array_reduce(
            array( 'billing', 'shipping' ),
            fn ( $data, $type ) =>
              $data + array(
                $type . '_email'      => $customer->user_email,
                $type . '_first_name' => $customer->first_name,
                $type . '_last_name'  => $customer->last_name,
                $type . '_phone'      => get_user_meta( $customer->ID, $type . '_phone', true ),
                $type . '_company'    => get_user_meta( $customer->ID, $type . '_company', true ),
                $type . '_address_1'  => get_user_meta( $customer->ID, $type . '_address_1', true ),
                $type . '_address_2'  => get_user_meta( $customer->ID, $type . '_address_2', true ),
                $type . '_country'    => get_user_meta( $customer->ID, $type . '_country', true ),
                $type . '_city'       => get_user_meta( $customer->ID, $type . '_city', true ),
                $type . '_state'      => get_user_meta( $customer->ID, $type . '_state', true ),
                $type . '_postcode'   => get_user_meta( $customer->ID, $type . '_postcode', true ),
              ),
            array()
          ),
        ),
      array()
    );
  }
}
