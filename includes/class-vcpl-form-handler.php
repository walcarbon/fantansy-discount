<?php

/**
 * Handles form submissions.
 *
 * @package    MetricsSalesSystem
 * @subpackage MetricsSalesSystem/includes
 * @version    1.0.0
 */

namespace Includes;

class VCPL_Form_Handler
{

  /**
   * validation user action form
   *
   * @param  string $nonce_action The nonce action.
   * @return array|bool
   */
  public function validate_user_action_form ( string $nonce_action ) : array | bool
  {

    $nonce_value = vcpl_get_var( "vcpl_{$nonce_action}_nonce", strval( false ), 'post' );

    if ( ! vcpl_get_var( 'action', false, 'post' ) || ! wp_verify_nonce( $nonce_value, "vcpl_{$nonce_action}" ) ) {
      return false;
    }

    $this->is_tax_idein_request_unique();

    return $this->sanitize_data( apply_filters( 'vcpl_userdata', $this->get_wp_userdata( $_POST ) ) );

  }

  // Create new user and save data
  public function save_new_user(): void
  {

    $userdata = $this->validate_user_action_form( 'new_user' );

    if ( ! $userdata ) {
      return;
    }

    if ( ! $this->can_manage_user_from_context( 0 ) ) {
      $this->deny_access();
      return;
    }

    do_action( 'vcpl_save_new_user', $userdata );

    $user_id  = wp_insert_user( $userdata );
    $is_error = is_wp_error( $user_id );
    $user     = get_userdata( $user_id );

    if( ! $is_error ) {
      $this->update_user_meta( $user_id, $userdata );
    }

    list( $notice_type, $notice_message, $url ) = match ( $is_error ) {
      true  => array( 'error', $user_id->get_error_messages()[0], null ),
      false => array( 'success', "<strong>{$user->user_login}</strong> has been created successfully.", remove_query_arg( array( 'action' ) ) )
    };

    if ( is_admin() ) {
      $this->notice_form( array( 'type' => $notice_type, 'notice' => $notice_message ), $url );
    } else {

      wc_add_notice( $notice_message, $notice_type );

      if ( ! is_null( $url ) ) {
        wp_safe_redirect( $url );
        exit;
      }

    }

  }

  // Update user and save data
  public function save_edit_profile(): void
  {

    $userdata = $this->validate_user_action_form( 'edit_profile' );

    if ( ! $userdata ) {
      return;
    }

    $target_user_id = (int) ( $userdata['ID'] ?? 0 );

    if ( ! $this->can_manage_user_from_context( $target_user_id ) ) {
      $this->deny_access();
      return;
    }

    do_action( 'vcpl_update_profile', $userdata );

    $user_id  = wp_update_user( $userdata );
    $is_error = is_wp_error( $user_id );
    $user     = get_userdata( $user_id );

    if( ! is_wp_error( $user_id ) ) {
      $this->update_user_meta( $user_id, $userdata );
    }

    list( $notice_type, $notice_message ) = match ( $is_error ) {
      true  => array( 'error', $user_id->get_error_messages()[0] ),
      false => array( 'success', "<strong>{$user->user_login}</strong> has been updated successfully." )
    };

    if ( is_admin() ) {
      $this->notice_form( array( 'type' => $notice_type, 'notice' => $notice_message ) );
    } else {
      wc_add_notice( $notice_message, $notice_type  );
      wp_safe_redirect( vcpl_get_var( '_wp_http_referer' ) );
    }

  }

  // Delete user
  public function delete_user(): void
  {

    global $wp_roles;

    require_once ABSPATH . 'wp-admin/includes/user.php';

    if ( ! isset( $wp_roles ) ) {
      $wp_roles = new \WP_Roles();
    }

    if ( $this->validate_user_action_form( 'delete_user' ) === false ) {
      return;
    }

    if ( ! current_user_can( 'delete_users' ) || ( is_admin() && ! current_user_can( 'manage_options' ) ) ) {
      $this->deny_access();
      return;
    }

    $notices = array();
    foreach ( array_map( 'intval', vcpl_get_var( 'user_id', array(), 'post' ) ) as $user_id ) {

      if ( ! $this->can_manage_user_from_context( $user_id ) ) {
        $this->deny_access();
        return;
      }

      if ( vcpl_get_var( 'actions-before-delete' )[$user_id] ?? strval( false ) === 'delete_all' ) {
        $arr = array_merge( array( $user_id ), get_user_meta( $user_id, 'customers', true ) ?: array() );
      }

      if ( trim( implode( '', get_userdata( ( int ) $user_id )->roles ) ) === 'cbv' ) {
        vcpl_delete_my_customer_id( ( int ) $user_id, ( int ) get_user_meta( $user_id, 'vendor', true ) );
      }

      foreach( $arr ?? array( $user_id ) as $id ) {

        $user = get_userdata( $id );

        $is_error = wp_delete_user( $id );

        $notices = array_merge( $notices, array( array(
          'type'   => ! $is_error ? 'error' : 'success',
          'notice' => "<strong>{$wp_roles->roles[trim( implode( '', $user->roles ) )]['name']}: </strong>
                        <strong style='color:#2271b1'>{$user->user_login}</strong>"
                        . sprintf ( esc_html__( ' has %s been deleted.', VCPL_TEXT_DOMAIN ), ! $is_error ? 'not' : '' ),
          )
        ) );

      }

    }

    if ( is_admin() ) {
      $this->notice_form( $notices, remove_query_arg( array( 'action', 'user_id' ) ), true );
    } else {
      foreach ( $notices as $notice ) {
        wc_add_notice( $notice['notice'], $notice['type'] );
      }
      wp_safe_redirect( remove_query_arg( array( 'action', 'user_id' ) ) );
    }

  }


  private function deny_access( string $message = '' ): void
  {
    $notice = $message ?: __( 'You are not allowed to perform this action.', VCPL_TEXT_DOMAIN );

    if ( is_admin() ) {
      $this->notice_form( array( 'type' => 'error', 'notice' => $notice ) );
    } else {
      wc_add_notice( $notice, 'error' );
      wp_safe_redirect( vcpl_get_var( '_wp_http_referer' ) ?: remove_query_arg( array( 'action', 'user_id' ) ) );
      exit;
    }
  }

  private function can_manage_user_from_context( int $user_id ): bool
  {
    if ( is_admin() ) {
      return current_user_can( 'manage_options' );
    }

    $current_user = wp_get_current_user();

    if ( ! is_user_logged_in() || ! in_array( 'vendor', $current_user->roles ?: array(), true ) ) {
      return false;
    }

    if ( $user_id <= 0 ) {
      return true;
    }

    if ( (int) get_user_meta( $user_id, 'vendor', true ) === (int) $current_user->ID ) {
      return true;
    }

    return in_array( $user_id, array_map( fn( $customer ) => (int) $customer->ID, vcpl_get_my_customers( $current_user->ID ) ?: array() ), true );
  }

  /**
   * Update the user meta.
   *
   * @param int   $user_id The user ID.
   * @param array $usermeta The user meta.
   */
  private function update_user_meta( int $user_id, array $post_request ): void
  {

    $userdata  = array_filter( $post_request, fn( $key ) => ! is_valid_wp_user_arg ( $key ) || 'role' === $key, ARRAY_FILTER_USE_KEY );
    $vendor_id = get_user_meta( $user_id, 'vendor', true );

    foreach ( $userdata as $key => $value ) {
      if ( 'vendor' === $key ) {
        vcpl_change_vendor( (int) $user_id, (int) $vendor_id, (int) $value );
      } elseif ( 'role' === $key && ! empty( $vendor_id ) && 'cbv' !== $value ) {
        vcpl_delete_my_customer_id( $user_id, (int) $vendor_id );
        delete_user_meta( $user_id, 'vendor' );
      } elseif ( 'role' === $key && 'vendor' === $value ) {
        $customer_ids = get_user_meta( $user_id, 'customers', true );

        foreach( $customer_ids as $key => $customer_id ) {
          if ( ! get_userdata( $customer_id ) ) {
            unset( $customer_ids[$key] );
          }
        }
      } elseif ( 'commission' === $key || 'salary' === $key ) {
        $date    = date( 'Y-m-d H:i:s' );
        $current = get_user_meta( $user_id, $key, true ) ?: array();

        if ( ! empty( $current ) && ! is_array( $current ) ) {
          $current = array();
        }

        // if ( is_array( $current ) && isset( end( $current )['value'] ) && intval( end( $current )['value'] ) === intval( $value ) ) {
        //   return;
        // }

        $value = array_merge( $current, array( array( 'value' => intval( $value ), 'date' => $date ) ) );

      }

      update_user_meta( $user_id, $key, $value );

    }
  }



  /**
   * Get the user data.
   *
   * @param  array $request_post The request post.
   * @return array
   */
  private function get_wp_userdata( array $post_request ): array
  {

    $meta_keys = array( 'ID', 'user_login', 'user_pass', 'user_email', 'user_url',
      'role', 'first_name', 'last_name', 'description', 'img_profile', 'vendor',
      'tax_id_ein', 'salary', 'commission', 'salary'
    );

    foreach ( array( 'shipping', 'billing' ) as $prefix ) {
      $meta_keys = array_merge( $meta_keys, array_map( fn ( $key ) => "{$prefix}_{$key}", array(
        'company', 'address_1', 'address_2', 'city',
        'state', 'postcode', 'country', 'phone'
      ) ) );
    }

    $userdata = array_reduce(
      $meta_keys,
      fn ( $arr, $key ) => $arr + array( $key => vcpl_get_var_array( $key, $post_request ) ),
      array()
    );

    return array_filter( $userdata, fn ( $metadata ) => ! is_null( $metadata ) );

  }

  // Check if the tax id ein is unique.
  public function is_tax_idein_request_unique(): void
  {

    $tax_id_ein = vcpl_get_var( 'tax_id_ein' );

    if ( ! is_null( $tax_id_ein ) ) {

      $tax_id_ein = sanitize_text_field( trim( $tax_id_ein ) );

      $users_with_tax = get_users( array(
        'meta_key'   => 'tax_id_ein',
        'meta_value' => $tax_id_ein
      ) );

      if ( count( $users_with_tax ) > 1 ) {
        $this->notice_form( array(
          'type'   => 'error',
          'notice' => __( 'The tax id ein is not unique.', VCPL_TEXT_DOMAIN )
        )  );
      }

    }

  }

  /**
   * Sanitize the data of the form.
   *
   * @param  mixed $item The data of the form.
   * @return mixed
   */
  public function sanitize_data( mixed $item ): mixed
  {
    return is_string( $item )
      ? sanitize_text_field( trim( $item ) )
      : ( is_array( $item )
        ? array_map( array( $this, 'sanitize_data' ), $item )
        : $item
      );
  }

  /**
   * Notice to the form.
   *
   * @param  string $type The type of the notice.
   * @param  string $msg The message of the notice.
   * @return void
   */
  private function notice_form( array $args, string | null $http_referer = null, bool $multi_notice = false ): void
  {
    if ( empty( $args ) ) {
      return;
    }

    if ( ! session_id() && ! headers_sent() ) {
      @session_start();
    }

    $formmatter = fn( array $notice )=> sprintf(
      '<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
      $notice['type'],
      $notice['notice']
    );

    if ( session_id() ) {
      $_SESSION['notice'] = $multi_notice
        ? implode( ' ', array_map( fn( array $notice ) => $formmatter( $notice ), $args ) )
        : $formmatter( $args );
    }

    wp_safe_redirect( $http_referer ?? vcpl_get_var( '_wp_http_referer' ) );
    exit;

  }

}
