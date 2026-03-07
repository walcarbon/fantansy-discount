<?php

/**
 * Template for the dashboard page.
 *
 * @package    Templates
 * @subpackage Templates/Myaccount
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">
    <div class="vcpl-dashboard">
      <div class="vcpl-dashboard-btn-wrap">
        <a class="woocommerce-Button button wp-element-button" href="<?= wc_get_page_permalink( 'myaccount' ) . 'customers?action=add_user'; ?>"><?= esc_html__( 'Add new customer', VCPL_TEXT_DOMAIN ); ?></a>
        <a class="woocommerce-Button button wp-element-button" href="<?= get_permalink( wc_get_page_id( 'shop' ) ); ?>"><?= esc_html__( 'Add new order', VCPL_TEXT_DOMAIN ); ?></a>
      </div>
      <span class="vcpl-dashboard-info"><?= esc_html__( 'Review the progress of your sales and top customers', VCPL_TEXT_DOMAIN ); ?></span>
      <div class="vcpl-cards">

        <?php foreach( apply_filters( 'vcpl_dashboard_vendor_cards', array() ) as $card ) : ?>
          <div class="vcpl-card">
            <div class="vcpl-card-wrap">
              <div class="vcpl-card-header">
                <h3><?= vcpl_get_var_array( 'title', $card ); ?></h3>
              </div>
              <div class="vcpl-card-body">
                <strong><?= vcpl_get_var_array( 'insight', $card ); ?></strong>
                <p><?= vcpl_get_var_array( 'desc', $card ); ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>

      </div>
      <div class="vcpl-analysis">

          <?php foreach( apply_filters( 'vcpl_analysis_tables', array() ) as $table ) : ?>
            <div class="vcpl-analysis-table">
              <h3 class="vcpl-analysis-table-title"><?= $table['title'] ?? strval( false ); ?></h3>
              <div class="vcpl-analysis-table-items">

                <?php foreach ( $table[ 'items' ] ?? array() as $item ) : ?>
                  <?php list( $key, $value ) = $item; ?>
                  <div class="vcpl-analysis-table-item">
                    <p class="vcpl-analysis-table-item-title"><?= $key ?? ''; ?></p>
                    <p class="vcpl-analysis-table-item-info"><?= $value ?? ''; ?></p>
                  </div>
                <?php endforeach; ?>

              </div>
            </div>
          <?php endforeach; ?>
      </div>
    </div>
</div>