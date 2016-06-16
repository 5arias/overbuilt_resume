<?php
/**
 * ProfileMods Class
 *
 * Includes all methods used to modify the user account profile page and options
 *
 * @author  Stephen Brody
 * @link    http://stephenbrody.com
 * @version 1.0.0
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 */
 
add_action( 'init', array( 'ProfileMods', 'init' ));
 
class ProfileMods {
	
	/**
     * Holds the class instance for singleton style instantiation.
     *
     * @var self
     */
	public static $instance;
	
	
	/**
     * Init
     *
     * Instantiates the class on WP init and ensures a single instance only
     *
     * @var string $instance
     */
	public static function init() {
        
        if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
    }
    
    
    /**
     * Constructor
     *
     * Add filters/actions, enqueue scripts/styles, etc, etc.
     *
     */
    public function __construct() {
	    
	    // Removes Unnecessary Personal Options
	    add_action('admin_init', array($this, 'cleanup_admin_profile_page'));
	    
	    //New Custom Contact Fields
    	add_filter( 'user_contactmethods', array( $this, 'add_user_contact_methods' ) );
    	
    	
    	//HTML output for the new "About Yourself" Fields
		add_action( 'show_user_profile', array( $this, 'add_about_yourself_profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'add_about_yourself_profile_fields' ) );
		
		
		//Save & Update New User Meta Fields
		add_action( 'personal_options_update', array( $this, 'save_about_yourself_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_about_yourself_profile_fields' ) );
		
		
		//JS to move the fields into the correct location on page load
		add_action( 'admin_footer-profile.php', array( $this, 'move_extra_about_fields' ) );
    	
    	
    	//Enqueue Media Loader Scripts
    	add_action( 'admin_enqueue_scripts', array( $this, 'load_profile_media_uploader' ) );
    }
    
    
    
	/**
     * Add Custom Contact Fields to User Profile
     *
     * adds new contact fields. Add as many as you like! 
     *
     * @param $user_contact
     * @return $user_contact
     */
	public function add_user_contact_methods( $user_contact ) {
	
		// Add user contact methods
		$user_contact['phone']   = __( 'Phone Number'   );
		$user_contact['skype']   = __( 'Skype Username'   );
		$user_contact['github'] = __( 'Github Account' );
		$user_contact['linkedin'] = __( 'LinkedIn Account' );
	
		return $user_contact;
	}
	
	
	
	/**
     * Additional "About" Fields to the Profile Page
     *
     * New fields for the about section since they 
     * really aren't contact info and WP doesn't have a hook.
     * New Profile Image Uploader too. Gravatar sucks.
     *
     * This site helped a lot with the image uploader
     * @link http://s2webpress.com/add-image-uploader-to-profile-admin-page-wordpress/
     *
     * @var string $about_nickname
     * @var string $hometown
     * @var string $residence
     * @var string|int $age
     * @var file $user_meta_image
     *
     * @return html
     */
	public function add_about_yourself_profile_fields( $user ) { ?>
	
		<table id="extra_about_fields" class="form-table">
	
			<tr class="user-bachelor-degree-wrap">
				<th><label for="bachelor_degree">Bachelor Degree</label></th>
				<td><input type="text" name="bachelor_degree" id="bachelor_degree" value="<?php echo esc_attr( get_the_author_meta( 'bachelor_degree', $user->ID ) ); ?>" class="regular-text" /></td>
			</tr>
			<tr class="user-master-degree-wrap">
				<th><label for="master_degree">Master Degree</label></th>
				<td><input type="text" name="master_degree" id="master_degree" value="<?php echo esc_attr( get_the_author_meta( 'master_degree', $user->ID ) ); ?>" class="regular-text" /></td>
			</tr>
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
	<?php 
	}




	/**
     * Update/Save Fields on the Profile Page
     *
     * Don't forget to save the new meta data either.
     *
     * @param int $user_id
     * @var string $about_nickname
     * @var string $hometown
     * @var string $residence
     * @var string|int $age
     * @var file $user_meta_image
     *
     * @return void
     */
	public function save_about_yourself_profile_fields( $user_id ) {
		if ( !current_user_can( 'edit_user', $user_id ) )
			return false;
	
		update_usermeta( $user_id, 'bachelor_degree', sanitize_text_field( $_POST['bachelor_degree'] ) );
		update_usermeta( $user_id, 'master_degree', sanitize_text_field( $_POST['master_degree'] ) );
		update_usermeta( $user_id, 'about_nickname', sanitize_text_field( $_POST['about_nickname'] ) );
		update_usermeta( $user_id, 'hometown', sanitize_text_field( $_POST['hometown'] ) );
		update_usermeta( $user_id, 'residence', sanitize_text_field( $_POST['residence'] ) );
		update_usermeta( $user_id, 'age', sanitize_text_field( $_POST['age'] ) );
		update_usermeta( $user_id, 'user_meta_image', $_POST['user_meta_image'] );
	}
	
	
	
	/**
     * Move Additional Fields higher on the Profile Page
     *
     * Wordpress seems to have forgotten about this section. Hence no hook.
     * So we have to use jQuery to move the rows into the correct table.
     *
     */
	public function move_extra_about_fields() {
	?>
	    <script type="text/javascript">
	        jQuery(document).ready(function($) {
	            rows = $('#extra_about_fields tr').remove();
	            rows.insertAfter('tr.user-description-wrap');
	        });
	    </script>
	<?php 
	}
	
	
	/**
     * JS For Media Uploader
     *
     * Enqueue scripts for the media loader on the profile page
     *
     * @link http://s2webpress.com/add-image-uploader-to-profile-admin-page-wordpress/
     * @link Adapted from: http://mikejolley.com/2012/12/using-the-new-wordpress-3-5-media-uploader-in-plugins/
     */
	public function load_profile_media_uploader() {
		
		//Prevent loading if not on a profile page.
		$screen = get_current_screen();
		if ( $screen->id !== 'profile' )
	        return;
	    
	    //load wp.media and custom loader scripts
	    wp_enqueue_media();
	    wp_enqueue_script('add_meta_image', get_template_directory_uri() . '/inc/js/add_media_uploader.min.js', array('jquery'));
	}
	
	
	
	/**
     * Remove Excess Profile Options
     *
     * removes the "personal options" section from the profile since it isn't necessary
     *
     * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/user_contactmethods
     */
	public function cleanup_admin_profile_page(){
		
		// removes the admin color scheme options
		remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
		
		// 
		add_action( 'admin_head-profile.php', array( $this, 'user_profile_subject_start' ) );
		add_action( 'admin_footer-profile.php', array( $this, 'user_profile_subject_end' ) );
	}
	
	
    
    // removes the leftover 'Visual Editor', 'Keyboard Shortcuts' and 'Toolbar' options.
	public function remove_personal_options( $subject ) {
    	$subject = preg_replace( '#<h2>Personal Options</h2>.+?/table>#s', '', $subject, 1 );
		return $subject;
	}
	
	// Process without sending headers	
	public function user_profile_subject_start() {
    	ob_start( array( $this, 'remove_personal_options' ) );
	}

	// Cleanup after page load.
	public function user_profile_subject_end() {
		ob_end_flush();
	}
    
    
}