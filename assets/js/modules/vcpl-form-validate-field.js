/**
 * Validate form fields
 * @package VcProfitLost
 * @subpackage VcProfitLostMetrics/assets/js/modules
 * @version 1.0.0
 */
const vcpl_form_validate_field = ( $ => ( {
  // Validate tax id ein
  tax_id_ein() {
    $( '#tax_id_ein' ).each( function () {
      const $input = $( this ),
        $form = $input.closest( 'form' ),
        regex = /^[0-9-]+$/;

      $input.on( 'input', function () {
        this.setCustomValidity( regex.test( $( this ).val() ) ? '' : vci18n.validate_text_tax_id_ein );
        this.reportValidity();
      } );

      $form.on( 'submit', function ( e ) {
        $input.trigger( 'input' );
        if ( ! this.checkValidity() ) e.preventDefault();
      } );
    } );
  }
} ) )( jQuery );

export default vcpl_form_validate_field;