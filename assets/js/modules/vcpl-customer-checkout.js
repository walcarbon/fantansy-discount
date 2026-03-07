const vcpl_customer_checkout = ( $ => {
 return {
    fill_checkout_fields() {

      if ( $( '#customer_order' ).length === 0 ) {
        return;
      }

      $( '#customer_order' ).on( 'change', function () {
        const $this = $( this ),
          $form = $this.closest( 'form' ),
          customer = customer_checkout.customers[ $this.val() ];
          if ( customer ) {
            for ( let key in customer ) {
				console.log( key );
			  if ( ( 'billing_country' == key || 'shipping_country' == key ) && customer[key] === '') {
				continue;
			  }

              const $field = $form.find( `input[name="${key}"], select[name="${key}"]` );
              if ( Object.prototype.hasOwnProperty.call(customer, key) && $field.length > 0 ) {
                $field.val( customer[ key ] );
              }
			  if ( 'billing_country' == key || 'shipping_country' == key || 'billing_state' == key || 'shipping_state' == key ) {
				  $field.trigger( 'change' );
			  }
            }
          }
      } );
    }
  }
} )( jQuery );

export default vcpl_customer_checkout;

