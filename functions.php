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
			//add the city meta data box
			add_action( 'add_meta_boxes', array( $this, 'city_meta_box' ) );
			//save data from the city meta data box
			add_action( 'save_post', array( $this, 'city_meta_box_save' ) );
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
	  register_post_type( 'city', $args ); 		
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
	function city_meta_box() {
		//add the city meta box
	  add_meta_box( 
	    'city_meta_box',
	    __( 'City Details', 'myplugin_textdomain' ),
	    array( $this, 'city_meta_box_content' ),
	    'city',
	    'side',
	    'default'
	  );
	}
	function city_meta_box_content( $post ) {
		//populate the city meta data box
		$cityRegion = get_post_meta( $post->ID, 'city_region', true ); //select	
		$cityPopulation = get_post_meta( $post->ID, 'city_population', true ); //text field
		$cityLatitude = get_post_meta( $post->ID, 'city_latitude', true ); //text field
		$cityLongitude = get_post_meta( $post->ID, 'city_longitude', true ); //text field	
	    
		wp_nonce_field( 'wbcity_meta_box_save', 'wbcity_meta_box_content_nonce' );	
		?>
		<div>
			<label for="city_region">Region:</label>&nbsp;&nbsp;
		  <select class="required" id="city_region" name="city_region" placeholder="select region">
			  <option value="">- Select One -</option>
			  <option value="midwest" <?php if($cityRegion == 'midwest') echo 'selected'; ?>>Midwest</option>
			  <option value="northeast" <?php if($cityRegion == 'northeast') echo 'selected'; ?>>Northeast</option>
			  <option value="pacific" <?php if($cityRegion == 'pacific') echo 'selected'; ?>>Pacific</option>
			  <option value="south" <?php if($cityRegion == 'south') echo 'selected'; ?>>South</option>
			  <option value="west" <?php if($cityRegion == 'west') echo 'selected'; ?>>West</option>
			</select>
		</div>
		<div>
		  <label for="city_population">Population:</label>&nbsp;&nbsp;
		  <input type="text" class="required" id="city_population" name="city_population" value="<?php echo $cityPopulation; ?>" placeholder="177000" size="14"/>
		</div>
		<div>
			<label for="city_latitude">Latitude:</label>&nbsp;&nbsp;
		  <input type="text" class="required" id="city_latitude" name="city_latitude" value="<?php echo $cityLatitude; ?>" placeholder='35.045630' size="7" />
		</div>
		<div>  
		  <label for="city_longitude">Longitude:</label>&nbsp;&nbsp;
		  <input type="text" class="required" id="city_longitude" name="city_longitude" value="<?php echo $cityLongitude; ?>" placeholder='-85.309680' size="7" />
		</div>
		<?php
	}
	function city_meta_box_save( $post_id ) {
	  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	  if ( ! isset( $_POST['wbcity_meta_box_content_nonce'] ) ) return;
	  if ( !wp_verify_nonce( $_POST['wbcity_meta_box_content_nonce'], 'wbcity_meta_box_save' ) ) return;
	  if ( 'page' == $_POST['post_type'] ) {
	    if ( !current_user_can( 'edit_page', $post_id ) ) return;
	  } else {
	    if ( !current_user_can( 'edit_post', $post_id ) ) return;
	  }
	  $cityRegion = $_POST['city_region']; //select 
	  $cityPopulation = $_POST['city_population']; //text field
		$cityLatitude = $_POST['city_latitude']; //text field
		$cityLongitude = $_POST['city_longitude']; //text field		
	  update_post_meta( $post_id, 'city_region', $cityRegion );
		update_post_meta( $post_id, 'city_population', $cityPopulation );
		update_post_meta( $post_id, 'city_latitude', $cityLatitude );
		update_post_meta( $post_id, 'city_longitude', $cityLongitude );
	}
}

include('inc/utility-functions.php');

new LaunchframeSite();
