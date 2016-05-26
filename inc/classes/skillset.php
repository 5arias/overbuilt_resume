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
	
	//Create slug for use in menu and enqueueing scripts
	protected $slug = 'skillset';
	
	/**
     * Init
     *
     * Instantiates the class on WP init
     *
     * @var string $class The name of the class.
     */
	public static function init() {
        $class = __CLASS__;
        new $class;
    }
	
	
	/**
     * Constructor
     *
     * Add database table, enqueue scripts/styles, etc, etc.
     *
     */
    public function __construct() {
    	
    	//Create a new database table on theme activation
    	add_action('wp_loaded', array($this, 'create_skillset_table'));
    	
    	//Create Admin Menu page
    	add_action('admin_menu', array($this, 'add_admin_menu_pages'));
    }
    
    
    
    /**
     * Create Skillset Table
     *
     * Checks on activation if the _skillset table exists
     * and creates a new table if it does not.
     *
     * @method callable create_skillset_table
     */
    public function create_skillset_table() {
	     
	    global $wpdb;
		$table_name = $wpdb->prefix . 'skillset';
		 
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			
			//table not in database? Create new table
			$charset_collate = $wpdb->get_charset_collate();
 
			$sql = "CREATE TABLE $table_name (
				id int(4) NOT NULL AUTO_INCREMENT,
				user_id mediumint(9) NOT NULL,
				name varchar(55) NOT NULL,
				skill_level int(3) NOT NULL,
				date_created datetime NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		 	
		}
	     
    }
     
     
    //Add and render admin page
    public function add_admin_menu_pages() {
	    
	    add_menu_page(
			'My Skills & Proficiency',              // page title
			'Skillset',            					// menu title
			'manage_options',                  	  	// capability
			$this->slug,                          	// menu slug
			array ( $this, 'render_admin_page' ),  	// callback function
			'dashicons-chart-bar',					// menu_icon
			21										// position
		);
		
		// make sure the style callback is used on our page only
		add_action( "admin_print_styles-" . $this->slug, array ( $this, 'enqueue_style' ) );
		do_action( "admin_print_styles-" . $this->slug, 'admin');
		
		// make sure the script callback is used on our page only
		add_action( "admin_print_scripts-" . $this->slug, array ( $this, 'enqueue_script' ));
		do_action( "admin_print_scripts-" . $this->slug, 'admin');
		
    }
    
    
    /**
	 * Load stylesheet
	 *
	 * @param string $location identifies whether to load on front end or admin panel
	 * @return void
	 */
	public function enqueue_style( $location ) {
		wp_enqueue_style( $this->slug . '_css', get_template_directory_uri() . '/inc/css/' . $this->slug . '-' . $location . '.css');
	}
	
	/**
	 * Load JavaScript
	 *
	 * @param string $location identifies whether to load on front end or admin panel
	 * @return void
	 */
	public function enqueue_script( $location ){
		wp_enqueue_script( $this->slug . '_js', get_template_directory_uri() . '/inc/js/' . $this->slug . '-' . $location . '.js', array('jquery'), FALSE, TRUE);
	}
    
    
    //Render admin page via template part..
    public function render_admin_page() {
	    if(!current_user_can('manage_options'))
			echo '<p>You do not have sufficient permissions to access this page</p>';
		else
			return get_template_part('template-parts/' . $this->slug, 'admin');
    }
    
    
    
}