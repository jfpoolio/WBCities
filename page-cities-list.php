<?php
/**
 * The template for displaying the list of cities
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/views/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

$args = array(
	//retrieve cities posts 
	'post_type' => 'cities',
	'posts_per_page' => -1,
	'orderby' => array(
    'post_title' => 'ASC'
	));
$context['cities'] = Timber::get_posts( $args );

Timber::render( array( 'page-cities-list.twig', 'page-cities-list.twig' ), $context );
