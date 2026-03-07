const vcpl_delete_user = ( $ => {
  return {
    // Enable submit button when all options are equal to delete_all
    enable_submit() {
      var values,
        $parent,
        $options = $( '#vcpl-delete-user-form' ).find( 'input[name^="actions-before-delete"]' );

      $options.on( 'change', function () {
        values = $options.map( function() {
          if( this.checked ) {
            return $( this ).val();
          }
        } );
        $parent = $( this ).closest( '.vcpl-action-form-options' );

        if( Array.from( values ).every( value => value === 'delete_all' ) ) {
          $( '#vcpl-delete-user-submit' ).removeAttr( 'disabled' );
          $parent.siblings( 'tr' ).find( 'input, select, button' ).attr( 'disabled', true );
        } else {
          $( '#vcpl-delete-user-submit' ).attr( 'disabled', true );
          $parent.siblings( 'tr' ).find( 'input, select, button' ).removeAttr( 'disabled' );
        }

      } );

      $options.trigger( 'change' );
    },
    // handler for the customer of the vendor
    actions_before_delete_user() {

      $( '.js-vcpl-customers' ).each( function () {
        var $button         = $( this ),
          $select           = $button.siblings( '.form-field' ).find( 'select' ),
          current_vendor_id = $button.data( 'current-vendor-id' ),
          customer_id       = $button.data( 'customer-id' ),
          action            = $button.data( 'action' ),
          users_delete      = $button.closest( 'form' ).data( 'users-delete' );

        $button.on( 'click', e => {
          e.preventDefault();
          var new_vendor_id   = $select.val(),
            errorMessage = new_vendor_id === '' ? 'Please select a vendor' : '';

          if( $select.length > 0 ) {
            $select[ 0 ].setCustomValidity( errorMessage );
            $select[ 0 ].reportValidity();

            if( errorMessage ) return;
          }

          $.ajax( {
            method  : 'POST',
            url     : vc.ajax_url,
            dataType: 'json',
            data    : {
              action           : 'actions_before_delete_user',
              nonce            : vc.nonce,
              new_vendor_id    : new_vendor_id,
              current_vendor_id: current_vendor_id,
              customer_id      : customer_id,
              users_delete     : users_delete,
              action_before_delete : action,
            },
            success: response => {

              if ( response.have_customer ) {
                $button.closest( 'tr' ).remove();
              } else {
                $button.closest( '.vcpl-actions-before-delete' ).remove();
                $( '#vcpl-delete-user-wrap' ).append( response.output );
              }

              if ( ! response.enable_submit ) {
                $( '#vcpl-delete-user-submit' ).removeAttr( 'disabled' );
              }

              $.each( response.request, function ( ix, value ) {
                var $notice = $( value.notice );
                $( "#wpbody-content h2" ).after( $notice );
                $notice.find( ".notice-dismiss" ).on( "click", function () {
                  $( this ).closest( ".notice" ).fadeOut( "slow", () => {
                    $( this ).closest( ".notice" ).remove();
                  } );
                } );
              } );

            },
          } );
        } );
      } );
    },
  }
} ) ( jQuery );

export default vcpl_delete_user;