<?php

/**
 * All Metas for the users.
 *
 * @package    Includes
 * @subpackage Includes/Usermeta
 * @version    1.0.0
 */

namespace Includes\Admin;

if ( !defined( 'ABSPATH' ) ) {
  exit;
}

class VCPL_Admin_Profile
{

  /**
   * Get user's meta fields
   *
   * @param  array $fields fields to apply filter
   * @return array fields
   */
  public function get_user_meta_fields ( array $fields ) : array
  {

    $current_user_page = VCPL()->get_current_user_page();

    $meta_fields = apply_filters( 'vcpl_user_meta_fields', array(
      'profile' => array( 'title'  => __( 'Profile Information', VCPL_TEXT_DOMAIN ), 'fields' => $this->get_profile_meta_fields() )
    ) );

    if ( method_exists( $this, 'get_' . $current_user_page . '_meta_fields' ) ) {
      $meta_fields[$current_user_page] = array(
        'title'  => __( ucfirst($current_user_page) . ' Information', VCPL_TEXT_DOMAIN ),
        'fields' => $this->{'get_' . $current_user_page . '_meta_fields'}()
      );
    }

    if ( $current_user_page === 'customer' ) {

      foreach ( array( 'billing', 'shipping' ) as $extra_field ) {

        if ( method_exists($this, 'get_' . $extra_field . '_meta_fields' ) ) {

          $meta_fields[$extra_field] = array(
            'title'  => __( ucfirst( $extra_field ) . ' Address', VCPL_TEXT_DOMAIN ),
            'fields' => $this->{'get_' . $extra_field . '_meta_fields'}(),
            'id'     => 'fieldset-' . $extra_field
          );

        }

      }
    }

    // Adjust meta fields in edit user action
    if ( VCPL()->get_current_action_page() === 'edit_user' ) {

      foreach ( $meta_fields as $key => &$args ) {

        switch ( $key ) {
          case 'profile' :
            $args['fields'] = array_merge( $args['fields'], array(
              'reset_pass' => array(
                'label' => __( 'Reset Password', VCPL_TEXT_DOMAIN ),
                'type'  => 'button',
                'extra' => self::output_reset_password(),
                'tr_class' => array( 'user-generate-reset-link-wrap hide-if-no-js' ),
                'description' => sprintf(
                  __( 'Send %s a link to reset their password. This will not change their password, nor will it force a change.', VCPL_TEXT_DOMAIN ),
                  esc_html( get_userdata( VCPL()->get_current_user_id_action_page() )->display_name ?? '' )
                ),
              )
            ) );
            break;
          case 'vendor' :
            $args[ 'fields' ] = array_merge(  $args[ 'fields' ], array(
              'customers' => array(
                'label' => __( 'Customers', VCPL_TEXT_DOMAIN ),
                'type'  => 'button',
                'extra' => self::output_customers( ( int ) VCPL()->get_current_user_id_action_page()[0] ),
              )
            ) );
            break;
        }

      }


      $meta_fields = $this->adjust_field_value( $meta_fields );

    }

    return array_merge( $fields, $meta_fields );

  }

  /**
   * Profile meta fields.
   *
   * @return array
   */
  public function get_profile_meta_fields () {

    $fields_profile = apply_filters( 'vcpl_profile_meta_fields', array(
      'user_login'  => array(
        'label' => __( 'Username ( Required )', VCPL_TEXT_DOMAIN ),
        'type'  => 'text',
        'class' => 'regular-text',
        'custom_attributes' => array( 'required' => true )
      ),
      'first_name'  => array(
        'label' => __( 'First Name', VCPL_TEXT_DOMAIN ),
        'type'  => 'text',
        'class' => 'regular-text',
      ),
      'last_name'   => array(
        'label' => __( 'Last Name', VCPL_TEXT_DOMAIN ),
        'type'  => 'text',
        'class' => 'regular-text',
      ),
      'user_email'  => array(
        'label' => __( 'Email ( Required )', VCPL_TEXT_DOMAIN ),
        'type'  => 'email',
        'class' => 'regular-text',
        'custom_attributes' => array( 'required' => true )
      ),
      'user_url'    => array(
        'label' => __( 'Website', VCPL_TEXT_DOMAIN ),
        'type'  => 'url',
        'class' => 'regular-text',
      ),
      'description' => array(
        'label' => __( 'Biographical Info', VCPL_TEXT_DOMAIN ),
        'type'  => 'textarea',
        'class' => 'regular-text',
        'custom_attributes' => array( 'rows' => 5 ),
      ),
      'img_profile' => array(
        'label' => __( 'Profile Picture', VCPL_TEXT_DOMAIN ),
        'type'  => 'hidden',
        'class' => 'regular-text',
        'extra' => self::output_image_profile(),
      ),
      'user_pass'   => array(
        'label' => __( 'Password', VCPL_TEXT_DOMAIN ),
        'type'  => 'button',
        'extra' => apply_filters( 'vcpl_password', self::output_password1() ),
        'row_class' => array( 'user-pass1-wrap' ),
        'row_id'    => 'password',
      ),
    ) );

    return $fields_profile;

  }

  /**
   * Customer meta fields.
   *
   * @return array
   */
  public function get_customer_meta_fields () : array
  {

    $fields_customer = apply_filters( 'vcpl_customer_meta_fields', array(
      'tax_id_ein' => array(
        'label' => __( 'Tax ID / EIN', VCPL_TEXT_DOMAIN ),
        'type'  => 'text',
        'class' => 'regular-text',
      ),
      'role'       => array(
        'label' => __( 'Role ( Required )', VCPL_TEXT_DOMAIN ),
        'type'  => 'select',
        'class' => 'regular-text',
        'custom_attributes' => array( 'required' => true ),
        'options' => array_reduce(
          array( 'cbs', 'cw', 'cbv' ),
          fn ( $carry, $item ) => count_users()['avail_roles']['vendor'] > 0 || $item !== 'cbv'
            ? array_merge( $carry, array( $item => wp_roles()->roles[$item]['name'] ) )
            : $carry,
          array()
        ),
      ),
      'vendor'     => array(
        'label' => __( 'Vendor', VCPL_TEXT_DOMAIN ),
        'type'  => 'select',
        'class' => 'regular-text',
        'custom_attributes' => array( 'data-require-dep' => true ),
        'data_dep' => array( array( 'name' => 'role', 'value' => 'cbv' ) ),
        'options'  => array_reduce(
          get_users( array( 'role' => 'vendor' ) ) ?: array(),
          fn ( $options, $vendor ) => $options + array( $vendor->ID => $vendor->display_name ),
          array()
        ),
      ),
    ) );

    return $fields_customer;
  }

  /**
   * Filter the fields for WooCommerce.
   * order the fields and remove the placeholder.
   *
   * @param string $prefix - billing or shipping
   * @return array
   */
  private static function wc_filter_meta_fields ( string $prefix ): array
  {

    $wc_meta_fields  = WC()->checkout()->get_checkout_fields( $prefix );
    $fields_filtered = array();

    $wc_meta_fields_order = array(
      $prefix . '_company'   => __( 'Company', VCPL_TEXT_DOMAIN ),
      $prefix . '_address_1' => __( 'Address line 1', VCPL_TEXT_DOMAIN ),
      $prefix . '_address_2' => __( 'Address line 2', VCPL_TEXT_DOMAIN ),
      $prefix . '_city'      => __( 'City', VCPL_TEXT_DOMAIN ),
      $prefix . '_postcode'  => __( 'Postcode / ZIP', VCPL_TEXT_DOMAIN ),
      $prefix . '_country'   => __( 'Country / Region', VCPL_TEXT_DOMAIN ),
      $prefix . '_state'     => __( 'State / County', VCPL_TEXT_DOMAIN ),
      $prefix . '_phone'     => __( 'Phone', VCPL_TEXT_DOMAIN ),
    );

    foreach ( $wc_meta_fields_order as $key => $label ) {
      if ( isset( $wc_meta_fields[$key] ) ) {
        $fields_filtered[$key] = array_merge( $wc_meta_fields[$key], array( 'label' => $label ) );
        unset( $fields_filtered[$key]['placeholder'] );
      }
    }

    $fields_filtered[$prefix . '_country']['input_class'] = array( 'js_field-country regular-text' );
    $fields_filtered[$prefix . '_state']['input_class']   = array( 'js_field-state regular-text' );

    return $fields_filtered;

  }

  /**
   * Billing meta fields.
   *
   * @return array
   */
  public function get_billing_meta_fields () : array
  {

    $fields_billing = apply_filters( 'vcpl_billing_meta_fields', self::wc_filter_meta_fields( 'billing' ) );
    return $fields_billing;

  }

  /**
   * Shipping meta fields.
   *
   * @return array
   */
  public function get_shipping_meta_fields () : array
  {
    $fields_shipping = apply_filters( 'vcpl_shipping_meta_fields', array_merge( array(
      'copy_billing_info' => array(
        'label' => __( 'Copy Billing Address', VCPL_TEXT_DOMAIN ),
        'type'  => 'button',
        'class' => 'regular-text',
        'extra' => sprintf( '<button type="button" class="button js_copy-billing" id="copy_billing">%s</button>', __( 'Copy', VCPL_TEXT_DOMAIN ) ),
      ) ),
      self::wc_filter_meta_fields( 'shipping' ),
      array(
        'shipping_phone' => array(
          'label' => __( 'Phone', VCPL_TEXT_DOMAIN ),
          'type'  => 'tel',
          'class' => 'regular-text',
        ),
      )
    ) );

    return $fields_shipping;
  }

  /**
   * Vendor meta fields.
   *
   * @return array
   */
  public function get_vendor_meta_fields () : array
  {
    $fields_vendor = apply_filters( 'vcpl_vendor_meta_fields', array(
      'role'       => array(
        'label' => __( 'Role ( Required )', VCPL_TEXT_DOMAIN ),
        'type'  => 'hidden',
        'value' => 'vendor',
        'tr_class' => array( 'hidden' ),
        'custom_attributes' => array( 'required' => true ),
      ),
      'salary'     => array(
        'label' => __( 'Salary', VCPL_TEXT_DOMAIN ),
        'type'  => 'number',
        'class' => 'regular-text',
        'custom_attributes' => array( 'min' => 0 ),
      ),
      'commission' => array(
        'label' => __( 'Commission ( % )', VCPL_TEXT_DOMAIN ),
        'type'  => 'number',
        'class' => 'regular-text',
        'custom_attributes' => array( 'min' => 0, 'max' => 100 ),
      ),
    ) );

    return $fields_vendor;
  }

  /**
   * Shop Manager meta fields.
   *
   * @return array
   */
  public function get_shop_manager_meta_fields () : array
  {
    $fields_shop_manager = apply_filters( 'vcpl_shop_manager_meta_fields', array(
      'role'   => array(
        'label' => __( 'Role ( Required )', VCPL_TEXT_DOMAIN ),
        'type'  => 'hidden',
        'value' => 'shop_manager',
        'tr_class' => array( 'hidden' ),
        'custom_attributes' => array( 'required' => true ),
      ),
      'salary' => array(
        'label' => __( 'Salary (' . get_woocommerce_currency_symbol() . ')', VCPL_TEXT_DOMAIN ),
        'type'  => 'number',
        'class' => 'regular-text',
        'custom_attributes' => array( 'min' => 0 ),
      ),
    ) );

    return $fields_shop_manager;

  }

  /**
   * Adjust the field value.
   * If the field value is not set, get the value from the user.
   *
   * @param  array $new_fields New fields.
   * @param  int   $user_id    User id.
   * @return array Adjusted fields.
   */
  private function adjust_field_value ( array $new_fields )
  {

    $user_id = ( int ) VCPL()->get_current_user_id_action_page()[0];

    if ( is_null( $user_id ) ) {
      return $new_fields;
    }

    foreach ( $new_fields as &$args ) {

      foreach ( $args['fields'] as $key => &$field ) {

        $value = get_userdata( $user_id )->{$key} ?? get_user_meta( $user_id, $key, true );

        if ( str_ends_with( $key, '_state' ) ) {
          $country = get_user_meta( $user_id, str_replace( '_state', '_country', $key ), true );
          $states  = WC()->countries->get_states( $country );
        }

        $field = array_merge(
          $field,
          ! isset( $field['value'] ) ? array( 'value' => $value  ) : array(),
          match ( $key ) {
            'user_login'    => array( 'description' => __( 'The username cannot be changed.', VCPL_TEXT_DOMAIN ), 'custom_attributes' => array( 'disabled' => true ) ),
            'user_pass'     => array( 'extra' => self::output_password2() ),
            'img_profile'   => array( 'extra' => self::output_image_profile( vcpl_get_img_profile_url( $user_id ) ), 'value' => get_user_meta( $user_id, $key, true ) ),
            'role'          => array( 'value' => get_userdata( $user_id )->roles[0] ),
            'shipping_state',
            'billing_state' => ! empty( $states ) ? array( 'type' => 'select', 'options' => $states ) : array(),
            'commission',
            'salary'        => array( 'value' => is_array( $value ) && isset( end( $value )['value'] ) ? end( $value )['value'] : '' ),
            default         => array()
          }
        );

      }

    }

    return $new_fields;

  }

  /**
   * Output the password field.
   * Password in the new user form.
   *
   * @return string
   */
  public static function output_password1 (): string
  {
    ob_start(); ?>
    <button type="button" class="button wp-generate-pw hide-if-no-js">
      <span class="text"><?= esc_html__( 'Generate Password', VCPL_TEXT_DOMAIN ); ?></span>
    </button>
    <div class="wp-pwd">
      <span class="password-input-wrapper">
        <input type="password" name="user_pass" id="pass1" class="regular-text" autocomplete="new-password" aria-describedby="pass-strength-result" spellcheck="false" data-reveal="1" data-pw="<?= esc_attr( wp_generate_password( 24 ) ); ?>" />
      </span>
      <button type="button" class="button wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?= esc_html__( 'Hide password', VCPL_TEXT_DOMAIN ); ?>">
        <span class="dashicons dashicons-hidden" aria-hidden="true"></span>
        <span class="text"><?= esc_html__( 'Hide', VCPL_TEXT_DOMAIN ); ?></span>
      </button>
      <div style="display:none" id="pass-strength-result" aria-live="polite"></div>
    </div>
  <?php return ob_get_clean();
  }

  /**
   * Output the password field.
   * Password in the edit user form.
   *
   * @return string
   */
  public static function output_password2 (): string
  {
    ob_start(); ?>
    <input type="hidden" value=" " />
    <button type="button" class="button wp-generate-pw hide-if-no-js" aria-expanded="false"><?php _e( 'Set New Password' ); ?></button>
    <div class="wp-pwd hide-if-js">
      <div class="password-input-wrapper">
        <input type="password" name="user_pass" id="pass1" class="regular-text" value="" autocomplete="new-password" spellcheck="false" data-pw="<?php echo esc_attr( wp_generate_password( 24 ) ); ?>" aria-describedby="pass-strength-result" />
        <div style="display:none" id="pass-strength-result" aria-live="polite"></div>
      </div>
      <button type="button" class="button wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Hide password' ); ?>">
        <span class="dashicons dashicons-hidden" aria-hidden="true"></span>
        <span class="text"><?php _e( 'Hide' ); ?></span>
      </button>
      <button type="button" class="button wp-cancel-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Cancel password change' ); ?>">
        <span class="dashicons dashicons-no" aria-hidden="true"></span>
        <span class="text"><?php _e( 'Cancel' ); ?></span>
      </button>
    </div>
  <?php return ob_get_clean();
  }

  /**
   * Output the image profile field.
   *
   * @param  string|null $src Image source.
   * @return string
   */
  public static function output_image_profile ( string $src = null ): string
  {
    $src = ! is_null( $src ) ? $src : get_avatar_url( false, array( 'default' => 'mm' ) );
    ob_start(); ?>
    <div class="vcpl-image-profile-wrap">
      <img src="<?= $src; ?>" alt="Profile Picture" class="vcpl-image-profile" id="vcpl-img-profile-view" data-default-src="<?= get_avatar_url( false, array( 'default' => 'mm' ) ); ?>">
      <button type="button" class="button vcpl-image-profile-btn" id="vcpl-upload-img-profile">
        <?= esc_html__( 'Upload', VCPL_TEXT_DOMAIN ); ?>
      </button>
      <button type="button" class="button vcpl-image-profile-btn" id="vcpl-remove-img-profile">
        <?= esc_html__( 'Remove', VCPL_TEXT_DOMAIN ); ?>
      </button>
    </div>
  <?php return ob_get_clean();
  }

  /**
   * Output the reset password button.
   *
   * @return string
   */
  public static function output_reset_password (): string
  {
    ob_start(); ?>
    <div class="generate-reset-link">
      <button type="button" class="button button-secondary" id="generate-reset-link">
        <?= esc_html__( 'Send Reset Link', VCPL_TEXT_DOMAIN ); ?>
      </button>
    </div>
  <?php return ob_get_clean();
  }

  /**
   * Output the customers table.
   *
   * @param  int $vendor_id Vendor id.
   * @return string
   */
  public static function output_customers ( int $vendor_id ): ?string
  {
    if ( ! user_can( ( int ) $vendor_id, 'vendor' ) ) return null;
    ob_start();
  ?>

    <table class="wp-list-table widefat fixed striped users vcpl-customers-table">
      <thead>
        <tr>
          <?php foreach ( array( 'username', 'email', 'total purchases', 'action' ) as $column ) : ?>
            <th scope="col" class="vcpl_column vcpl-column-<?= str_replace( ' ', '-', $column ); ?>">
              <?= esc_html__( ucwords( $column ) ); ?>
            </th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>

        <?php foreach ( vcpl_get_my_customers( $vendor_id ) ?: array() as $customer ) : ?>

          <tr>
            <?php
              foreach( array(
                'username'  => esc_html( $customer->display_name ),
                'email'     => esc_html( $customer->user_email ),
                'purchases' => esc_html( wc_get_customer_total_spent( $customer->ID ) . ' ' . get_woocommerce_currency() ),
                'actions'   => sprintf(
                  '<a href="%s" class="button vcpl-delete button-secondary remove-customer">%s</a>',
                  admin_url( 'admin.php?page=customer&action=delete_user&user_id=' . $customer->ID ),
                  esc_html__( 'Delete', VCPL_TEXT_DOMAIN )
                )
              ) as $key => $value ) :
            ?>
              <td class="<?= $key; ?> vcpl-column-<?= $key; ?>"><?= $value; ?></td>
            <?php endforeach; ?>

          </tr>

        <?php endforeach; ?>

        <?php if ( ! vcpl_get_my_customers( $vendor_id ) ) : ?>
          <tr>
            <td colspan="4"><?= esc_html__( 'No customers found.', VCPL_TEXT_DOMAIN ); ?></td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
<?php return ob_get_clean();
  }
}
