<?php
/**
 * Abilities - Abstract Class
 *
 * An abstract class that defines common methods and requirements for resume sections that
 * don't need a standard CPT interface, such as skillsets, toolbox, etc.
 *
 * @author  Stephen Brody
 * @link    http://stephenbrody.com
 * @version 0.1.0
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 */
 
abstract class Abilities {
	

/* ============================================================================
*
*	DEFINE REQUIRED METHODS
*
* ============================================================================*/

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
    abstract protected function define_db_sql();
    
    
    /**
     * Load Admin Menu Page
     *
     * sets page config and loads dependencies for screen options, JS, and CSS
     *
     * @return void
     */
    abstract public function add_admin_menu_page();
    
    
    /**
	 * Screen options
	 *
	 * Sets default screen options and officially instantiates the skillset list table
	 */
	abstract public function screen_option();
	
	
	/**
	 * Render Admin Page Content
	 *
	 * Includes #add_ability Form and table display
	 *
	 * @return html
	 */
    abstract public function render_admin_page();
    
    
    /**
	 * Config settings for new Abilities_List_Table
	 *
	 * Includes #add_ability Form and table display
	 *
	 * @return html
	 */
    abstract public function list_table_config();
    
    
    
    /**
	 * getAll entries
	 *
	 * Returns class objects for all available entries for use on frontend
	 * 
	 * @arg string $orderby
	 * @arg string $order 
	 * @return objects $entries
	 *
	 * NOTE TO SELF: $wpdb->prepare was giving a weird syntax error, so for the time being, the args are sanitized. Fix it later.
	 */
    //abstract public static function getAll();
	
	
	 
/* ============================================================================
*
*	COMMON PROPERTIES
*
* ============================================================================*/
	/**
     * Class object for displaying the custom WP_List_Table for this class.
     *
     * @object class CLASS_List_Table.
     */
	public $table_display;
	
	
	/**
     * Store table name for CRUD use - assigned on __construct().
     *
     * @var string $prefix + $slug.
     */
	public $table_name;
	
	
	/**
     * Store option name for class database version - assigned on __construct().
     *
     * @var string $table_name + _db_option
     */
	protected $db_version_option;
	
	
	/**
     * Class slug for use in menu and enqueueing scripts
     * assigned on __construct()
     *
     * @var string __CLASS__.
     */
	protected $slug;
	
	
	/**
     * Store menu page hook.
     *
     * @var string $hook.
     */
	protected $hook;
	
	
	
	
/* ============================================================================
*
*	COMMON METHODS
*
* ============================================================================*/

	public function __construct() {
		//Create a new database table on theme activation
    	add_action('wp_loaded', array( $this, 'create_db_table'));
    	
    	//Update database table on load if new version exists.
    	add_action( 'wp_loaded', array( $this, 'update_db_table' ));
    	
    	//Set Screen Options Filter
    	add_filter( 'set-screen-option', array( $this, 'set_screen' ), 10, 3 );
    	
    	//Create Admin Menu page
    	add_action('admin_menu', array($this, 'add_admin_menu_page'));
    	
    	//Ajax Response to refresh wp_list_table
    	//add_action('wp_ajax_update_abilities_list_table_ajax', array($this, 'update_abilities_list_table_ajax') );
    	
	}	
	   
    
    /**
     * Generates the table name - assigned on __construct().
     *
     * @return string $prefix + $slug.
     */
    protected function generate_table_name() {
	    global $wpdb;
	    return $wpdb->prefix . $this->slug;
    }
    
    
    /**
     * Generates the database version option name - assigned on __construct().
     *
     * @var string $table_name + _db_option
     */
    protected function generate_db_option_name() {
	    return $this->table_name . '_db_version';
    }
    
    
    /**
     * Create Database Table
     *
     * Checks on activation if the database table exists
     * and creates a new table if it does not.
     *
     * @var string $table_name
     * @var string $db_version
     * @var string $db_version_option
     *
     */
    public function create_db_table() {
	     
	    // Set table_name property as variable
		$table_name = $this->table_name;
		
		 
		global $wpdb; 
		 
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			
			//Load sql statement and create new table!
			$sql = $this->define_db_sql();
			
			if ( ! function_exists('dbDelta') ) {
            	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        	}
        	
        	// Create DB Table
			dbDelta( $sql );
			
			// DB Version Control
			add_option( $this->db_version_option, $this->db_version );
		 	
		}
	     
    }
    
    
    /**
     * Update Database Table
     *
     * Checks the stored version in wp_options and updates the database table
     * if it does not match the current version designated in the $db_version_option property.
     *
     * @var string $db_version
     * @var string $db_version_option
     *
     */
    public function update_db_table() {
	    
    	// Get current DB version from property
		$current_db_version = $this->db_version;
		
		//Get Database option from property
		$option = $this->db_version_option;

		// If database version is not the same
		if( $current_db_version != get_option($option) ) {
			
        	//load sql statement
			$sql = $this->define_db_sql();

        if ( ! function_exists('dbDelta') ) {
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        }
		
		// Update DB Table
        dbDelta( $sql );
		
		// Update DB version
        update_option( $option, $current_db_version );
    	}
	}
	
    
    /**
     * Set Screen
     *
     * applies modified user submitted screen options to current admin screen
     *
     * @return mixed updated option
     */
    public function set_screen( $status, $option, $value ) {
		return $value;
	}
	
	
	/**
     * Load Admin Menu Page Scripts
     *
     * sets page config and loads dependencies for JS and CSS
     *
     * @return void
     */
    public function load_admin_menu_page_scripts() {
		
		// make sure the jqueryui style callback is used on this page only
		add_action( "admin_print_styles-" . $this->hook, array( $this, 'load_jquery_ui_theme' ) );
		
		// make sure the style callback is used on this page only
		add_action( "admin_print_styles-" . $this->hook, array( $this, 'enqueue_style' ) );
		
		// make sure the script callback is used on this page only
		add_action( "admin_print_scripts-" . $this->hook, array( $this, 'enqueue_script' ) );
		
    }
    
    
    /**
	 * Load jQuery UI Smoothness Theme
	 *
	 * @return void
	 */
    public function load_jquery_ui_theme() {
    	global $wp_scripts;
 
    	// get registered script object for jquery-ui
    	$ui = $wp_scripts->query('jquery-ui-core');
 
    	// tell WordPress to load the Smoothness theme from Google CDN
    	$protocol = is_ssl() ? 'https' : 'http';
    	$url = "$protocol://ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.min.css";
    	wp_enqueue_style('jquery-ui-smoothness', $url, false, null);
	}
    
    
    /**
	 * Load Stylesheets
	 *
	 * @return void
	 */
	public function enqueue_style() {
		
		// Default Class Styles
		wp_enqueue_style( $this->slug . '_css', get_template_directory_uri() . '/inc/css/abilities-admin.css');
		
		// X-Editable Styles
		wp_enqueue_style( $this->slug . '_xedit', get_template_directory_uri() . '/inc/css/jqueryui-editable.css');
	}
	
	/**
	 * Load JavaScript
	 *
	 * @return void
	 */
	public function enqueue_script(){
		
		// Load jQuery Ajax Form Plugin
		wp_enqueue_script( 'jquery-form' );
		
		// Load X-Editable
		wp_enqueue_script( $this->slug . '_xedit', get_template_directory_uri() . '/inc/js/jqueryui-editable.min.js', array('jquery', 'jquery-ui-button', 'jquery-ui-tooltip'), FALSE, TRUE);
		
		// Toolbox class scripts
		wp_enqueue_script( $this->slug . '_js', get_template_directory_uri() . '/inc/js/abilities-admin.js', array('jquery'), FALSE, TRUE);
	}
	
	
	/**
	 * Creat Ability and Insert into DB
	 *
	 * Adds a new abliity w/ meta to the respective database.
	 * Data is sanitized by the referring method to make this more flexible.
	 * 
	 * @param array $meta
	 *
	 * NOTE TO SELF: look into creating a separate santization method 
	 * which can be applied to the arrays within this method
	 */
    protected function create( $meta ) {
	    
	    // Make sure that we are provided a meta array
	    if( !is_array($meta) ) return false;
		    
		//Get User ID
		$user_id = get_current_user_id();
		
		//Set default fields not supplied by the form
		$sql = array( 
				'user_id' 		=> $user_id,
				'updated_by'	=> $user_id,
				'date_created'	=> current_time('mysql'),
				'date_updated'  => current_time('mysql'),
			);
		
		// Merge the two arrays for db insertion.
		$sql = array_merge($sql, $meta);
			
		//Insert Entry into Database Table
		global $wpdb;
			
		$wpdb->insert( $this->table_name, $sql);
	    
    }
    
    
    /**
	 * Update Ability Field Meta
	 *
	 * Updates a single field for a given ability
	 * primarily used during ajax update via X-Editable
	 * Data is sanitized within the referring method to make this more flexible.
	 * 
	 * @var int $id 
	 * @var string $column
	 * @var string|int $value
	 */
    protected function update( $id, $column, $value) {
	    
	    //Get current user ID
	    $user_id = get_current_user_id();
	    
	    //Update the field in the DB
	    global $wpdb;
		$wpdb->update(
			$this->table_name,
			array(
				$column => $value,
				'date_updated' => current_time('mysql'),
				'updated_by' => $user_id
			),
			array(
				'id' => $id
			)
		);
    }
    
    
    /**
	 * Display Admin Notice
	 * 
	 * Generates the html code for the admin notice to be displayed
	 *
	 * @param string $type (success | error | warning )
	 * @param string $message
	 * @return HTML
	 */
    public function display_admin_notice($type, $message) {
	    echo '<div class="notice notice-' . $type .' is-dismissible"><p>' . $message . '</p></div>';
    }
    
    
    
    
	/**
	 * Callback function for 'wp_ajax__ajax_fetch_custom_list' action hook.
	 * 
	 * Loads the Custom List Table Class and calls ajax_response method
	 *
	public function update_abilities_list_table_ajax() {
	
		$this->screen_option();
		$this->table_display->ajax_response();
	}
	*/
	
}