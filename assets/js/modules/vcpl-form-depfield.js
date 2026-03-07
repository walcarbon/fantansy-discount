/**
 * Module to handle dependent fields in a form
 * Enables or disables the fields that depend on the value of other fields.
 *
 * @package VcProfitLost
 * @subpackage VcProfitLostMetrics/assets/js
 * @version 1.0.0
 */
const vcpl_form_depfield = ( $ => {

  return {
    // Validate the dependent fields
    validate( depList ) {
      return depList.every( ( depObject ) => {
        const depField = $( `[name="${depObject.name}"]` )
            .filter( (_, field) => ['radio', 'checkbox'].includes( field.type ) ? field.checked : field );

        return depField.length !== 0 && Array.from( depField ).every( ( field ) =>
          $( field ).val() === depObject.value );
      });
    },

    // Display the dependent fields
    show() {
      $( '[data-dep]' ).each( ( i, field ) => {
        const depList = $( field ).data( 'dep' );

        $.each( depList, ( i, dep ) => {
          $( `[name="${dep.name}"]` ).on( 'change', () => {
            const action = this.validate( depList ) ? 'removeAttr' : 'attr';
            $( field )[ action ]( 'data-dep', '' )
              .find( '[data-require-dep]' )[ action ]( 'disabled', true );
          } );

          $( `[name="${dep.name}"]` ).trigger( 'change' );
        } );
      } );
    }
  }
} ) ( jQuery );

export default vcpl_form_depfield;