<?php
/**
 * My Skillset Class (mySkills)
 *
 * Allows user to create skills and corresponding skill level in admin and displays on frontend.
 *
 * @author  Stephen Brody
 * @link    http://stephenbrody.com
 * @version 0.1.0
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 */
 
add_action( 'init', array( 'Skillset', 'init' ));
 
class Skillset {
	
	// class instance
	static $instance;

	// customer WP_List_Table object
	public $skills_obj;
	
	//Create slug for use in menu and enqueueing scripts
	protected $slug = 'skillset';
	
	// store table name for CRUD use - assigned on __construct
	private $table_name;
	
	/**
     * Init
     *
     * Instantiates the class on WP init
     *
     * @var string $class The name of the class.
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
    	
    	//Set Screen Options Filter
    	add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
    	
    	//Create Admin Menu page
    	add_action('admin_menu', array($this, 'add_admin_menu_pages'));
    	
    	//AJAX Submission for Skill Form
    	add_action( 'wp_ajax_submit_skill_ajax', array ( $this, 'submit_skill_ajax' ));
    	
    	//AJAX Submission for Skill Form
    	add_action( 'wp_ajax_update_skill_ajax', array ( $this, 'update_skill_ajax' ));
    	
    }
    
    
    /**
     * Autoload
     *
     * dynamically autoload undefined classes
     *
     *
	function __autoload() {
		require("skillset_list_table.php");
	}
	*/
    
    // Create $table_name
    private function generate_table_name() {
	    global $wpdb;
	    return $wpdb->prefix . $this->slug;
    }
    
    
    
    /**
     * Create Skillset Table
     *
     * Checks on activation if the _skillset table exists
     * and creates a new table if it does not.
     *
     * @method callable create_skillset_table
     *
     * NOTE TO SELF: at some point you should probably add columns for updated_by and date_updated when you create the update functions.
     * Don't forget to add a table update and versioning function too...
     */
    public function create_skillset_db_table() {
	     
	    global $wpdb;
		$table_name = $this->table_name;
		 
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			
			//table not in database? Create new table
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
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		 	
		}
	     
    }
    
    
    //Set Screen Options
    public static function set_screen( $status, $option, $value ) {
		return $value;
	}
     
     
    //Add and render admin page
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

		add_action( "load-$hook", array ( $this, 'screen_option' ) );
		
		add_action( "admin_print_styles-$hook", array( $this, 'load_jquery_ui' ) );
		
		// make sure the style callback is used on our page only
		add_action( "admin_print_styles-$hook", array( $this, 'enqueue_style' ) );
		
		// make sure the script callback is used on our page only
		add_action( "admin_print_scripts-$hook", array( $this, 'enqueue_script' ) );
		
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
	 * Load stylesheet
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
		
		// Sckillset class scripts
		wp_enqueue_script( $this->slug . '_js', get_template_directory_uri() . '/inc/js/skillset-admin.js', array('jquery'), FALSE, TRUE);
	}
	
	
	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'Skills',
			'default' => 5,
			'option'  => 'skills_per_page'
		];

		add_screen_option( $option, $args );

		$this->skills_obj = new Skillset_List_Table();
	}
    
    
    //Render admin page via template part..
    public function render_admin_page() {
	    if(!current_user_can('manage_options')) { ?>
			<p>You do not have sufficient permissions to access this page</p>
			
		<?php } else { 
			//echo get_template_part('template-parts/' . $this->slug, 'admin');
		?>
		
		<div class="wrap">
			<h1><?php global $title; echo __($title, 'overbuilt-resume'); ?></h1>
			<h2><?php echo current_time('mysql'); ?></h2>
			<div class="wrap">
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
				<?php //wp_nonce_field( 'submit_skill_ajax', 'submit_skill_ajax_nonce' ); ?>
				<input type="hidden" name="action" id="action" value="submit_skill_ajax">
				<input type="submit" value="Add Skill" id="submit_skillset_button" class="button button-primary button-large">
			</form>
			<div id="add_skill_response"></div>
		</div><!-- .wrap Form - Add Skill -->

		<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								$this->skills_obj->prepare_items();
								$this->skills_obj->display(); 
								?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
	</div>
		
		<?php } 
    }
    
    
    
    // Insert new skill into database
    public function insert_skill_to_db( $user_id, $meta ) {
	    
	    // Make sure that we are provided a user ID and a meta array
	    if(isset($user_id) && !empty($meta)){
			
			global $wpdb;
			
			//Insert Entry into Database Table
			$wpdb->insert( 
				$this->table_name, 
				array( 
					'user_id' 		=> $user_id,
					'name' 			=> $meta['name'],
					'level' 	 	=> $meta['level'],
					'date_created'	=> current_time('mysql'),
					'date_updated' => current_time('mysql')
				)
			);
		}
	    
    }
    
    
    // AJAX function to add new skill to db
    public function submit_skill_ajax() {
	    
	    $nonce = $_POST['submit_skill_ajax_nonce'];
	    
	    // Verify nonce and user permissions
	    //if ( !wp_verify_nonce( $nonce, 'submit_skill_ajax' ) || !current_user_can('manage_options'))
		//	wp_die ( 'Sorry, You do not have permission to submit skills.');
		
		//Get User ID
		$user_id = get_current_user_id();
		
		//Get and Sanitize Post Data
		$skill_name  = sanitize_text_field( $_POST[ 'skill_name' ] );
		$skill_level = absint( $_POST[ 'skill_level' ] );
		
		//Set data into array for creating a skill
		$meta = array(
			'name'	=> $skill_name,
			'level' => $skill_level
		);
		
		//Try creating a new skill!
		//NOTE Need to add error handling.....
		$this->insert_skill_to_db( $user_id, $meta );
		
		$message = 'Successfully added!';
		
		wp_die( wp_json_encode($message) );
		
    }
    
    // AJAX function to add new skill to db
    public function update_skill_ajax(){
	    if($_REQUEST){
		 	
		 	//Get variables
			$id = $_POST['pk'];
			$column = $_POST['name'];
			$value	= $_POST['value'];
			
			//Update field/column
			global $wpdb;
			$wpdb->update(
				$this->table_name,
				array(
					$column => $value,
					'date_updated' => current_time('mysql')
				),
				array(
					'id' => $id
				)
			);
		
		    
		    $message = 'Successfully updated!';
	    }
		
		wp_die( wp_json_encode($message) );
    }
    
    
}