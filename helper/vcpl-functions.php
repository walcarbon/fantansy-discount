<?php
/**
 * Check if the callback returns true for every element of the array.
 *
 * @param  array    $array    Array to check.
 * @param  callable $callback Callback to check.
 * @return bool
 */
if ( ! function_exists( 'vcpl_every' ) ) {

  function vcpl_every ( array $array, callable $callback ) : bool
  {
    return array_reduce( $array, fn ( $carry, $item ) => $carry && $callback( $item ), true );
  }
}

/**
 * Get the value of a variable from the request.
 * If the variable does not exist, return the default value.
 *
 * @param  string $key     Key of the variable.
 * @param  mixed  $default Default value.
 * @return mixed
 */
if ( ! function_exists('vcpl_get_var') ) {

  function vcpl_get_var ( string $key, mixed $default = null, string $request = '', $formatt = '', callable $callback = null, ...$arg_n ): mixed
  {
    $request = strtoupper( $request );

    if ( ! in_array( $request, array( 'GET', 'POST', 'REQUEST', 'COOKIE', '' ) ) ) {
      throw new InvalidArgumentException('Invalid request type.');
    }

    $request = $request ?: 'REQUEST';
    $request = array_map( 'stripslashes_deep', $GLOBALS['_'.$request] );
    $value   = $request[$key] ?? $default;
    $value   = $callback ? $callback( $value, ...$arg_n ) : $value;

    return $formatt ? sprintf( $formatt, $value ) : $value;
  }
}

/**
 * Get the value of a variable from the array.
 * If the variable does not exist, return the default value.
 *
 * @param  string $key     Key of the variable.
 * @param  array  $array   Array of the variable.
 * @param  mixed  $default Default value.
 * @return mixed
 */
if ( ! function_exists( 'vcpl_get_var_array' ) ) {

  function vcpl_get_var_array ( string | int $key, $array, mixed $default = null, $formatt = '', callable $callback = null, ...$arg_n ): mixed
  {
    if ( ! isset( $array[$key] ) ) {
      return $default;
    }

    $value = $callback ? $callback( $array[$key], ...$arg_n ) : $array[$key];

    return $formatt ? sprintf( $formatt, $value ) : $value;
  }
}

/**
 * Check if the user exists.
 *
 * @param  array $user_ids list of the customer ids.
 * @return bool  True if the users exists, false otherwise.
 */
if ( ! function_exists( 'vcpl_user_exists' ) ) {

  function vcpl_user_exists ( int | array $user_id ): bool
  {
    return is_array( $user_id ) ? vcpl_every( $user_id, 'vcpl_user_exists' ) : ( bool ) get_userdata( $user_id );
  }
}

/**
 * Get ID List of the customers of a vendor.
 *
 * @param  int $vendor_id  vendor ID.
 * @return array
 */
if ( ! function_exists( 'vcpl_get_my_customers' ) ) {

  function vcpl_get_my_customers ( int $vendor_id ): array | false
  {
    return array_map( fn( $customer_id ) => get_userdata( $customer_id ), get_user_meta( $vendor_id, 'customers', true ) ?: array() ) ?: false;
  }
}

/**
 * Remove Customer ID of the customers of a vendor.
 *
 * @param  int $customer_id ID of the customer.
 * @param  int $vendor_id   ID of the vendor.
 */
if ( ! function_exists( 'vcpl_delete_my_customer_id' ) ) {

  function vcpl_delete_my_customer_id ( int $customer_id, int $vendor_id ): bool | null
  {
    if ( ! vcpl_user_exists( func_get_args() ) ) {
      trigger_error( 'The user does not exist.', E_USER_WARNING );
    }

    update_user_meta( $vendor_id, 'customers', array_diff( get_user_meta( $vendor_id, 'customers', true ) ?: array(), array( $customer_id ) ) );

    if ( $vendor_id === ( int ) get_user_meta( $customer_id, 'vendor', true ) ) {
      delete_user_meta( $customer_id, 'vendor' );
    }

    return ! in_array( $customer_id, get_user_meta( $vendor_id, 'customers', true ) ?: array() );
  }
}

/**
 * Set Customer ID to the customers of a vendor.
 *
 * @param  int $customer_id The id of the customer.
 * @param  int $vendor_id The id of the vendor.
 */
if ( ! function_exists( 'vcpl_set_my_customer_id' ) ) {

  function vcpl_set_my_customer_id ( int $customer_id, int $vendor_id ): bool
  {

    if ( ! vcpl_user_exists( func_get_args() ) ) {
      trigger_error( 'The user does not exist.', E_USER_WARNING );
    }

    $customer_ids = get_user_meta( $vendor_id, 'customers', true ) ?: array();

    if ( ! in_array( $customer_id, $customer_ids ) ) {
      $customer_ids[] = $customer_id;
    }

    update_user_meta( $vendor_id, 'customers', $customer_ids );

    if ( $vendor_id !== ( int ) get_user_meta( $customer_id, 'vendor', true ) ) {
      update_user_meta( $customer_id, 'vendor', $vendor_id );
    }

    return in_array( $customer_id, get_user_meta( $vendor_id, 'customers', true ) ?: array() );
  }
}

/**
 * Change a customer's vendor
 *
 * @param  int $customer_id       Customer ID
 * @param  int $current_vendor_id Current Vendor ID
 * @param  int $new_vendor_id     New Vendor ID
 */
if ( ! function_exists( 'vcpl_change_vendor' ) ) {

  function vcpl_change_vendor ( int $customer_id, int $current_vendor_id, int $new_vendor_id ): bool
  {
    if ( ! vcpl_user_exists( array( $customer_id ) ) ) {
      trigger_error( 'The user does not exist.', E_USER_WARNING );
    }

    if ( vcpl_user_exists( array( $current_vendor_id ) ) ) {
      vcpl_delete_my_customer_id( $customer_id, $current_vendor_id );
    }

    if ( vcpl_user_exists( array( $new_vendor_id ) ) ) {
      vcpl_set_my_customer_id( $customer_id, $new_vendor_id );
    }
    return ! in_array( $customer_id, get_user_meta( $current_vendor_id, 'customers', true ) ?: array() ) && in_array( $customer_id, get_user_meta( $new_vendor_id, 'customers', true ) ?: array() );
  }
}

/**
 * Check if the key is a valid user argument.
 *
 * @param  string $key Key to check.
 * @return bool
 */
if ( ! function_exists( 'is_valid_wp_user_arg' ) ) {

  function is_valid_wp_user_arg ( string $key ) {

    return in_array( $key, array(
      'ID', 'user_pass', 'user_login', 'user_nicename', 'user_url', 'user_email',
      'display_name', 'nickname', 'first_name', 'last_name', 'description',
      'rich_editing', 'user_registered', 'role', 'jabber', 'aim', 'yim',
      'show_admin_bar_front', 'comment_shortcuts', 'admin_color', 'use_ssl',
      'user_activation_key', 'spam', 'deleted'
    ) );

  }

}

/**
 * Get the image profile URL of the user.
 *
 * @param  int $user_id User ID
 * @return string
 */
if ( ! function_exists( 'vcpl_get_img_profile_url' ) ) {

  function vcpl_get_img_profile_url ( int $user_id ): string
  {

    $image_id  = get_user_meta( $user_id, 'img_profile', true );
    $image_src = wp_get_attachment_image_src( $image_id, 'thumbnail' );
    $image_url = vcpl_get_var_array( 0, $image_src, get_avatar_url( false,  array( 'default' => 'mm' ) ) );

    return $image_url;

  }

}

/**
 * Get Available vendors selects
 *
 * @param  string id select
 * @param  string label
 * @return html select
 *
 */
if ( ! function_exists( 'vcpl_wp_select_available_vendors_to_change' ) ) {

  function vcpl_wp_select_available_vendors_to_change ( array $args ): string
  {

    extract( $args, EXTR_OVERWRITE );

    ob_start();
    woocommerce_wp_select( array(
      'id'      => $id ?? 'available_vendors',
      'label'   => $label ?? strval( false ),
      'options' => array_reduce(
        get_users( array( 'role__in' => array( 'vendor' ),  'exclude'  => VCPL()->get_current_user_id_action_page() ) ),
        fn ( $carry, $vendor ) => $carry +  array( $vendor->ID => $vendor->user_login ) ,
        array( "" => __( 'Select a vendor ', VCPL_TEXT_DOMAIN ) )
      )
    ) );
    ?>
      <button class="button js-vcpl-customers" data-action="change_vendor" data-customer-id="<?= $customer_id ?? null; ?>" data-current-vendor-id="<?= $vendor_id; ?>">
        <?= esc_html__( 'Apply', VCPL_TEXT_DOMAIN ); ?>
      </button>
    <?php

    return ob_get_clean();

  }

}

/**
 * Inputs radio to select an option for customer
 *
 * @param  int   $user_id
 * @return array output inputs
 */
if ( ! function_exists( 'vcpl_wp_radio_actions_before_delete_user' ) ) {

  function vcpl_wp_radio_actions_before_delete_user( int $user_id ) : string
  {

    $actions = array(
      'diff_vendors'  => 'add customers to different vendors',
      'single_vendor' => 'add all customers to the same vendor',
      'delete_all'    => 'delete vendor with its customers'
    );

    return implode( strval( false ), array_map(
      fn ( $action, $label ) =>
        "<label>
          <input type='radio' name='actions-before-delete[{$user_id}]' value='{$action}' " . ( $action === 'diff_vendors' ? 'checked' : strval( false ) ) . ">"
          . esc_html__( $label, VCPL_TEXT_DOMAIN ) .
        "</label>",
      array_keys( $actions ),
      $actions
    ) );

  }

}

/**
 * Get the dependencies of the action
 *
 * @param  int    $user_id
 * @param  string $action
 * @return string json data dependency
 */
if ( ! function_exists( 'vcpl_wp_actions_before_delete_user_dep' ) ) {

  function vcpl_wp_actions_before_delete_user_dep( int $user_id, string $action )
  {

    return json_encode(
      array( array( 'name' => "actions-before-delete[{$user_id}]", 'value' => $action ) )
    );

  }

}

/**
 * Execute the action before deleting the user
 *
 * @param  string $action
 * @param  array  $args_user_id
 * @return array
 */
if ( ! function_exists( 'vcpl_actions_before_delete_user' ) ) {

  function vcpl_actions_before_delete_user( string $action, array $args_user_id ) : array
  {

    extract( $args_user_id, EXTR_OVERWRITE );

    return array_reduce(
      $customer_id ?? array(),
      function( $notices, $user_id ) use ( $action, $current_vendor_id, $new_vendor_id ) {

        $user = get_userdata( $user_id );

        list( $happened, $message ) = match ( $action ) {
          'change_vendor'   => array( vcpl_change_vendor( $user_id, $current_vendor_id, $new_vendor_id ), 'assigned to the vendor' ),
          'delete_customer' => array( wp_delete_user( ( int ) $user_id ), 'deleted from vendor' ),
          default           => array( false, strval( false ) )
        };

        if ( $action === 'delete_customer' && $happened ) {
          vcpl_delete_my_customer_id( $user_id, $current_vendor_id );
        }

        $notice = array(
          'happened' => $happened,
          'notice'   => sprintf( '<div class="notice notice-%s is-dismissible">
                                    <p><strong>%s:</strong><strong style=\'color:#2271b1\'> %s</strong> has %s been %s %s</p>
                                    <button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button>
                                  </div>',
            $happened ? 'success' : 'error',
            esc_html__( 'Customer', VCPL_TEXT_DOMAIN ),
            $user->user_login,
            $happened ? 'successfully' : 'not',
            $message,
            get_userdata( $new_vendor_id ?? $current_vendor_id )->user_login
          )
        );

        return array_merge( $notices, array( $notice ) );

      },
      array()
    );

  }

}

/**
 * Validate if the users have customers
 *
 * @param  array $user_ids
 * @return bool
 */
if ( ! function_exists( 'vcpl_validate_users_have_customers' ) ) {

  function vcpl_validate_users_have_customers( array $user_ids ) : bool
  {
    return vcpl_every( $user_ids, fn( $user_id ) => count( vcpl_get_my_customers( $user_id ) ?: array() ) > 0 );
  }

}

/**
 * Get the form field
 *
 * @param  string $key
 * @param  array  $args
 * @param  mixed  $value
 * @return string
 */
if ( ! function_exists( 'get_woocommerce_form_field' ) ) {

  function get_woocommerce_form_field( $key, $args, $value = null ) : string
  {

    ob_start();
    woocommerce_form_field($key, $args, $value);
    return ob_get_clean();

  }

}

/**
 * Get the total purchases of the customers
 *
 * @param  int $vendor_id
 * @return float
 */
if ( ! function_exists( 'vcpl_get_my_customers_total_purchases' ) ) {

  function vcpl_get_my_customers_total_purchases ( int $vendor_id ) : int
  {
    $orders = wc_get_orders( array(
      'limit'    => -1,
      'customer' => get_user_meta( $vendor_id, 'customers', true ) ?: false,
      'status'   => 'completed'
    ) );

    return array_reduce( $orders, fn( $carry, $order ) => $carry + $order->get_total(), 0 );
  }

}

/**
 * Get the total sales of the customers today
 *
 * @param  int $vendor_id
 * @return float
 */
if ( ! function_exists( 'vcpl_get_my_customers_sales_today' ) ) {

  function vcpl_get_my_customers_sales_today( int $vendor_id ) : int
  {
    $orders = wc_get_orders( array(
      'limit'        => -1,
      'date_created' => date('Y-m-d', current_time('timestamp')),
      'customer'     => get_user_meta( $vendor_id, 'customers', true ) ?: false
    ) );

    return array_reduce( $orders, fn( $carry, $order ) => $carry + $order->get_total(), 0 );
  }

}

/**
 * Get the number of orders of the customers
 *
 * @param  int $vendor_id
 * @return int
 */
if ( ! function_exists( 'vcpl_get_number_my_customers_sales' ) ) {

  function vcpl_get_number_my_customers_sales( int $vendor_id ) : int
  {
      $orders = wc_get_orders( array(
        'limit'    => -1,
        'customer' => get_user_meta( $vendor_id, 'customers', true ) ?: false,
        'status'   => 'completed'
      ) );

      return count( $orders );
  }

}

/**
 * Get the number of orders of the customers today
 *
 * @param  int $vendor_id
 * @return int
 */
if ( ! function_exists( 'vcpl_get_number_my_customers_sales_today' ) ) {

  function vcpl_get_number_my_customers_sales_today( int $vendor_id ) : int
  {
      $orders = wc_get_orders( array(
        'limit'        => -1,
        'customer'     => get_user_meta( $vendor_id, 'customers', true ) ?: false,
        'date_created' => date('Y-m-d', current_time('timestamp')),
      ) );

      return count( $orders );
  }

}

/**
 * Get my top customers
 *
 * @param  int $vendor_id
 * @param  int $limit
 * @return array
 */
if ( ! function_exists( 'vcpl_get_my_top_customers' ) ) {

  function vcpl_get_my_top_customers( int $vendor_id, int $limit )
  {
    $orders = wc_get_orders(array(
      'limit' => -1,
      'customer' => get_user_meta($vendor_id, 'customers', true) ?: false,
    ) );

    $order_counts = array();

    foreach ( $orders as $order ) {
        $customer_id = $order->get_customer_id();
        if ( ! isset( $order_counts[$customer_id] ) ) {
          $order_counts[$customer_id] = 0;
        }
        $order_counts[$customer_id]++;
    }

    arsort( $order_counts );

    $top_customers = array_slice( $order_counts, 0, $limit, true );

    return $top_customers;
  }

}

/**
 * Get the commission percent by date
 *
 * @param  string $date
 * @param  object $vendor
 * @return float
 */
if ( ! function_exists( 'vcpl_get_commission_percent_by_date' ) ) {

  function vcpl_get_commission_percent_by_date( string $date, object $vendor ) : float
  {
    // Obtiene la comisión del meta dato del usuario
    $commission = $vendor instanceof \WP_User ? get_user_meta( $vendor->ID, 'commission', true ) : [];

    // Verifica si la comisión es un arreglo
    if ( $vendor && is_array( $commission ) && !empty( $commission ) ) {
        // Ordena la comisión por la fecha más cercana a la fecha dada
        usort( $commission, function( $a, $b ) use ( $date ) {
            $timeA = abs( strtotime( $date ) - strtotime( $a['date'] ) );
            $timeB = abs( strtotime( $date ) - strtotime( $b['date'] ) );
            return $timeA - $timeB;
        } );

        // Retorna el valor de la comisión más cercana
        return (float) $commission[0]['value'];
    }

    // Si no existe comisión, retorna 0
    return 0.00;
  }
}


/**
 * Get the commission of the vendor by date
 *
 * @param  object $vendor
 * @param  object $order
 * @return float
 */
if ( ! function_exists( 'vcpl_get_my_commission_amount' ) ) {

  function vcpl_get_my_commission_amount( object $vendor, object $order ) : float
  {
    return number_format( ( float ) ( $order->get_total() * vcpl_get_commission_percent_by_date( ( $order->get_date_created() )->format('Y-m-d H:i:s'), $vendor ) / 100 ), 2, '.', '' );
  }
}

/**
 * Get all users with the 'vendor' role.
 * This is an optimized function that queries users directly.
 * NOTE: This assumes your vendors have a user role named 'vendor'.
 * If not, adjust the 'role' parameter accordingly.
 *
 * @return array Array of WP_User objects.
 */
if ( ! function_exists( 'vcpl_get_all_vendors' ) ) {
    function vcpl_get_all_vendors() {
        $vendors = get_users( array(
            'role'    => 'vendor', // <-- ¡IMPORTANTE! Asegúrate de que este sea el nombre de rol correcto para tus vendedores.
            'orderby' => 'display_name',
            'order'   => 'ASC',
        ) );

        return $vendors;
    }
}



/**
 * Get current feature vendor
 *
 * @return float
 */
if ( ! function_exists( 'vcpl_get_current_feature_vendor' ) ) {

  function vcpl_get_current_feature_vendor( int $vendor_id, string $feature ) : float
  {
    $feature = array_slice( get_user_meta( $vendor_id, $feature, true ) ?: array(), -1)[0] ?? false;
    return empty( $feature ) || ! isset( $feature['value'] )
      ? '0.00'
      : number_format( ( float ) $feature['value'], 2, '.', '' );
  }
}
