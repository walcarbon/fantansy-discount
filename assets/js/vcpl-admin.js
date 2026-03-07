/**
 * This file is used to load all modules for the admin page
 *
 * @package VcProfitLost
 * @subpackage VcProfitLostMetrics/assets/js
 * @version 1.0.0
 */
import vcpl_media_profile       from './modules/vcpl-media-profile.js';
import vcpl_form_validate_field from './modules/vcpl-form-validate-field.js';
import vcpl_form_depfield       from './modules/vcpl-form-depfield.js';
import vcpl_delete_user         from './modules/vcpl-delete-user.js';

( $ => {
  // if the page is not customer, vendor, shop-manager or vcpl-profit-lost, return
  const pages = ['customer', 'vendor', 'shop_manager', 'vcpl-profit-lost', 'analytics'];
  if ( ! pages.map( ( page ) => `toplevel_page_${page}` ).includes( vc.hook ) ) return;

  // Execute all modules
  $( document ).ready( function () {
    $(".js-select2-multiple").select2();
    vcpl_media_profile.init();
    vcpl_form_validate_field.tax_id_ein();
    vcpl_form_depfield.show();
    vcpl_delete_user.enable_submit();
    vcpl_delete_user.actions_before_delete_user();
  } );

} )( jQuery );
