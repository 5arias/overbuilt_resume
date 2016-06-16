<?php
/**
 * Resume functions and definitions.
 *
 * @package Overbuilt_Resume
 * @since overbuilt_resume 0.1.0
 *
 * Thanks to the following sites for some great WP clean up functions.
 * @Matteo Spinelli - http://cubiq.org/clean-up-and-optimize-wordpress-for-your-next-theme
 *
 */
 
 if ( ! function_exists( 'overbuilt_resume_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function overbuilt_resume_setup() {
	
	// launching operation cleanup
    add_action('init', 'overbuilt_resume_head_cleanup');
    
    // Remove generator name from RSS feeds (just for good measure even though we removed the feed links above)
	add_filter('the_generator', '__return_false');
	
	// Disable Admin Bar on Front End (it bothers me during development)
	add_filter('show_admin_bar','__return_false'); 
	
	// Add Title Tag support
	add_theme_support( 'title-tag' );
	
	// Makes theme available for translation.
	load_theme_textdomain( 'overbuilt_resume', get_template_directory() . '/languages' );


	// Enable support for Post Thumbnails on posts and pages. (shouldn't this be standard already? )
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'overbuilt_resume' ),
	) );

	//Switch default core markup for search form, comment form, and comments to output valid HTML5.
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	//Enable support for Post Formats.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );
}
endif;
add_action( 'after_setup_theme', 'overbuilt_resume_setup' );




/*=====================================================================================
  The default wordpress head is a mess. Let's clean it up by removing all the junk we don't need.
  @since overbuilt_resume 0.1.0
======================================================================================*/
function overbuilt_resume_head_cleanup() {
	
	// Remove post, comment, and category feeds
	remove_action( 'wp_head', 'feed_links', 2 );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	
	// Remove Really Simple Discovery (aka EditURI) link
	remove_action( 'wp_head', 'rsd_link' );
	
	// Remove Windows live writer
	remove_action( 'wp_head', 'wlwmanifest_link' );
	
	// Remove Page Shortlink
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
	
	// Remove index link
	remove_action( 'wp_head', 'index_rel_link' );
	
	// Remove previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	
	// Remove start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	
	// Remove links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0);
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	
	// Remove WP version
	remove_action( 'wp_head', 'wp_generator' );
	
	//Remove Emoji Support (totally not gonna use that anytime soon)
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	
	// Disable REST API since it's not being used for this project
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	remove_action( 'rest_api_init', 'wp_oembed_register_route');
	remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10);
	
}


/*=====================================================================================
  Remove Query Strings from Static Resources to prevent issues with caching and CDNs
  @link https://www.keycdn.com/blog/speed-up-wordpress/#caching
  @since overbuilt_resume 0.1.0
======================================================================================*/
function _remove_script_version( $src ){
	$parts = explode( '?ver', $src );
	return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );






/*=====================================================================================
  Remove hAtom filters from content container
  @since overbuilt_resume 0.1.0
======================================================================================*/
function remove_add_mod_hatom_data() {
    remove_filter( 'the_content', 'add_mod_hatom_data' );
}
add_action( 'wp_loaded', 'remove_add_mod_hatom_data' );






/*=====================================================================================
  Enqueue our fonts  
  @since overbuilt_resume 0.1.0
======================================================================================*/
function overbuilt_resume_fonts() {
	//Fonts
	wp_enqueue_style('font-unicaone', '//fonts.googleapis.com/css?family=Unica+One' );
	wp_enqueue_style('font-vollkorn', '//fonts.googleapis.com/css?family=Vollkorn:700,400italic,400' );
	wp_enqueue_style('font-fontawesome', get_template_directory_uri() . '/inc/vendors/font-awesome/font-awesome.min.css' );

}
add_action( 'wp_enqueue_scripts', 'overbuilt_resume_fonts' );





/*=====================================================================================
  Enqueue our scripts and styles
  @since overbuilt_resume 0.1.0
======================================================================================*/
function overbuilt_resume_scripts() {
	
	//Foundation for Sites
	wp_enqueue_style( 'overbuilt_resume-foundation-css', get_template_directory_uri() . '/inc/vendors/foundation/foundation.min.css' );
	wp_enqueue_script( 'overbuilt_resume-foundation-js', get_template_directory_uri() . '/inc/vendors/foundation/foundation.min.js', array('jquery'), '', true );
	wp_enqueue_script( 'overbuilt_resume-what-input', get_template_directory_uri() . '/inc/vendors/foundation/what-input.js', array('jquery'), '', true );
	
	// jQuery Scrollfire
	wp_enqueue_script( 'overbuilt_resume-scrollfire-js', get_template_directory_uri() . '/inc/vendors/scrollfire/jquery.scrollfire.min.js', array('jquery'), '', true );

	
	//CSS
	wp_enqueue_style( 'overbuilt_resume-default', get_template_directory_uri() . '/inc/css/theme-default.min.css' );
	wp_enqueue_style( 'overbuilt_resume-style', get_stylesheet_uri() );
	
	//JS
	wp_enqueue_script( 'overbuilt_resume-app', get_template_directory_uri() . '/inc/js/app.min.js', array('jquery'), '', true );

}
add_action( 'wp_enqueue_scripts', 'overbuilt_resume_scripts' );





/*=====================================================================================
  Redirect All front tend pages to homepage
  
  Since this is meant to be a single page site, there's no need to allow access
  to any pages other than the homepage.
  
  @since overbuilt_resume 0.1.0
======================================================================================*/
function redirect_all_to_home()
{
    if( !is_home() )
    {
        wp_redirect( home_url() );
        exit();
    }
}
add_action( 'template_redirect', 'redirect_all_to_home' );




/*=====================================================================================
  Remove unneccessary menu optioins from the Admin Menu
  
  Some default wordpress menu options aren't relevant for this particular project,
  so I'm cleaning up the admin menu for the sake of it. I like the cleaner look.
  
  @since overbuilt_resume 0.1.0
======================================================================================*/
function remove_unused_menu_pages() {
    remove_menu_page( 'edit.php' );                   //Posts
    remove_menu_page( 'edit.php?post_type=page' );    //Pages
	remove_menu_page( 'edit-comments.php' );          //Comments
	remove_menu_page( 'tools.php' );                  //Tools
}
add_action( 'admin_menu', 'remove_unused_menu_pages' );




/*=====================================================================================
  Custom Classes
  @since overbuilt_resume 0.1.0
======================================================================================*/
/*
 * Singleton style self instantiated classes 
 */
 
//Abstract
require('classes/abilities.php');

//Skillset
require('classes/skillset.php');
require('classes/skillset_list_table.php');

//Toolbox
require('classes/toolbox.php');
require('classes/toolbox_list_table.php');

//User Profile Mods
require('classes/profilemods.php');


/*
 * Autoloads non-singleton classes such as CPT and RCMB
 *
 * NOTE: the singleton classes need to stay above this function 
 * otherwise it will try to include classes like WP_List_Table too
 */
spl_autoload_register(function ($class_name) {
    include 'classes/' . $class_name . '.php';
});





/*=====================================================================================
  Work History Custom Post Type
  
  Uses the CPT and RCMB Classes
  @link http://github.com/jjgrainger/wp-custom-post-type-class/
======================================================================================*/
$history = new CPT(
	array(
    	'post_type_name' => 'work_history',
		'singular' 		 => 'Work History',
		'plural' 		 => 'Work History',
		'slug' 		 	 => 'work_history'
		),
	array(
		'menu_position'	 => 20,
		'menu_icon'		 => 'dashicons-businessman',
		'supports' 		 => array('title')
		)
);

/*
 * Define Columns
 *
 * uses the meta_tag to determine data to load
 */
$history->columns(
	array(
    	'cb' 			 		 => '<input type="checkbox" />',
		'meta_rcmb_start_date'	 => __('Start Date'),
		'meta_rcmb_end_date'  	 => __('End Date'),
		'title' 		 		 => __('Title'),
		'meta_rcmb_organization' => __('Organization'),
		'meta_rcmb_location'   	 => __('Location'),
		'meta_rcmb_website_url'  => __('Website'),
		'organization_logo'		 => __('Logo'),
		'date' 			 		 => __('Date')
));

/*
 * Populate Logo Column with thumbnail
 */
$history->populate_column('organization_logo', function($column, $post) {
	
	$attach_id = $post->rcmb_organization_logo;
	if ($attach_id)
		echo wp_get_attachment_image( $attach_id , 'thumbnail');

});

/*
 * Define Sortable Columns
 */
$history->sortable(array(
    'meta_rcmb_start_date' 	 => array('rcmb_start_date', true),
    'meta_rcmb_end_date' 	 => array('rcmb_end_date', true),
    'meta_rcmb_organization' => array('rcmb_organization', true),
    'meta_rcmb_location'	 => array('rcmb_location', true)
));


/*
 * Add Custom metaboxes
 */
 $work_history_metaboxes = new RCMB(
	'work_history',
	'Work Experience', 
	array(
		'Organization Logo' => 'image',
		'Organization' 	    => 'text',
		'Start Date' 	    => 'text',
		'End Date' 		    => 'text',
		'Location' 		    => 'text',
		'Website URL' 	    => 'text',
		'Job Description'   => 'editor'
		)
	);





/*=====================================================================================
  Portfolio Custom Post Type
  
  Uses the CPT and RCMB Classes
  @link http://github.com/jjgrainger/wp-custom-post-type-class/
======================================================================================*/
$portfolio = new CPT(
	array(
    	'post_type_name' => 'portfolio',
		'singular' 		 => 'Portfolio Item',
		'plural' 		 => 'Portfolio',
		'slug' 		 	 => 'portfolio'
		),
	array(
		'menu_position'	 => 23,
		'menu_icon'		 => 'dashicons-vault',
		'taxonomies' 	 => array('post_tag'),
		'supports' 		 => array('title')
		)
);


/*
 * Define Columns
 *
 * uses the meta_tag to determine data to load
 */
$portfolio->columns(
	array(
    	'cb' 			=> '<input type="checkbox" />',
		'screenshot'	=> __('Screenshot'),
		'title' 		=> __('Title'),
		'meta_rcmb_website_url'   => __('Website'),
		'date' 			=> __('Date')
));


/*
 * Populate Screenshot Column with thumbnail
 */
$portfolio->populate_column('screenshot', function($column, $post) {
	
	$attach_id = $post->rcmb_screenshot;
	if ($attach_id)
		echo wp_get_attachment_image( $attach_id , 'thumbnail');

});


/*
 * Define Sortable Columns
 */
$portfolio->sortable(array(
    'meta_rcmb_website_url' 	 => array('rcmb_website_url', true)
));


/*
 * Add Custom metaboxes
 */
$portfolio_metaboxes = new RCMB(
	'portfolio',
	'Portfolio', 
	array(
		'Website URL' 	    	=> 'text',
		'Website Description'   => 'editor',
		'Screenshot' 			=> 'image'
		)
	);

 
 
 
 
 
 
 
 
 
 
 
 
 
 ?>