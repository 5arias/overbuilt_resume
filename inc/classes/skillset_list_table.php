<?php
/**
 * Skillset List Table
 *
 * custom class to display the list of available skills on the Skillset admin page.
 *
 * @author  Stephen Brody
 * @link    http://stephenbrody.com
 * 
 * Modified version of the tutorial code linked below.
 * I really liked the clean structure of the sitepoint tutorial so much that I decided
 * to simply make the necessary modifications to meet my needs for the class. 
 * @link https://www.sitepoint.com/using-wp_list_table-to-create-wordpress-admin-tables/
 *
 */
 
 
 // WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Skillset_List_Table extends WP_List_Table {
	
	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Skill', 'sp' ), 		//singular name of the listed records
			'plural'   => __( 'Skills', 'sp' ), 	//plural name of the listed records
			'ajax'     => true 						//does this table support ajax?
		] );

	}


	/**
	 * Retrieve skillset data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_skills( $per_page = 5, $page_number = 1 ) {

		global $wpdb;
		
//NOTE TO SELF: Replace static table name with property from Skillset class. add $wpdb->prepare ???
		$sql = "SELECT * FROM {$wpdb->prefix}skillset";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}


	/**
	 * Delete a skill.
	 *
	 * @param int $id skill ID
	 */
	public static function delete_skill( $id ) {
		global $wpdb;
		
//NOTE TO SELF: Replace static table name with property from Skillset class. add $wpdb->prepare ???
		$wpdb->delete(
			"{$wpdb->prefix}skillset",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;
//NOTE TO SELF: Replace static table name with property from Skillset class. add $wpdb->prepare ???
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}skillset";

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no skillset data is available */
	public function no_items() {
		_e( 'You have no skills. Get your life together.', 'sp' );
	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
			case 'level':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
		);
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'sp_delete_skill' );

		$title = '<strong>' . $item['name'] . '</strong>';

		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&skill=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'name'    => __( 'Name', 'sp' ),
			'level'   => __( 'Proficiency', 'sp' ),
			'date_created' => __( 'Date Added', 'sp' )
		];

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', true ),
			'level' => array( 'level', false ),
			'date_created' => array( 'date_created', false )
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'skills_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_skills( $per_page, $current_page );
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'sp_delete_skill' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_skill( absint( $_GET['skill'] ) );

				wp_redirect( esc_url( add_query_arg() ) );
				exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_skill( $id );

			}

			wp_redirect( esc_url( add_query_arg() ) );
			exit;
		}
	}  
	
	
	
}
