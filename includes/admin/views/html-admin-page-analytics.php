<?php

/**
 * This file is used to display the analythics page.
 *
 * @package    Includes
 * @subpackage Includes/Admin/Views/Analythics
 */

use Includes\Admin\List_Tables\VCPL_Admin_List_Table_Analytics_Vendor;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! current_user_can( 'manage_options' ) ) {
  wp_die( 'You do not have sufficient permissions to access this page.' );
}

$analythics_table = new VCPL_Admin_List_Table_Analytics_Vendor();

?>

<div class="wrap">
  <h2 class="page-title vcpl-page-title">
    <?= apply_filters( 'vcpl_page_users_title', get_admin_page_title() ); ?>
  </h2>
  <form method='post' name='vcpl_search_analythics' id='wp_list_table_anaythics' action='<?= esc_url( add_query_arg( 'page', VCPL()->get_current_user_page(), menu_page_url( VCPL()->get_current_user_page(), false ) ) ); ?>'>
    <?php $analythics_table->prepare_items(); ?>
    <?php $analythics_table->views(); ?>
    <?php $analythics_table->search_box( 'Search', 'search_analythics' ); ?>
    <?php $analythics_table->display(); ?>
  </form>
</div>