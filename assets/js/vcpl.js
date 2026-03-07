/**
 * This file is used to load all modules for the forntend of the plugin
 *
 * @package VcProfitLost
 * @subpackage VcProfitLostMetrics/assets/js
 * @version 1.0.0
 */

import vcpl_product_config      from "./modules/vcpl-product-config.js";
import vcpl_form_validate_field from "./modules/vcpl-form-validate-field.js";
import vcpl_customer_checkout   from "./modules/vcpl-customer-checkout.js";

( ( $ ) => {
  $( document ).ready( function () {
    vcpl_product_config.item_per_product();
    vcpl_form_validate_field.tax_id_ein();
    vcpl_customer_checkout.fill_checkout_fields();
    $( '#customer_order' ).select2();
  } );
} )( jQuery );