<?php

/**
 * Add New User
 *
 * @package    Includes
 * @subpackage Includes/Admin/Views
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

add_filter( 'vcpl_page_users_title', fn () => 'Add New' . VCPL()->get_current_user_page_args()['title'] );

do_action( 'vcpl_before_new_user_form' );  ?>

<form class="vcpl-form" method="post" action="" autocomplete="off">
  <input type="hidden" name="action" value="add_user">

  <?php wp_nonce_field( 'vcpl_new_user', 'vcpl_new_user_nonce' ); ?>

  <div class="vcpl-form-sections">

    <?php foreach ( apply_filters( 'vcpl_new_user_fields', array() ) as $sect ) : ?>

      <?= vcpl_get_var_array( 'title', $sect, '', '<h2 class="hndl">%s</h2>', 'esc_html__', VCPL_TEXT_DOMAIN ); ?>
      <?= vcpl_get_var_array( 'description', $sect, '', '<p>%s</p>', 'esc_html__', VCPL_TEXT_DOMAIN ); ?>

      <table class="vcpl-form-table form-table" <?= vcpl_get_var_array( 'id', $sect, '', 'id="%s"', 'esc_attr' ); ?> >
        <tbody>

          <?php foreach ( vcpl_get_var_array( 'fields', $sect, array() ) as $key => $field ) : ?>

            <tr
                class="vcpl-form-field form-field <?= implode( ' ', $field['row_class'] ?? array() ); ?>"
                <?= vcpl_get_var_array( 'row_id', $field, '', 'id="%s"' ); ?>
                <?= vcpl_get_var_array( 'data_dep', $field, '', "data-dep='%s'", 'json_encode' ); ?>
            >
              <th scope="row">
                <label for="<?= esc_attr( $key ); ?>">

                  <?= esc_html__( vcpl_get_var_array( 'label', $field ) ); ?>

                </label>
              </th>
              <td>

                <?php woocommerce_form_field( $key, $field, vcpl_get_var_array( 'value', $field ) ); ?>

                <?= vcpl_get_var_array( 'extra', $field ); ?>
                <?= vcpl_get_var_array( 'description', $field, '', '<p class="description">%s</p>', 'esc_html__', VCPL_TEXT_DOMAIN ); ?>

              </td>
            </tr>

          <?php endforeach; ?>

        </tbody>
      </table>

    <?php endforeach; ?>

    <p class="vcpl-form-submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?= 'Add New ' . VCPL()->get_current_user_page_args()['title'] ?>"></p>
  </div>
</form>

<?php
/**
 * Action to after new user form
 *
 * @param string $current_role
 */
do_action( 'vcpl_after_new_user_form' );
?>