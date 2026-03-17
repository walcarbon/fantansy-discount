<?php

/**
 * Customer Template
 *
 * @package    Templates
 * @subpackage Templates/Myaccount
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$current_action = VCPL()->get_current_action_page();
$user_id = VCPL()->get_current_user_id_action_page()[0] ?? 0;

if ( $user_id !== 0 && ! vcpl_user_exists( ( int ) $user_id ) ) {
  wp_die( "Invalid user. no user with id " . $user_id . " exists." );
}

$user = get_userdata( $user_id );

$my_account_customers_url = wc_get_endpoint_url( 'customers', '', wc_get_page_permalink( 'myaccount' ) );

?>

<div class="wrap">
  <div class="woocommerce-notices-wrapper">
    <?php wc_print_notices(); ?>
  </div>
  <h2 class="page-title vcpl-page-title">
    <?= esc_html__( 'Customers', VCPL_TEXT_DOMAIN ); ?>

    <?php

    if ( ! empty( $current_action ) ) :
      list( $fields, $btn_text, $nonce_action ) = match( $current_action ) {
        'add_user'    => array(
          array_reduce(
            array_keys( self::get_form_fields_new_customer() ),
            fn( $acc, $key ) => $acc . get_woocommerce_form_field( $key, self::get_form_fields_new_customer()[$key] ),
            strval( false )
          ),
          __( ucwords( 'add new customer' ), VCPL_TEXT_DOMAIN ),
          'new_user'
        ),
        'edit_user'   => array(
          "<input type='hidden' name='ID' value='{$user->ID}'>".
          get_woocommerce_form_field( 'user_pass', self::get_form_fields_new_customer()['user_pass'] ) .
          get_woocommerce_form_field( 'tax_id_ein', self::get_form_fields_new_customer()['tax_id_ein'], get_user_meta( $user->ID, 'tax_id_ein', true ) ),
          __( ucwords( 'update profile' ), VCPL_TEXT_DOMAIN ),
          'edit_profile'
        ),
        'delete_user' => array(
          "<input type='hidden' name='user_id[]' value='{$user->ID}'>
            <div id='vcpl-delete-user-wrap'>
                <p class='vcpl-customer'><strong>ID:</strong> {$user->ID} | <span class='vcpl-username'>{$user->user_login}</span></p>
                <p class='vcpl-customer'><strong>E-mail:</strong> {$user->user_email}</p>
            </div>
            <p>" . __( 'Are you sure you want to delete this customer?', VCPL_TEXT_DOMAIN ) . "</p>
          ",
          __( ucwords( 'delete customer' ), VCPL_TEXT_DOMAIN ),
          'delete_user'
        ),
        default           => array( false, false, false )
      };

    ?>

        <?php do_action( 'vcprfitlost_before_' . $current_action ); ?>

        <a href="<?= esc_url( $my_account_customers_url ); ?>" class="vcpl-back"><?= esc_html__( 'Back to List', VCPL_TEXT_DOMAIN ); ?></a>
      </h2>
       <form method="post" autocomplete="off" class="myacccount-form woocommerce-form vcpl-<?= esc_attr( $current_action ); ?>" action="">
        <?php if ( $user ) : ?>
          <h3><?= esc_html__( ucfirst( str_replace( '_user', '', $current_action ) ) . ' ' . $user->user_login, VCPL_TEXT_DOMAIN ); ?></h3>
          <p><?= esc_html__( 'Edit the customer details below.', VCPL_TEXT_DOMAIN ); ?></p>
        <?php endif; ?>
        <input type="hidden" name="action" value="<?= esc_attr( VCPL()->get_current_action_page() ); ?>">
        <?php if ( in_array( $current_action, array( 'add_user', 'edit_user' ), true ) ) : ?>
          <input type="hidden" name="role" value="cbv">
          <input type="hidden" name="vendor" value="<?= esc_attr( wp_get_current_user()->ID ); ?>">
        <?php endif; ?>
        <?php wp_nonce_field( 'vcpl_' . $nonce_action , 'vcpl_' . $nonce_action . '_nonce' ); ?>
        <div class="field-wrap" >
          <?= $fields; ?>
          <p class="submit"><input type="submit" name="submit" id="submit" class="woocommerce-Button button wp-element-button" value="<?= $btn_text; ?>"></p>
        </div>
      </form>

        <?php do_action( 'vcprfitlost_after_' . $current_action ); ?>

    <?php else : ?>

        <a href="<?= esc_url( add_query_arg( array( 'action' => 'add_user' ) ) ); ?>" class="woocommerce-Button button wp-element-button"><?= ucwords( esc_html__( 'add new customer', VCPL_TEXT_DOMAIN ) ); ?></a>
      </h2>
      <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
        <thead>
          <tr>
            <?php foreach ( self::get_table_customers_columns_headers() as $column ) : ?>
              <th class="woocommerce-orders-table__header"><?= $column; ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ( self::get_table_customers_rows() as $row ) : ?>
            <tr>
              <?php foreach ( $row as $cell ) : ?>
                <td class="woocommerce-orders-table__cell"><?= $cell; ?></td>
              <?php endforeach; ?>
            </tr>
          <?php endforeach; ?>
          <?php if ( empty( self::get_table_customers_rows() ) ) : ?>
            <tr>
              <td colspan="<?= count( self::get_table_customers_columns_headers() ); ?>" class="woocommerce-orders-table__cell woocommerce-orders-table__cell--no-data"><?= esc_html__( 'No customers found.', VCPL_TEXT_DOMAIN ); ?></td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

    <?php endif; ?>

</div>




