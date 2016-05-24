/* JS For Media Uploader
@link http://s2webpress.com/add-image-uploader-to-profile-admin-page-wordpress/
Adapted from: http://mikejolley.com/2012/12/using-the-new-wordpress-3-5-media-uploader-in-plugins/
----------------------------------------------------*/

jQuery(document).ready(function($){

var preview = $('.add_meta_image_preview');
    
    if ( preview.attr('src') == '' )
    	preview.hide();
	
	
// Uploading files
var custom_uploader;
 
  $(document).on('click', '.upload-meta-image', function(e){

    e.preventDefault();
 
    // If the media frame already exists, reopen it.
    if ( custom_uploader ) {
      custom_uploader.open();
      return;
    }
 
    // Create the media frame.
    custom_uploader = wp.media.frames.file_frame = wp.media({
      title: $( this ).data( 'uploader_title' ),
      button: {
        text: $( this ).data( 'uploader_button_text' ),
      },
      multiple: false  // Set to true to allow multiple files to be selected
    });
 
    // When an image is selected, run a callback.
    custom_uploader.on( 'select', function() {
      // We set multiple to false so only get one image from the uploader
      attachment = custom_uploader.state().get('selection').first().toJSON();
 
      // Do something with attachment.id and/or attachment.url here
      $('.add_meta_image_preview').attr('src',attachment.url).show();
      $('.add_meta_image').attr('value',attachment.id);
      console.log(attachment.id);
    });
 
    // Finally, open the modal
    custom_uploader.open();
  });
 
});