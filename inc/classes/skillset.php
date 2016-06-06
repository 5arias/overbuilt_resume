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
 
class Skillset extends Abilities {
	
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
	public $db_version = '0.3.2';
	
	
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
    	
    	//AJAX Submission for Skill Form
    	add_action( 'wp_ajax_submit_skill_ajax', array ( $this, 'submit_skill_ajax' ));
    	
    	//AJAX Update for X-Editable Skill Field Data
    	add_action( 'wp_ajax_update_skill_ajax', array ( $this, 'update_skill_ajax' ));
    	
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
			'My Skills & Proficiency',              // page title
			'Skillset',            					// menu title
			'manage_options',                  	  	// capability
			$this->slug,                          	// menu slug
			array ( $this, 'render_admin_page' ),  	// callback function
			'dashicons-chart-bar',					// menu_icon
			21										// position
		);
		
		// Load screen option parameters / args
		add_action( "load-" . $this->hook, array ( $this, 'screen_option' ) );
		
		//Load default styles and scripts
		parent::load_admin_menu_page_scripts();
		
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
		$this->table_display = new Skillset_List_Table();
		
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
								<label for="name">Skill Name</label>
								<input type="text" name="name" id="name" value="">
							</div>
							<div class="field-wrap ">
								<label for="level">How proficient are you?</label>
								<input type="range" name="level" id="level" value="50" min="0" max="100" step="1">
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
			'name'	=> sanitize_text_field($_POST[ 'name' ]),
			'level' => absint($_POST[ 'level' ])
		);
		
		//Try creating a new skill!
		$new_skill = $this->create( $meta );
		
		// Basic error handling
		if ( $new_skill === false ) {
			$type = 'error';
			$message = "Oops! Unable to add new skill, please try again.";
		} else {
			$type = 'success';
			$message = "Success! You've added a new skill!";
		}
		
		add_action('admin_notices', array( $this, 'display_admin_notice'), 10, 2 );
		do_action('admin_notices', $type, $message);
		
		wp_die();
		
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
		$id 	= absint($_POST['pk']);
		$column = sanitize_text_field($_POST['name']);
		$value	= sanitize_text_field($_POST['value']);
			
		//Update field/column data
		$update = $this->update($id, $column, $value);
			
		// Basic error handling
		if ( $update === false ) {
			$type = 'error';
			$message = "Oops! Unable to update your skill, please try again.";
		} else { 
			$type = 'success';
			$message = "Success! Yourskill has been updated!";
		}
		
		add_action('admin_notices', array( $this, 'display_admin_notice'), 10, 2 );
		do_action('admin_notices', $type, $message);
		
		wp_die();
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