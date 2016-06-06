<?php
/**
 * My Skillset Class
 *
 * Allows user to create skills and corresponding skill level in admin and displays on frontend.
 *
 * @author  Stephen Brody
 * @link    http://stephenbrody.com
 * @version 0.3.0
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 */
 
add_action( 'init', array( 'Skillset', 'init' ));
 
class Skillset {
	
	/**
     * Holds the class instance for singleton style instantiation.
     *
     * @var self
     */
	static $instance;
	
	
	/**
     * Designates the current database table version for this class
     *
     * @var string
     */
	public $skillset_db_version = '0.3.2';


	/**
     * Class object for displaying the custom WP_List_Table for this class.
     *
     * @object class Skillset_List_Table.
     */
	public $skills_table;
	

	/**
     * Create slug for use in menu and enqueueing scripts
     *
     * @var string __CLASS__.
     */
	protected $slug = 'skillset';
	
	
	/**
     * Store table name for CRUD use - assigned on __construct().
     *
     * @var string $prefix + $slug.
     */
	private $table_name;
	
	
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
    	
    	//Assign table_name
    	$this->table_name = $this->generate_table_name();
    	
    	//Create a new database table on theme activation
    	add_action('wp_loaded', array($this, 'create_skillset_db_table'));
    	
    	//Update database table on load if new version exists.
    	add_action( 'wp_loaded', array($this, 'update_skillset_db_table' ));
    	
    	//Set Screen Options Filter
    	add_filter( 'set-screen-option', array( $this, 'set_screen' ), 10, 3 );
    	
    	//Create Admin Menu page
    	add_action('admin_menu', array($this, 'add_admin_menu_pages'));
    	
    	//AJAX Submission for Skill Form
    	add_action( 'wp_ajax_submit_skill_ajax', array ( $this, 'submit_skill_ajax' ));
    	
    	//AJAX Update for X-Editable Skill Field Data
    	add_action( 'wp_ajax_update_skill_ajax', array ( $this, 'update_skill_ajax' ));
    	
    }
    
    

    
    /**
     * Generates the table name - assigned on __construct().
     *
     * @return string $prefix + $slug.
     */
    private function generate_table_name() {
	    global $wpdb;
	    return $wpdb->prefix . $this->slug;
    }
    
    
    
    /**
     * Create Skillset Database Table
     *
     * Checks on activation if the _skillset table exists
     * and creates a new table if it does not.
     *
     * @method callable create_skillset_table
     *
     */
    public function create_skillset_db_table() {
	     
	    // Set table_name property as variable
		$table_name = $this->table_name;
		 
		global $wpdb; 
		 
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			
			//table not in database? Create new table!
			$charset_collate = $wpdb->get_charset_collate();
			
			$sql = "CREATE TABLE $table_name (
				id int(4) NOT NULL AUTO_INCREMENT,
				user_id mediumint(9) NOT NULL,
				name varchar(55) NOT NULL,
				level int(3) NOT NULL,
				date_created datetime NOT NULL,
				date_updated datetime NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";
			
			if ( ! function_exists('dbDelta') ) {
            	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        	}
        	
        	// Create DB Table
			dbDelta( $sql );
			
			// DB Version Control
			add_option( 'res_skillset_db_version', '0.1.0' );
		 	
		}
	     
    }
    
    
    /**
     * Update Skillset Database Table
     *
     * Checks the stored version in wp_options and updates the _skillset table
     * if it does not match the current version designated in the $skillset_db_version class property.
     *
     * @var string $skillset_db_version
     *
     */
    public function update_skillset_db_table() {
	    
	    // Set table_name property as variable
		$table_name = $this->table_name;
	    
    	// Get current DB version from property
		$current_db_version = $this->skillset_db_version;

		// If database version is not the same
		if( $current_db_version != get_option('res_skillset_db_version') ) {
			
        	global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id int(4) NOT NULL AUTO_INCREMENT,
				user_id mediumint(9) NOT NULL,
				name varchar(55) NOT NULL,
				level int(3) NOT NULL,
				date_created datetime NOT NULL,
				date_updated datetime NOT NULL,
				updated_by mediumint(9) NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";

        if ( ! function_exists('dbDelta') ) {
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        }
		
		// Update DB Table
        dbDelta( $sql );
		
		// Update DB version
        update_option( 'res_skillset_db_version', $current_db_version );
    	}
	}
     
     
    /**
     * Load Admin Menu Page
     *
     * sets page config and loads dependencies for screen options, JS, and CSS
     *
     * @return void
     */
    public function add_admin_menu_pages() {
	    
	    $hook = add_menu_page(
			'My Skills & Proficiency',              // page title
			'Skillset',            					// menu title
			'manage_options',                  	  	// capability
			$this->slug,                          	// menu slug
			array ( $this, 'render_admin_page' ),  	// callback function
			'dashicons-chart-bar',					// menu_icon
			21										// position
		);
		
		// Load screen option parameters / args
		add_action( "load-$hook", array ( $this, 'screen_option' ) );
		
		// make sure the jqueryui style callback is used on this page only
		add_action( "admin_print_styles-$hook", array( $this, 'load_jquery_ui' ) );
		
		// make sure the style callback is used on this page only
		add_action( "admin_print_styles-$hook", array( $this, 'enqueue_style' ) );
		
		// make sure the script callback is used on this page only
		add_action( "admin_print_scripts-$hook", array( $this, 'enqueue_script' ) );
		
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
	 * Load jQuery UI Smoothness stylesheet
	 *
	 * @return void
	 */
    function load_jquery_ui() {
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
		wp_enqueue_style( $this->slug . '_css', get_template_directory_uri() . '/inc/css/skillset-admin.css');
		
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
		
		// Skillset class scripts
		wp_enqueue_script( $this->slug . '_js', get_template_directory_uri() . '/inc/js/skillset-admin.js', array('jquery'), FALSE, TRUE);
	}
	
	
	/**
	 * Screen options
	 *
	 * Sets default screen options and officially instantiates the skillset list table
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'Skills',
			'default' => 5,
			'option'  => 'skills_per_page'
		];

		add_screen_option( $option, $args );
		
		// Create new table!
		$this->skills_table = new Skillset_List_Table();
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
				<div id="post-body" class="metabox-holder">
					<div id="post-body-content">
			
						<form id="add_skill" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
							<div class="field-wrap">
								<label for="skill_name">Skill Name</label>
								<input type="text" name="skill_name" id="skill_name" value="">
							</div>
							<div class="field-wrap ">
								<label for="skill_level">How proficient are you?</label>
								<input type="range" name="skill_level" id="skill_level" value="50" min="0" max="100" step="1">
								<span class="range__value">0</span>
							</div>
							
							<label for="submit">&nbsp;</label>
							<?php wp_nonce_field( 'submit_skill_ajax', 'submit_skill_ajax_nonce' ); ?>
							<input type="hidden" name="action" id="action" value="submit_skill_ajax">
							<input type="submit" value="Add Skill" id="submit_skillset_button" class="button button-primary button-large">
						</form>
						<div id="add_skill_response"></div>
					</div><!-- #post-body-content -->
					
					<div class="meta-box-sortables ui-sortable">
						<form method="post">
							<?php
							$this->skills_table->prepare_items();
							$this->skills_table->display(); 
							?>
						</form>
					</div><!-- .metabox-sortables -->
				</div><!-- #post-body -->
			</div><!-- #poststuff -->
		</div><!-- .wrap -->
		
		<?php } 
    }
    
    
    
    /**
	 * Insert Skill into DB
	 *
	 * Adds a new skill w/ meta to the database.
	 * Data is sanitized within this method for added security
	 * 
	 * @param array $meta
	 */
    protected function insert_skill_to_db( $meta ) {
	    
	    // Make sure that we are provided a meta array
	    if( !is_array($meta) ) return false;
		    
		//Get User ID
		$user_id = get_current_user_id();
		
		//Get and Sanitize Post Data
		$name  = sanitize_text_field( $meta['name'] );
		$level = absint( $meta['level'] );
			
		//Insert Entry into Database Table
		global $wpdb;
			
		$wpdb->insert( 
			$this->table_name, 
			array( 
				'user_id' 		=> $user_id,
				'updated_by'	=> $user_id,
				'name' 			=> $name,
				'level' 	 	=> $level,
				'date_created'	=> current_time('mysql'),
				'date_updated'  => current_time('mysql')
			)
		);
	    
    }
    
    
    /**
	 * Update Skill Field
	 *
	 * Updates a single field for a given skill
	 * primarily used during ajax update via X-Editable
	 * Data is sanitized within this method for added security
	 * 
	 * @var int $id 
	 * @var string $column
	 * @var string|int $value
	 */
    protected function update_skill_field( $id, $column, $value) {
	    
	    // Sanitize the data!
	    $id = absint($id);
	    $column = sanitize_text_field($column);
	    
	    switch($column){
		    case 'name':
		    	$value = sanitize_text_field($value);
		    	break;
		    case 'level':
		    	$value = absint($value);
		    	break;
	    }
	    
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
	 * AJAX Skill Submission
	 *
	 * Ajax callback for the "add skill" form
	 * checks permissions then wraps meta in an array for insertion into the db
	 * 
	 * @var array $meta
	 * @return string $message
	 */
    public function submit_skill_ajax() {
	    
	    $nonce = $_POST['submit_skill_ajax_nonce'];
	    
	    // Verify nonce and user permissions
	    if ( !wp_verify_nonce( $nonce, 'submit_skill_ajax' ) || !current_user_can('manage_options'))
			wp_die ( 'Sorry, You do not have permission to submit skills.');
		
		//Set data into array for creating a skill
		$meta = array(
			'name'	=> $_POST[ 'skill_name' ],
			'level' => $_POST[ 'skill_level' ]
		);
		
		//Try creating a new skill!
		$new_skill = $this->insert_skill_to_db( $meta );
		
		// Basic error handling
		if ( $new_skill === false )
			$message = "Oops! Unable to add new skill, please try again.";
		else 
			$message = "Success! You've added a new skill!";
		
		wp_die( wp_json_encode($message) );
		
    }
    
    /**
	 * AJAX Update Skill Data
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
    public function update_skill_ajax() {
	    
	    if ( !current_user_can('manage_options'))
			wp_die ( wp_json_encode('Sorry, You do not have permission to update skills.') );
		
		//Get variables
		$id = $_POST['pk'];
		$column = $_POST['name'];
		$value	= $_POST['value'];
			
		//Update field/column data
		$update = $this->update_skill_field($id, $column, $value);
			
		// Basic error handling
		if ( $update === false )
			$message = "Oops! Unable to update your skill, please try again.";
		else 
			$message = "Success! Your skill has been updated!";
		
		wp_die( wp_json_encode($message) );
    }
    
    
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
    public static function getAll( $orderby = 'id', $order = 'ASC') {
	    
	    global $wpdb;
	    
	    //Sanitize Input
	    $orderby = sanitize_text_field($orderby);
	    $order = sanitize_text_field($order);
	    
	    //Get class table since it's used for the table name.
	    $class = strtolower(__CLASS__);
	    
	    // Run Query 
	    $entries = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}{$class} ORDER BY {$orderby} {$order}" );
	    
	    return $entries; 
    }
    
    
}