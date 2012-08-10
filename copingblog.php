<?php
/*
Plugin Name: coping blog plugin
Plugin URI: http://copingblog.dev
Description: A brief description of the Plugin.
Version: 0.1
Author: Andrea Cantieni
Author URI: http://schwyz.phz.ch
License: GPL2
*/

function set_visibility($post_ID, $post)
{
	global $wpdb;
	
	$visi = isset($_GET["visi"]) ? $_GET["visi"] : "public";

	if ($visi == "pwd") 
	{
		$wpdb->update( $wpdb->posts, array( 'post_password' => rand(1000,9999) ), array( 'ID' => $post_ID ) );
	}

	if ($visi == "private") 
	{
		$wpdb->update( $wpdb->posts, array( 'post_status' => 'private' ), array( 'ID' => $post_ID ) );
	}
}

add_action('save_post','set_visibility', 10, 2);



function my_enqueue($hook) {
    if( 'post-new.php' != $hook && 'post.php' != $hook)
       return;
    wp_enqueue_script( 'my_custom_script', plugins_url('/copingblog.js', __FILE__) );

}

add_action( 'admin_enqueue_scripts', 'my_enqueue', 10, 1 );

