<?php
/**
 * Developer Toolbox Class
 *
 * Allows user to create a list of web development tools with corresponding skill level and years of experience.
 *
 * @author  Stephen Brody
 * @link    http://stephenbrody.com
 * @version 0.1.0
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 */
 
add_action( 'init', array( 'Toolbox', 'init' ));
 

class Toolbox extends Abilities {
	
	/**
     * Holds the class instance for singleton style instantiation.
     *
     * @var self
     */
	public static $instance;
	
	
	/**
     * Designates the current database table version for this class
     *
     * @var string
     */
	public $db_version = '0.4.0';
	
	
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
     * Add database table, enqueue scripts/styles, etc, etc.
     *
     */
    public function __construct() {
	    
	    //Assign slug
	    $this->slug = strtolower(__CLASS__);
	    
		//Assign table_name
    	$this->table_name = $this->generate_table_name();
    	
    	//Assign DB version option name
    	$this->db_version_option = $this->generate_db_option_name();
    	
    	//Load db, screen_options, and menu_page actions 
    	parent::__construct();
    	
    	//AJAX Submission for Toolbox Form
    	add_action( 'wp_ajax_submit_tool_ajax', array ( $this, 'submit_tool_ajax' ));
    	
    	//AJAX Update for X-Editable Toolbox Field Data
    	add_action( 'wp_ajax_update_tool_ajax', array ( $this, 'update_tool_ajax' ));
    	
    }
    
    
	/**
     * Define Database SQL Query
     *
     * Defines the SQL query for the class database table.
     *
     * It seemed pointlessly redundant to have separate query definitons for 
     * both the create and update functions since create only runs if the table doesn't exist. 
     * At least this way the theme will always be up to date with the correct schema.
     *
     * @var string $charset_collate
     * @property string $table_name
     * @return string $sql
     *
     */
    protected function define_db_sql() {
	    
	    global $wpdb;
		
		//Get table char collation
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE $this->table_name (
				id int(4) NOT NULL AUTO_INCREMENT,
				user_id mediumint(9) NOT NULL,
				name varchar(55) NOT NULL,
				experience int(3) NOT NULL,
				level int(3) NOT NULL,
				date_created datetime NOT NULL,
				date_updated datetime NOT NULL,
				updated_by mediumint(9) NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";
	    
	    //Assign SQL query to db_sql property
	    return $sql;
    }
    
    
     
    /**
     * Load Admin Menu Page
     *
     * sets page config and loads dependencies for screen options, JS, and CSS
     *
     * @return void
     */
    public function add_admin_menu_page() {
	    
	    //Set $hook property and add page
	    $this->hook = add_menu_page(
			'Web Development Tools & Proficiency',  // page title
			'Toolbox',            					// menu title
			'manage_options',                  	  	// capability
			$this->slug,                          	// menu slug
			array ( $this, 'render_admin_page' ),  	// callback function
			'dashicons-editor-code',				// menu_icon
			22										// position
		);

		
		// Load screen option parameters / args
		add_action( "load-" . $this->hook, array ( $this, 'screen_option' ) );
		
		//Load default styles and scripts
		parent::load_admin_menu_page_scripts();
		
    }
    
	
	/**
	 * Screen options
	 *
	 * Sets default screen options and officially instantiates the abilities_list_table
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'Tools',
			'default' => 5,
			'option'  => 'tools_per_page'
		];

		add_screen_option( $option, $args );
		
		//Instantiate the list table and config settings
		$this->list_table_config();
		
	}
	
	
	/**
	 * New Abilities_List_Table and Configuration
	 *
	 * Instantiates the list table and defines the config settings
	 *
	 * @return void
	 */
	public function list_table_config() {
		
		// Create new table!
		$table = $this->table_display = new Toolbox_List_Table();
		
	}
    
    
    /**
	 * Render Admin Page Content
	 *
	 * This was originally a separate template file but I found it easier to include it here instead. 
	 * It might be worth breaking it out later on but it's minimal HTML right now, so nbd.
	 *
	 * @return html
	 */
    public function render_admin_page() {
	    
	    // Verify User Permissions
	    if(!current_user_can('manage_options')) { ?>
			<p>You do not have sufficient permissions to access this page</p>
			
		<?php } else { ?>
		
		<div class="wrap">
			<h2><?php global $title; echo __($title, 'overbuilt-resume'); ?></h2>
			<div id="poststuff">
				<div id="notice-wrapper"></div>
				<div id="post-body" class="metabox-holder">
					<div id="post-body-content">
						<form id="add_ability" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
							<div class="field-wrap">
								<label for="name">Tool</label>
								<input type="text" name="name" id="name" value="">
							</div>
							<div class="field-wrap">
								<label for="experience">Years of Experience</label>
								<input type="text" name="experience" id="experience" value="">
							</div>
							<div class="field-wrap ">
								<label for="level">How proficient are you?</label>
								<input type="range" name="level" id="level" value="50" min="0" max="100" step="1">
								<span class="range__value">0</span>
							</div>
							
							<label for="submit">&nbsp;</label>
							<?php wp_nonce_field( 'submit_tool_ajax', 'submit_tool_ajax_nonce' ); ?>
							<input type="hidden" name="action" id="action" value="submit_tool_ajax">
							<input type="submit" value="Add Tool" id="submit_ability_button" class="button button-primary button-large">
						</form>
						<div id="add_tool_response"></div>
					</div><!-- #post-body-content -->
					
					<div class="meta-box-sortables ui-sortable">
						<form method="post">
							<?php
							$this->table_display->prepare_items();
							$this->table_display->display(); 
							?>
						</form>
					</div><!-- .metabox-sortables -->
				</div><!-- #post-body -->
			</div><!-- #poststuff -->
		</div><!-- .wrap -->
		
		<?php } 
    }
    
    
    /**
	 * AJAX Tool Submission
	 *
	 * Ajax callback for the "add tool" form
	 * checks permissions then wraps meta in an array for insertion into the db
	 * 
	 * @var array $meta
	 * @return string $message
	 */
    public function submit_tool_ajax() {
	    
	    $nonce = $_POST['submit_tool_ajax_nonce'];
	    
	    // Verify nonce and user permissions
	    if ( !wp_verify_nonce( $nonce, 'submit_tool_ajax' ) || !current_user_can('manage_options'))
			wp_die ( 'Sorry, You do not have permission to submit tools.');
		
		//Set data into array for creating a skill
		$meta = array(
			'name'		 => sanitize_text_field($_POST[ 'name' ]),
			'experience' => absint($_POST[ 'experience' ]),
			'level' 	 => absint($_POST[ 'level' ])
		);
		
		//Try creating a new skill!
		$new_tool = $this->create( $meta );
		
		// Basic error handling
		if ( $new_tool === false ) {
			$type = 'error';
			$message = "Oops! Unable to add new tool, please try again.";
		} else { 
			$type = 'success';
			$message = "Success! You've added a new tool!";
		}
		
		add_action('admin_notices', array( $this, 'display_admin_notice'), 10, 2 );
		do_action('admin_notices', $type, $message);
		
		wp_die();
		
    }
    
    /**
	 * AJAX Update Tool Data
	 *
	 * Ajax callback for inline editing via X-Editable
	 * 
	 * @var int $id
	 * @var string $column
	 * @var string|int $value
	 * @return string $message
	 *
	 * NOTE TO SELF: Consider adding a nonce field for verification (especially since they are used everywhere else).
	 */
    public function update_tool_ajax() {
	    
	    if ( !current_user_can('manage_options'))
			wp_die ( wp_json_encode('Sorry, You do not have permission to update tools.') );
		
		//Get variables
		$id 	= absint($_POST['pk']);
		$column = sanitize_text_field($_POST['name']);
		$value	= sanitize_text_field($_POST['value']);
			
		//Update field/column data
		$update = $this->update($id, $column, $value);
			
		// Basic error handling
		if ( $update === false ) {
			$type = 'error';
			$message = "Oops! Unable to update your toolbox, please try again.";
		} else { 
			$type = 'success';
			$message = "Success! Your tool has been updated!";
		}
		
		add_action('admin_notices', array( $this, 'display_admin_notice'), 10, 2 );
		do_action('admin_notices', $type, $message);
		
		wp_die();
    }
    
    public function display_admin_notice($type, $message) {
	    echo '<div class="notice notice-' . $type .' is-dismissible"><p>' . $message . '</p></div>';
    }
    
}