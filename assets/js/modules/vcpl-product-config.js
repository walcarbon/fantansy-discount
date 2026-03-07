const vcpl_product_config = ( $ => {
  return {
    item_per_product() {
      $( '.add_to_cart_button' ).each(function () {
        const qty = $( this ).parent().find( '.qty' ),
          atc     = $( this ),
          n       = qty.attr( 'min' );

        atc.attr( 'data-quantity', n );

        qty
          .on( 'blur', function () {
            if ( parseInt( $( this ).val() ) % parseInt($(this).attr( 'min' ) ) !== 0 ) {
              $( this ).val( Math.round( $( this ).val() / n ) * n );
              atc.attr( 'data-quantity', $( this ).val() );
            }
          })
          .on( 'change', function () {
            atc.attr( 'data-quantity', $( this) .val() );
          });
      });
    }
  }
} ) ( jQuery );

export default vcpl_product_config;