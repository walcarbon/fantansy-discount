<?php

/**
 * Methods for handling AJAX requests.
 *
 * @package    Includes
 * @subpackage Includes/Ajax
 * @version    1.0.0
 */

namespace Includes;

defined( 'ABSPATH' ) || exit;

class VCPL_Ajax
{

  // Manage the customer when the vendor is deleted.
  public function actions_before_delete_user(): void
  {

    check_ajax_referer( 'vcpl_nonce', 'nonce' );

    if ( ! current_user_can( 'delete_users' ) ) {
      wp_send_json_error( array( 'message' => __( 'You are not allowed to perform this action.', VCPL_TEXT_DOMAIN ) ), 403 );
    }

    if ( ! isset( $_POST['action'] ) ) {
      wp_send_json_error( array( 'message' => __( 'Invalid request.', VCPL_TEXT_DOMAIN ) ), 403 );
    }

    $current_vendor_id = absint( vcpl_get_var( 'current_vendor_id' ) );
    $new_vendor_id     = absint( vcpl_get_var( 'new_vendor_id' ) );
    $customer_id       = array_values( array_filter( array_map( 'absint', explode( ',', strval( vcpl_get_var( 'customer_id' ) ) ) ) ) );
    $users_delete      = array_values( array_filter( array_map( 'absint', explode( ',', strval( vcpl_get_var( 'users_delete' ) ) ) ) ) );

    if ( $current_vendor_id <= 0 || ! current_user_can( 'delete_user', $current_vendor_id ) ) {
      wp_send_json_error( array( 'message' => __( 'You are not allowed to manage this vendor.', VCPL_TEXT_DOMAIN ) ), 403 );
    }

    $current_user_id = (int) get_current_user_id();
    $is_admin        = current_user_can( 'manage_options' );

    if ( ! $is_admin && $current_vendor_id !== $current_user_id ) {
      wp_send_json_error( array( 'message' => __( 'You are not allowed to manage this vendor.', VCPL_TEXT_DOMAIN ) ), 403 );
    }

    foreach ( $customer_id as $id ) {
      if ( ! current_user_can( 'edit_user', (int) $id ) || (int) get_user_meta( (int) $id, 'vendor', true ) !== (int) $current_vendor_id ) {
        wp_send_json_error( array( 'message' => __( 'You are not allowed to manage one or more selected customers.', VCPL_TEXT_DOMAIN ) ), 403 );
      }
    }

    $args_user_id = array(
      'current_vendor_id' => $current_vendor_id,
      'new_vendor_id'     => $new_vendor_id,
      'customer_id'       => $customer_id,
    );

    $vendor = get_userdata( $current_vendor_id );

    if ( ! $vendor ) {
      wp_send_json_error( array( 'message' => __( 'Invalid vendor.', VCPL_TEXT_DOMAIN ) ), 403 );
    }

    wp_send_json( array(
      'output'      => sprintf(
        '<p class="vcpl-user-delete"><strong>ID:</strong> %1$s | <span class="vcpl-user-delete-title">%2$s</span></p><input type="hidden" name="user_id[]" value="%1$s">',
        $current_vendor_id,
        $vendor->user_login
      ),
      'request'     => vcpl_actions_before_delete_user( vcpl_get_var( 'action_before_delete' ), $args_user_id ),
      'have_customer' => vcpl_validate_users_have_customers( array( $current_vendor_id ) ),
      'enable_submit' => vcpl_validate_users_have_customers( $users_delete )
    ) );

  }

}
