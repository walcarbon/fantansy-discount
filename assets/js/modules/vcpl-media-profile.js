
/**
 * Media profile - This module is used to upload and remove the image profile
 * @package VcProfitLost
 * @subpackage VcProfitLostMetrics/assets/js
 * @version 1.0.0
 */

const vcpl_media_profile = ( $ => {
  return {
    init() {
      $( document ).on( 'click', '#vcpl-upload-img-profile', ( e ) => {
        e.preventDefault();

        if ( this.marco ) {
          this.marco.open();
          return;
        }

        this.marco = wp.media( {
          frame   : 'select',
          title   : 'Select an image profile',
          library : { type: 'image' },
          button  : { text: 'Use this image' },
          multiple: false,
        } ).on( 'select', () => {
          const attachment = this.marco.state().get( 'selection' ).first().toJSON();
          $( '#vcpl-img-profile-view' ).attr( 'src', attachment.url );
          $( '#img_profile' ).val( attachment.id );
        } );

        this.marco.open()
      } );

      $( document ).on( 'click', '#vcpl-remove-img-profile', ( e ) => {
        e.preventDefault();
        $( '#vcpl-img-profile-view' ).attr( 'src', $( '#vcpl-img-profile-view' ).data( 'default-src' ) );
        $( '#img_profile' ).val( '' );
      } );
    }
  }
} )( jQuery );

export default vcpl_media_profile;