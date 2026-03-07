<?php
/**
 * Template for the analytics page.
 *
 * @package    Templates
 * @subpackage Templates/Myaccount
 */
if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">
  <div class="vcpl-analytics">
    <h2 class="page-title vcpl-page-title">
      <?= esc_html__( 'Analytics', VCPL_TEXT_DOMAIN ); ?>
    </h2>
    <span class="vcpl-analytics-info"><?= esc_html__( 'Review the progress and profit', VCPL_TEXT_DOMAIN ); ?></span>
    <div class="vcpl-analytics-table-wrap">
      <form class="vcpl-analytics-form" method="post" >
        <div class="vcpl-analytics-table-filters-wrap">
          <div class="alignleft actions bulkactions vcpl-filter-date">
            <label for="filter_date_before" class="filter_date_label"><?= __( 'from', VCPL_TEXT_DOMAIN ); ?></label>
            <input type="date" name="filter_date_before" id="filter_date_before" value="<?= vcpl_get_var( 'filter_date_before', '' ) ?>">
            <label for="filter_date_after" class="filter_date_label"><?= __( 'to', VCPL_TEXT_DOMAIN ); ?></label>
            <input type="date" name="filter_date_after" id="filter_date_after" value="<?= vcpl_get_var( 'filter_date_after', '' ) ?>">
          </div>
          <div class="alignleft actions bulkactions">
            <select name="filter_status" class="vcpl-status-select">
              <?=
                array_reduce(
                  array_keys( wc_get_order_statuses() ),
                  fn ( $options, $key ) => $options . sprintf( '<option value="%s" ' . selected( vcpl_get_var( 'filter_status', '' ), $key ) . '>%s</option>', $key, wc_get_order_statuses()[$key] ),
                  '<option value="">' . __( 'Select Status', VCPL_TEXT_DOMAIN ) . '</option>'
                );
              ?>
            </select>
          </div>
          <input type="submit" class="button js-submit-vcpl" name="filter_action" value="<?= esc_html__( 'Filter', VCPL_TEXT_DOMAIN ); ?>">
        </div>
        <table class="vcpl-table-analytics woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
          <thead>
            <tr>
              <?php foreach ( self::get_table_analytics_columns_headers() as $column ) : ?>
                <th class="woocommerce-orders-table__header"><?= $column; ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ( self::get_table_analytics_rows()['rows'] ?? array() as $row ) : ?>
              <tr>
                <?php foreach ( $row as $cell ) : ?>
                  <td class="woocommerce-orders-table__cell"><?= $cell; ?></td>
                <?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
            <?php if ( empty( self::get_table_analytics_rows()['rows'] ) ) : ?>
              <tr>
                <td colspan="<?= count( self::get_table_analytics_columns_headers() ); ?>" class="woocommerce-orders-table__cell woocommerce-orders-table__cell--no-data"><?= esc_html__( 'No profit found.', VCPL_TEXT_DOMAIN ); ?></td>
              </tr>
            <?php else: ?>
              <tr class="wp-table-total">
                <td colspan="2"></td>
                <td><?=  __( 'Orders total amount: ', VCPL_TEXT_DOMAIN ); ?><span class="vcpl-table-c-totals"><?= self::get_table_analytics_rows()['total_amount'] . get_woocommerce_currency_symbol(); ?></span></td>
                <td></td>
                <td><?= __( 'Total profit: ', VCPL_TEXT_DOMAIN ); ?><span class="vcpl-table-c-totals"><?= self::get_table_analytics_rows()['total_profit'] . get_woocommerce_currency_symbol(); ?></span></td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </form>
    </div>
  </div>
</div>