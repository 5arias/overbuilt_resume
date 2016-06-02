<?php
/**
 * Overbuilt Resume functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Overbuilt_Resume
 */


/*=====================================================================================
  Initialize Theme Setup
  @since overbuilt_resume 0.1.0
======================================================================================*/
require('inc/theme_setup.php');



/*=====================================================================================
  Custom Classes
  @since overbuilt_resume 0.1.0
======================================================================================*/
require('inc/classes/cpt.php');
require('inc/classes/metabox.php');
<<<<<<< HEAD
require('inc/classes/abilities.php');
require('inc/classes/skillset.php');
require('inc/classes/skillset_list_table.php');
require('inc/classes/devtools.php');
require('inc/classes/toolbox_list_table.php');
=======
require('inc/classes/skillset.php');
require('inc/classes/skillset_list_table.php');
>>>>>>> origin/toolbox-and-abstract-classes



/*=====================================================================================
  Enqueue our fonts  
  @since overbuilt_resume 0.1.0
======================================================================================*
function overbuilt_resume_fonts() {
	//Fonts
	//wp_enqueue_style('overbuilt_resume-opensans', '//fonts.googleapis.com/css?family=Open+Sans:300,600, 700' );
	//wp_enqueue_style('overbuilt_resume-fontawesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css' );
	//wp_enqueue_style('overbuilt_resume-fonts', get_template_directory_uri() . '/inc/css/fonts.css' );

}
add_action( 'wp_enqueue_scripts', 'overbuilt_resume_fonts' );




/*=====================================================================================
  Add Custom Contact Fields to User Profile
  @template-part 'contact.php'
  @since overbuilt_resume 0.1.0
======================================================================================*/

function add_user_contact_methods( $user_contact ) {

	// Add user contact methods
	$user_contact['phone']   = __( 'Phone Number'   );
	$user_contact['skype']   = __( 'Skype Username'   );
	$user_contact['github'] = __( 'Github Account' );
	$user_contact['linkedin'] = __( 'LinkedIn Account' );

	return $user_contact;
}
add_filter( 'user_contactmethods', 'add_user_contact_methods' );




/*=====================================================================================
  Add Extra "About Me" Fields to User Profile
  @template-part 'about.php'
  @since overbuilt_resume 0.1.0
======================================================================================*/

/* Add Extra "About" Fields to the Profile Page
   @link http://s2webpress.com/add-image-uploader-to-profile-admin-page-wordpress/
----------------------------------------------------*/
function add_about_yourself_profile_fields( $user ) { ?>

	<table id="extra_about_fields" class="form-table">

		<tr class="user-about_nickname-wrap">
			<th><label for="about_nickname">About your nickname</label></th>
			<td><input type="text" name="about_nickname" id="about_nickname" value="<?php echo esc_attr( get_the_author_meta( 'about_nickname', $user->ID ) ); ?>" class="regular-text" /></td>
		</tr>
		<tr class="user-hometown-wrap">
			<th><label for="hometown">Hometown</label></th>
			<td><input type="text" name="hometown" id="hometown" value="<?php echo esc_attr( get_the_author_meta( 'hometown', $user->ID ) ); ?>" class="regular-text" /></td>
		</tr>
		<tr class="user-residence-wrap">
			<th><label for="residence">Resident in</label></th>
			<td><input type="text" name="residence" id="residence" value="<?php echo esc_attr( get_the_author_meta( 'residence', $user->ID ) ); ?>" class="regular-text" /></td>
		</tr>
		<tr class="user-age-wrap">
			<th><label for="age">Age <span class="description">(in years)</span></label></th>
			<td><input type="number" name="age" id="age" value="<?php echo esc_attr( get_the_author_meta( 'age', $user->ID ) ); ?>" step="1" /></td>
		</tr>
		<tr class="user-met-image-wrap">
            <th><label for="user_meta_image"><?php _e( 'Profile Picture', 'overbuilt_resume' ); ?></label></th>
            <td>
                <!-- Outputs the image after save -->
                <img id="user_meta_image_preview" class="add_meta_image_preview" src="<?php $attach_id = get_the_author_meta( 'user_meta_image', $user->ID ); echo wp_get_attachment_image_url($attach_id, 'medium'); ?>" style="max-width: 150px;"><br />
                <!-- Outputs the text field and displays the URL of the image retrieved by the media uploader -->
                <input type="hidden" name="user_meta_image" id="user_meta_image" class="add_meta_image" value="<?php echo absint( get_the_author_meta( 'user_meta_image', $user->ID ) ); ?>" class="regular-text" />
                <!-- Outputs the save button -->
                <input type='button' class="upload-meta-image button-primary" value="<?php _e( 'Upload Image', 'overbuilt_resume' ); ?>" id="uploadimage"/><br />
                <span class="description"><?php _e( 'Upload an image for your user profile.', 'overbuilt_resume' ); ?></span>
            </td>
        </tr>

	</table>
<?php }
	
add_action( 'show_user_profile', 'add_about_yourself_profile_fields' );
add_action( 'edit_user_profile', 'add_about_yourself_profile_fields' );




/* Update/Save Fields on the Profile Page
----------------------------------------------------*/
function save_about_yourself_profile_fields( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	update_usermeta( $user_id, 'about_nickname', sanitize_text_field( $_POST['about_nickname'] ) );
	update_usermeta( $user_id, 'hometown', sanitize_text_field( $_POST['hometown'] ) );
	update_usermeta( $user_id, 'residence', sanitize_text_field( $_POST['residence'] ) );
	update_usermeta( $user_id, 'age', sanitize_text_field( $_POST['age'] ) );
	update_usermeta( $user_id, 'user_meta_image', $_POST['user_meta_image'] );
}

add_action( 'personal_options_update', 'save_about_yourself_profile_fields' );
add_action( 'edit_user_profile_update', 'save_about_yourself_profile_fields' );






/* Move Additional Fields higher on the Profile Page
	
   NOTE: Wordpress seems to have forgotten about this section
   so we have to use jquery to move the rows into the correct table.
----------------------------------------------------*/
function move_extra_about_fields() {
?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            rows = $('#extra_about_fields tr').remove();
            rows.insertAfter('tr.user-description-wrap');
        });
    </script>
<?php }
	
add_action( 'admin_footer-profile.php', 'move_extra_about_fields' );




/* JS For Media Uploader
@link http://s2webpress.com/add-image-uploader-to-profile-admin-page-wordpress/
Adapted from: http://mikejolley.com/2012/12/using-the-new-wordpress-3-5-media-uploader-in-plugins/
----------------------------------------------------*/
function load_profile_media_uploader() {
	
	//Prevent loading if not on a profile page.
	$screen = get_current_screen();
	if ( $screen->id !== 'profile' )
        return;
    
    //load wp.media and custom loader scripts
    wp_enqueue_media();
    wp_enqueue_script('add_meta_image', get_template_directory_uri() . '/inc/js/add_media_uploader.js', array('jquery'));
}

add_action( 'admin_enqueue_scripts', 'load_profile_media_uploader' );






/*=====================================================================================
  Work History Custom Post Type
======================================================================================*/
$history = new CPT(
	array(
    	'post_type_name' => 'work_history',
		'singular' 		 => 'Work History',
		'plural' 		 => 'Work History',
		'slug' 		 	 => 'work_history'
		),
	array(
		'menu_position'	 => 20,
		'menu_icon'		 => 'dashicons-businessman',
		'supports' 		 => array('title')
		)
);

$history->columns(
	array(
    	'cb' 			 => '<input type="checkbox" />',
		'meta_rcmb_start_date'	 => __('Start Date'),
		'meta_rcmb_end_date'  	 => __('End Date'),
		'title' 		 => __('Title'),
		'meta_rcmb_organization'   => __('Organization'),
		'meta_rcmb_location'   => __('Location'),
		'meta_rcmb_website_url'   => __('Website'),
		'organization_logo'	=> __('Logo'),
		'date' 			 => __('Date')
));

$history->populate_column('organization_logo', function($column, $post) {
	
	$attach_id = $post->rcmb_organization_logo;
	if ($attach_id)
		echo wp_get_attachment_image( $attach_id , 'thumbnail');

});


$history->sortable(array(
    'meta_rcmb_start_date' 	 => array('rcmb_start_date', true),
    'meta_rcmb_end_date' 	 => array('rcmb_end_date', true),
    'meta_rcmb_organization' => array('rcmb_organization', true),
    'meta_rcmb_location'	 => array('rcmb_location', true)
));

	
$work_history_metaboxes = new RCMB(
	'work_history',
	'Work Experience', 
	array(
		'Organization Logo' => 'image',
		'Organization' 	    => 'text',
		'Start Date' 	    => 'text',
		'End Date' 		    => 'text',
		'Location' 		    => 'text',
		'Website URL' 	    => 'text',
		'Job Description'   => 'editor'
		)
	);



/*=====================================================================================
  Portfolio Custom Post Type
======================================================================================*/
$portfolio = new CPT(
	array(
    	'post_type_name' => 'portfolio',
		'singular' 		 => 'Portfolio Item',
		'plural' 		 => 'Portfolio',
		'slug' 		 	 => 'portfolio'
		),
	array(
		'menu_position'	 => 23,
		'menu_icon'		 => 'dashicons-vault',
		'supports' 		 => array('title', 'post_tag')
		)
);

$portfolio->columns(
	array(
    	'cb' 			=> '<input type="checkbox" />',
		'screenshot'	=> __('Screenshot'),
		'title' 		=> __('Title'),
		'meta_rcmb_website'   => __('Website'),
		'date' 			 => __('Date')
));

$portfolio->populate_column('screenshot', function($column, $post) {
	
	$attach_id = $post->rcmb_portfolio_screenshot;
	if ($attach_id)
		echo wp_get_attachment_image( $attach_id , 'thumbnail');

});


$portfolio->sortable(array(
    'meta_rcmb_website' 	 => array('rcmb_website', true)
));

	
$portfolio_metaboxes = new RCMB(
	'portfolio',
	'Portfolio', 
	array(
		'Website URL' 	    	=> 'text',
		'Website Description'   => 'editor',
		'Screenshot' 			=> 'image'
		)
	);

