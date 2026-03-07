<?php

/**
 * Form to delete a user.
 *
 * @package    Includes
 * @subpackage Includes/Admin/Views
 * @version    1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
  exit;
}

$dom  = new DOMDocument();
$html = new DOMDocument();

$users = array_map( 'get_userdata', VCPL()->get_current_user_id_action_page() );

do_action( 'vcpl_before_delete_user_form' );

ob_start();
?>

<form class="vcpl-delete-user-form" method="post" action="" id="vcpl-delete-user-form" data-users-delete='<?= implode( ',', VCPL()->get_current_user_id_action_page() ); ?>' >
  <input class="vcpl-form-control" type="hidden" name="action" value="delete_user">

  <?php wp_nonce_field( 'vcpl_delete_user', 'vcpl_delete_user_nonce' ); ?>

  <div id="vcpl-delete-user-wrap">
  </div>
  <div id='vcpl-actions-before-delete-wrap'>
  </div>
  <p class="submit">
    <input  type="submit"
            name="submit"
            id="vcpl-delete-user-submit"
            class="button button-primary"
            value="<?= __( 'Confirm Deletion', VCPL_TEXT_DOMAIN ); ?>"
            <?= ! empty( array_filter( $users, fn ( $user ) => ! empty( vcpl_get_my_customers( $user->ID ) ) ) ) ? "disabled=" . true  : strval( false ) ;?>
    >
  </p>
</form>

<?php

@$dom->loadHTML( ob_get_clean() );

foreach ( $users as $user ) {

  ob_start();

  if ( ! vcpl_get_my_customers( $user->ID ) ) {

    $wrap_id = 'vcpl-delete-user-wrap';
    ?>

      <p class="vcpl-user-delete">
        <strong>ID:</strong> <?= $user->ID; ?> | <span class="vcpl-user-delete-title"><?= $user->user_login; ?></span>
      </p>
      <input type="hidden" name="user_id[]" value="<?= $user->ID; ?>">

    <?php
  } else {

    $wrap_id = 'vcpl-actions-before-delete-wrap';
    ?>

    <div class="vcpl-actions-before-delete">
      <p class="vcpl-actions-title" >
        <strong>ID:</strong> <?= $user->ID; ?> | <strong class="vcpl-actions-before-delete-title"><?= $user->user_login; ?></strong>
        <span class="vcpl-actions-desc"><?= esc_html__( ' has customers and cannot be deleted.', VCPL_TEXT_DOMAIN ); ?></span>
      </p>
      <input type="hidden" name="user_id[]" value="<?= $user->ID; ?>">
      <span class="vcpl-actions-info" >
        <strong><?= esc_html__( 'Please select an action to manage the customers:', VCPL_TEXT_DOMAIN ); ?></strong>
      </span>
      <table class="vcpl-actions-form-table" >
      <tbody>
        <tr class="vcpl-action-form-options">
          <td>
          <?= vcpl_wp_radio_actions_before_delete_user( $user->ID ); ?>
          </td>
        </tr>
        <tr data-dep='<?= vcpl_wp_actions_before_delete_user_dep( $user->ID, "single_vendor" ); ?>' >
          <td>
            <?=
              vcpl_wp_select_available_vendors_to_change( array(
                'vendor_id'   => $user->ID,
                'customer_id' => implode( ",", get_user_meta( $user->ID, 'customers', true ) )
              ) );
            ?>
          </td>
        </tr>
        <tr data-dep='<?= vcpl_wp_actions_before_delete_user_dep( $user->ID, "diff_vendors" ); ?>'>
          <td>
            <table class="widefat vcpl-tb">
              <thead>
                <tr>
                  <th><strong><?= esc_html__( 'Customers', VCPL_TEXT_DOMAIN ); ?></strong></th><th></th> <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ( vcpl_get_my_customers( $user->ID ) as $customer ) : ?>
                  <tr>
                    <td><?= $customer->user_login; ?></td>
                    <td class="vcpl-change-vendor">
                      <?=
                        vcpl_wp_select_available_vendors_to_change( array(
                          'vendor_id'   => $user->ID,
                          'customer_id' => $customer->ID
                        ) );
                      ?>
                    </td>
                    <td class="vcpl-delete-customer">
                      <button class="button vcpl-delete js-vcpl-customers" data-action="delete_customer" data-customer-id="<?= $customer->ID; ?>" data-current-vendor-id="<?= $user->ID; ?>" >
                        <?php esc_html_e( 'Confirm Deletion', VCPL_TEXT_DOMAIN ); ?>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
      </table>
    </div>
    <?php
  }

  @$html->loadHTML( ob_get_clean() );
  $imported = $dom->importNode( $html->getElementsByTagName( 'body' )->item( 0 ), true );
  $dom->getElementById( $wrap_id )->appendChild( $imported );

}

echo $dom->saveHTML();

do_action( 'vcpl_after_delete_user_form' );































