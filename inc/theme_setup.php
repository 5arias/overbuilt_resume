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
	//remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	//remove_action( 'rest_api_init', 'wp_oembed_register_route');
	//remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10);
	
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
  Enqueue our scripts and styles
  @since overbuilt_resume 0.1.0
======================================================================================*/
function overbuilt_resume_scripts() {
	//CSS
	wp_enqueue_style( 'overbuilt_resume-underscores', get_template_directory_uri() . '/inc/css/underscores.css' );
	wp_enqueue_style( 'overbuilt_resume-foundation-css', get_template_directory_uri() . '/inc/css/foundation.min.css' );
	wp_enqueue_style( 'overbuilt_resume-style', get_stylesheet_uri() );
	
	//JS
	wp_enqueue_script( 'overbuilt_resume-navigation', get_template_directory_uri() . '/inc/js/navigation.js', array(), '20151215', true );
	wp_enqueue_script( 'overbuilt_resume-skip-link-focus-fix', get_template_directory_uri() . '/inc/js/skip-link-focus-fix.js', array(), '20151215', true );
	wp_enqueue_script( 'overbuilt_resume-foundation-js', get_template_directory_uri() . '/inc/js/foundation.min.js', array('jquery'), '', true );
	wp_enqueue_script( 'overbuilt_resume-what-input', get_template_directory_uri() . '/inc/js/what-input.js', array('jquery'), '', true );
	wp_enqueue_script( 'overbuilt_resume-scrollfire-js', get_template_directory_uri() . '/inc/js/jquery.scrollfire.min.js', array('jquery'), '', true );
	wp_enqueue_script( 'overbuilt_resume-app', get_template_directory_uri() . '/inc/js/app.js', array('jquery'), '', true );

}
add_action( 'wp_enqueue_scripts', 'overbuilt_resume_scripts' );





/*=====================================================================================
  Custom template tags for this theme.
  @since overbuilt_resume 0.1.0
======================================================================================*/
require get_template_directory() . '/inc/template-tags.php';

 
 
 
 
 
 
 
 
 
 
 
 
 
 ?>