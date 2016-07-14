<?php
/**
 * Resume Custom Metabox Class (RCMB)
 *
 * Used to help create custom metaboxes and fields for Wordpress.
 * @link http://github.com/5arias/wp-custom-metabox-class/
 *
 * @author  Stephen Brody
 * @link    http://stephenbrody.com
 * @version 0.1.0
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 */
 
 class RCMB {
	
	/**
     * User Submitted Title for the metabox assigned on __construct()
     *
     * @var string $title.
     */
	protected $title;
	
	
	/**
     * User Submitted array of fields included within the metabox assigned on __construct()
     *
     * @var array $fields.
     */
	protected $fields;
	
	
	/**
     * User Submitted post type to register the metabox and assigned on __construct().
     *
     * @var string $post_type.
     */
	protected $post_type;
	
	
	/**
     * Holds the metabox ID assigned on __construct().
     *
     * @var string $prefix + $title.
     */
	protected $id;
	
	
	/**
     * Designates the class prefix used.
     *
     * @var string $prefix.
     */
	protected $prefix = 'rcmb_';
	 
	 
	/**
     * Constructor
     *
     * Add custom metabox and fields
     *
     * @param string $rcmb_post_type The name of the post type.
     * @param string $rcmb_title The name of the new metabox.
     * @param array $rcmb_fields User submitted array of fields as $field_type => $field_label.
     */
    public function __construct( $rcmb_post_type, $rcmb_title, $rcmb_fields = array() ) {
	    
	    // Apply to post_type
	    $this->post_type = $rcmb_post_type;
	    
	    // Apply to metabox title
	    $this->title = $rcmb_title;
	    
	    // Apply fields array
	    $this->fields = $rcmb_fields;
	    
	    // Set the metabox element ID
	    $this->id = $this->rcmb_metabox_id($this->title);
	    
	    // Add Metabox to post type
		add_action( 'add_meta_boxes', array($this, 'rcmb_add_meta_boxes' ) );
		
		// Enqueue Admin Only Scripts
		add_action( 'admin_enqueue_scripts', array($this, 'rcmb_enqueue_admin_scripts' ) );
		
		// Save Metabox field data
		add_action( 'save_post_' . $this->post_type, array( $this, 'rcmb_save_post' ) );
	}
	
	
	
	/**
     * Add metabox callback
     *
     * adds meta box to save additional meta data for the content type
     *
     * @method callable rcmb_add_meta_boxes
     */
	public function rcmb_add_meta_boxes(){
		
		//WP action for adding metaboxes to post type
		add_meta_box( $this->id, 					//id
			__($this->title, 'spr'),	//box name
			array($this,'rcmb_display_meta_box'), 	//display function
			$this->post_type, 						//content type 
			'normal', 								//context
			'default' 								//priority
		);
		
	}
	
	
	
	/**
     * Enqueue Admin Scripts
     *
     * loads admin only scripts needed for various metaboxes
     *
     * @method callable rcmb_enqueue_admin_scripts
     */
	public function rcmb_enqueue_admin_scripts(){
		
		//Verify post_type so scripts only load for that page
		//so we don't clog up the admin panel with unused scripts.
		global $post_type;
		if( $this->post_type == $post_type ){
		
			//Enqueue our admin stylesheet
			wp_enqueue_style('rcmb_admin_styles', get_template_directory_uri() . '/inc/css/metabox_admin.css');
		
			//Verify image field is being included
			if (in_array('image', $this->fields)) {
			
				//Enqueue wp.media and custom loader script
				wp_enqueue_media();
				wp_enqueue_script('add_meta_image', get_template_directory_uri() . '/inc/js/add_media_uploader.min.js', array('jquery'));
			}
		}
		
	}
	
	
	
	/**
     * Get metabox id
     *
     * Creates a slug style ID for the metabox container element.
     *
     * @param  string $title Title to slugify.
     * @return string $box_id Returns the slugified ID.
     */
    protected function rcmb_metabox_id( $title = null ) {

        // If no title is set use this->title .
        if ( ! isset( $title ) ) {

            $title = $this->title;
        }

        // Convert to standard id using rcmb_create_id method.
        $box_id = $this->rcmb_create_id( $title );

        return $box_id;
    }	



	
	/**
     * Create standardized ID
     *
     * Helper function to convert provided string into a standardized ID.
     * Adds the class prefix to prevent conflicts with other plugins or form changes.
     *
     * @param string $label text to be converted
     * @return string $id standardized ID 
     */
	protected function rcmb_create_id( $string ) {
		
		// String to lower case.
		$string = strtolower( $string );
		
		// Replace spaces with hyphen.
        $string = str_replace( " ", "_", $string );
		
		// Add prefix to prevent conflicts
		$id = $this->prefix . $string;
		
		return $id;
	}
	
	
	
	
	/**
     * Generate metabox fields HTML markup
     *
     * Iterates through the $fields array and generates the HTML markup
     * and loads any saved values frmo the database for each specified field
     *
     * @resource $post 
     * @param array $fields array of Field labels and types to be created
     * @return string $input generated HTML markup for each field 
     */
	public function rcmb_display_meta_box($post) {
		
		//Nonce to verify the data
		wp_nonce_field($this->post_type . '_nonce', $this->post_type . '_nonce_field');
		
		//Loop through array to generate the fields
		foreach($this->fields as $label => $type){
			
			//Set the Field ID
			$field_id = $this->rcmb_create_id($label);
			
			//Get Value from Database
			$db_value = get_post_meta($post->ID, $field_id , true);
			
			//Create HTML markup
			echo '<div class="field-container">';
			echo '<label for="' . $field_id . '">'. esc_html($label) .'</label>';
			
			//Determine the type of field to create
			switch ( $type ) {
				case 'textarea':
					$input = sprintf(
						'<textarea class="large-text" id="%s" name="%s" rows="5">%s</textarea>',
						$field_id,
						$field_id,
						$db_value
					);
					break;
				case 'editor':
					$content = $db_value ? html_entity_decode($db_value) : '';
					$settings = array(
						'media_buttons' => false,
						'textarea_rows' => 5,
						'quicktags' => false,
						'teeny' => true
					);
					$input = wp_editor($content, $field_id, $settings);
					break;
				case 'image':
					$image_url = wp_get_attachment_image_url( $db_value, 'thumbnail');
					$preview = sprintf(
						'<img id="%s_preview" class="add_meta_image_preview" src="%s">',
						$field_id,
						$image_url
					);
					$hidden = sprintf(
						'<input type="hidden" name="%s" id="%s" class="add_meta_image" value="%s" class="regular-text" />',
						$field_id,
						$field_id,
						$db_value
					);
					$button = sprintf(
						'<input type="button" class="upload-meta-image button-primary" value="Upload %s" id="uploadimage"/><br />',
						$label
					);
					$input = $preview . $hidden . $button;
					break;
				default:
					$input = sprintf(
						'<input %s id="%s" name="%s" type="%s" value="%s">',
						$type !== 'color' ? 'class="regular-text"' : '',
						$field_id,
						$field_id,
						$type,
						$db_value
					);
			}
			echo $input;
			echo '</div>';
			
		}
		
	}
	
	
	
	/**
     * Save and update post meta
     *
     * Hooks into WordPress' save_post function to save the post meta
     * to the designated $fields when editing a post.
     *
     * @see https://developer.wordpress.org/reference/hooks/save_post/ 
     * @see https://codex.wordpress.org/Function_Reference/update_post_meta
     */
	public function rcmb_save_post( $post_id ) {
		
		//Verify the nonce field exists
		if ( ! isset( $_POST[$this->post_type . '_nonce_field'] ) )
			return $post_id;
		
		//Verify nonce value
		$nonce = $_POST[$this->post_type . '_nonce_field'];
		if ( !wp_verify_nonce( $nonce, $this->post_type . '_nonce' ) )
			return $post_id;
		
		//Autosave goodness
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		//loop through each field and save POSTed data
		foreach ( $this->fields as $label => $type ) {
			
			$field_id = $this->rcmb_create_id($label);
			
			if ( isset( $_POST[ $field_id ] ) ) {
				switch ( $type ) {
					case 'email':
						$_POST[ $field_id ] = sanitize_email( $_POST[ $field_id ] );
						break;
					case 'text':
						$_POST[ $field_id ] = sanitize_text_field( $_POST[ $field_id ] );
						break;
					case 'editor':
						$_POST[ $field_id ] = esc_html( $_POST[ $field_id ] );
						break;
					case 'image':
						$_POST[ $field_id ] = absint( $_POST[ $field_id ] );
						break;	
				}
				update_post_meta( $post_id, $field_id, $_POST[ $field_id ] );
			}
		}
	}
	
 }