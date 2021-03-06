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
 
 ob_start();
 
// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


/* Hide notices to avoid AJAX errors
 * Sometimes the Class throws a notice about 'hook_suffix' being undefined,
 * which breaks every AJAX call.
 */
error_reporting( ~E_NOTICE );




class Skillset_List_Table extends WP_List_Table {
	
	/**
     * Store table name for CRUD use
     * Assigned on __construct
     *
     * @var string $table_name ($prefix + $slug).
     */
	public static $db_table;
	
	/** Class constructor */
	public function __construct() {
		
		global $wpdb;
		
		//Assign 
		self::$db_table = $wpdb->prefix . 'skillset';

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
		
		$sql = "SELECT * FROM " . self::$db_table;

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
	 * @param int $id skill id
	 */
	public static function delete_skill( $id ) {
		global $wpdb;

		$wpdb->delete(
			self::$db_table,
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

		$sql = "SELECT COUNT(*) FROM " . self::$db_table;

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
			case 'id':
			case 'user_id':
			case 'updated_by':
			case 'date_created':
			case 'date_updated':
				return $item[ $column_name ];
			default:
				$title = '<a href="#" class="xedit" data-type="text" data-name="' . $column_name . '" data-pk="' . $item['id'] . '" >' . $item[ $column_name ] . '</a>';
				$actions = [
					'edit'   => sprintf('<a href="#" class="xedit-button" for="%s" data-pk="%d">Edit</a>', $column_name, absint( $item['id'] ))
				];
				return $title . $this->row_actions( $actions );
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
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
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

		$title = '<strong><a href="#" class="xedit" data-type="text" data-name="name" data-pk="' . $item['id'] . '" >' . $item['name'] . '</a></strong>';

		$actions = [
			'edit'   => sprintf('<a href="#" class="xedit-button" data-type="text" for="%s" data-pk="%d" >Edit</a>', 'name', absint( $item['id'] )),
			'delete' => sprintf( '<a href="?page=%s&action=%s&skill=%s&_wpnonce=%s">Delete</a>', 'skillset', 'delete', absint( $item['id'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}
	
	
	/**
	 * Method for skill_level column (aka Proficiency)
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_level( $item ) {

		//$delete_nonce = wp_create_nonce( 'sp_delete_skill' );

		$title = '<a href="#" class="xedit" data-type="range" data-name="level" data-pk="' . $item['id'] . '" >' . $item['level'] . '</a>';

		$actions = [
			'edit'   => sprintf('<a href="#" class="xedit-button" data-type="range" for="%s" data-pk="%d">Edit</a>', 'level', absint( $item['id'] ))
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
			'date_created' => __( 'Date Added', 'sp' ),
			'date_updated' => __( 'Date Updated', 'sp' )
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
			'name' => array( 'name', false ),
			'level' => array( 'level', false ),
			'date_created' => array( 'date_created', false ),
			'date_updated' => array( 'date_updated', false )
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
		
		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$per_page     = $this->get_items_per_page( 'skills_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			//WE have to calculate the total number of items
			'total_items' => $total_items,
			//WE have to determine how many items to show on a page
			'per_page'    => $per_page,
			//WE have to calculate the total number of pages
			'total_pages'	=> ceil( $total_items / $per_page )
		] );
		
		/**
		 * REQUIRED. Set data
		 */
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

				wp_redirect( esc_url_raw( add_query_arg() ) );
				exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record ids and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_skill( $id );

			}

			wp_redirect( esc_url_raw( add_query_arg() ) );
			exit;
		}
	}
	
	
	
	
	/**
	 * Display the table
	 * Adds a Nonce field and calls parent's display method
	 *
	 * @since 3.1.0
	 * @access public
	 */
	function display() {

		wp_nonce_field( 'ajax-ability-table-nonce', 'ability_table_nonce' );

		echo '<input type="hidden" id="order" name="order" value="' . $this->_pagination_args['order'] . '" />';
		echo '<input type="hidden" id="orderby" name="orderby" value="' . $this->_pagination_args['orderby'] . '" />';
		echo '<input type="hidden" id="xedit_action" name="xedit_action" value="update_skill_ajax" />';
		echo '<input type="hidden" id="list_update_action" name="list_update_action" value="update_skillset_list_table_ajax" />';

		parent::display();
	}
	

	/**
	 * Handle an incoming ajax request (called from admin-ajax.php)
	 *
	 * @since 3.1.0
	 * @access public
	 */
	function ajax_response() {

		check_ajax_referer( 'ajax-ability-table-nonce', 'ability_table_nonce' );

		$this->prepare_items();

		extract( $this->_args );
		extract( $this->_pagination_args, EXTR_SKIP );

		ob_start();
		if ( ! empty( $_REQUEST['no_placeholder'] ) )
			$this->display_rows();
		else
			$this->display_rows_or_placeholder();
		$rows = ob_get_clean();
		
		
		ob_start();
		$this->print_column_headers();
		$headers = ob_get_clean();

		ob_start();
		$this->pagination('top');
		$pagination_top = ob_get_clean();

		ob_start();
		$this->pagination('bottom');
		$pagination_bottom = ob_get_clean();
		
		$response = array( 'rows' => $rows );
		$response['pagination']['top'] = $pagination_top;
		$response['pagination']['bottom'] = $pagination_bottom;
		$response['column_headers'] = $headers;

		if ( isset( $total_items ) )
			$response['total_items_i18n'] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );

		if ( isset( $total_pages ) ) {
			$response['total_pages'] = $total_pages;
			$response['total_pages_i18n'] = number_format_i18n( $total_pages );
		}

		//return $response;
		wp_die( wp_json_encode($response) );
	}
	
}


/**
 * Callback function for 'wp_ajax__ajax_fetch_custom_list' action hook.
 * 
 * Loads the Custom List Table Class and calls ajax_response method
 */
function update_skillset_list_table_ajax() {

	$skill_list_table = new Skillset_List_Table();
	$skill_list_table->ajax_response();
}

add_action('wp_ajax_update_skillset_list_table_ajax', 'update_skillset_list_table_ajax');
