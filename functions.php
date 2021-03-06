<?php

if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		} );
	return;
}

Timber::$dirname = array('templates', 'views');
require get_template_directory() . '/inc/version.php';
global $package_version;

class LaunchframeSite extends TimberSite {
	function __construct() {
		add_theme_support( 'post-formats' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		add_theme_support( 'title-tag' );
		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		// add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );			
		add_action( 'init', array( $this, 'register_taxonomies' ) );
    	add_action('wp_enqueue_scripts', array( $this, 'lf_cleanup'));
    	add_action( 'wp_enqueue_scripts', array( $this, 'register_stylesheets' ) );
    	add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		parent::__construct();
	}
	function lf_cleanup() {
	// wp_deregister_script('jquery');
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	}
	function register_stylesheets() {
	  global $package_version;
	  wp_enqueue_style( 'application-style', get_template_directory_uri() . '/assets/dist/css/application.min.css', true, $package_version );
	}
	function register_scripts() {
	  global $package_version;
	  wp_enqueue_script( 'application-js', get_template_directory_uri() . '/assets/dist/js/script.min.js#async', array('jquery'), $package_version, true );
	}
	function register_post_types() {
		//this is where you can register custom post types
		//Cities custom post type
		$labels = array(
	    'name'               => _x( 'Cities', 'post type general name' ),
	    'singular_name'      => _x( 'City', 'post type singular name' ),
	    'add_new'            => _x( 'Add New', 'city' ),
	    'add_new_item'       => __( 'Add New City' ),
	    'edit_item'          => __( 'Edit City' ),
	    'new_item'           => __( 'New City' ),
	    'all_items'          => __( 'All Cities' ),
	    'view_item'          => __( 'View City' ),
	    'search_items'       => __( 'Search Cities' ),
	    'not_found'          => __( 'No cities found' ),
	    'not_found_in_trash' => __( 'No cities found in the Trash' ), 
	    'parent_item_colon'  => '',
	    'menu_name'          => 'Cities'
	  );
	  $args = array(
	    'labels'        => $labels,
	    'description'   => 'Holds Cities with their associated information.',
	    'public'        => true,
	    'menu_position' => 5,
	    'supports'      => array( 'title', 'editor', 'thumbnail'),
	    'menu_icon' => 'dashicons-location-alt',
	    'has_archive'   => true,
	  );
	  register_post_type( 'cities', $args ); 		
	}
	function register_taxonomies() {
		//this is where you can register custom taxonomies
	}
	function add_to_context( $context ) {
		$context['menu'] = new TimberMenu();
		$context['site'] = $this;
		return $context;
	}
	function add_to_twig( $twig ) {
		/* this is where you can add your own fuctions to twig */
		//$twig->addExtension( new Twig_Extension_StringLoader() );
		//$twig->addFilter( 'myfoo', new Twig_Filter_Function( 'myfoo' ) );
		//return $twig;
	}	
}

include('inc/utility-functions.php');

new LaunchframeSite();
