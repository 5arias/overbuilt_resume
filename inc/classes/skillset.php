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
 
add_action( 'init', array( 'mySkills', 'init' ));
 
class mySkills {
	
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
    
    
    
}