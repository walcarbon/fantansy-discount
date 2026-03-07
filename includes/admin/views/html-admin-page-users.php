<?php

/**
 * This file is used to display the users page.
 *
 * @package    Includes
 * @subpackage Includes/Admin/Views/Users
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

global $plugin_page;

if ( ! session_id() ) session_start();

$notice = vcpl_get_var_array( 'notice', $_SESSION );

if ( $notice ) {
  echo $notice;
  unset( $_SESSION['notice'] );
}

$user_table = new ( 'Includes\Admin\List_Tables\VCPL_Admin_List_Table_' . ucwords( VCPL()->get_current_user_page() ) )();

$output_action = 'html-' . match ( VCPL()->get_current_action_page() ) {
  'add_user'    => 'new-user',
  'edit_user'   => 'edit-profile',
  'delete_user' => 'delete-user',
  default       => VCPL()->get_current_action_page() ? wp_die( 'Invalid action.' ) : false,
} . '.php';

if ( ! current_user_can( 'manage_options' ) ) {
  wp_die( 'You do not have sufficient permissions to access this page.' );
}

foreach ( VCPL()->get_current_user_id_action_page() as $user_id ) {
  if ( ! vcpl_user_exists( ( int ) $user_id ) ) {
    wp_die( 'Invalid user. no user with id ' . $user_id . ' exists.' );
  }
}

?>

<div class="wrap">
  <h2 class="page-title vcpl-page-title">
    <?= apply_filters( 'vcpl_page_users_title', get_admin_page_title() ); ?>

    <?php if ( VCPL()->get_current_action_page() ) : ?>

      <a href="<?= menu_page_url( VCPL()->get_current_user_page(), false ); ?>" class="vcpl-back"> <?= __( 'Back to List', VCPL_TEXT_DOMAIN ); ?></a>
    </h2>

    <?php require_once $output_action; ?>

    <?php else: ?>

      <a href="<?= add_query_arg( 'action', 'add_user', menu_page_url( VCPL()->get_current_user_page(), false ) ); ?>" class="page-title-action"> <?= __( 'Add New ' . VCPL()->get_current_user_page_args()['title'], VCPL_TEXT_DOMAIN ); ?></a>
    </h2>

    <form method='post' name='vcpl_search_<?= VCPL()->get_current_user_page(); ?>' action='<?= $_SERVER['PHP_SELF']; ?>?page=<?= VCPL()->get_current_user_page(); ?>'>
      <?php $user_table->prepare_items(); ?>
      <?php $user_table->views(); ?>
      <?php $user_table->search_box( 'Search', 'search_' . VCPL()->get_current_user_page() ); ?>
      <?php $user_table->display(); ?>
    </form>

    <?php endif; ?>

</div>