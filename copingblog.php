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

function change_new_post_menu_item()
{
	global $wp_admin_bar;
	
	$args = array(
		'id' => 'new-post', // id of the existing child node (New > Post)
		'title' => 'Post (public)', // alter the title of existing node
    );

    $wp_admin_bar->add_node($args);


}

add_action('wp_before_admin_bar_render', 'change_new_post_menu_item');

function add_new_post_menu_item()
{
	global $wp_admin_bar;

	$args = array(
    	'id' => 'post-private',
    	'title' => 'Post (private)',
    	'href' => site_url() . '/wp-admin/post-new.php?visi=private',
    	'parent' => 'new-content',
		//'meta' => array('class' => 'my-toolbar-page')
  	);

  	$wp_admin_bar->add_node($args);
  	
	$args = array(
    	'id' => 'post-pwd',
    	'title' => 'Post (password protected)',
    	'href' => site_url() . '/wp-admin/post-new.php?visi=pwd',
    	'parent' => 'new-content',
		//'meta' => array('class' => 'my-toolbar-page')
  	);

  	$wp_admin_bar->add_node($args);
}

add_action('admin_bar_menu', 'add_new_post_menu_item');

function my_enqueue($hook) {
 //   if( 'post-new.php' != $hook || 'post.php' != $hook)
  //      return;
    wp_enqueue_script( 'my_custom_script', plugins_url('/copingblog.js', __FILE__) );
}

add_action( 'admin_enqueue_scripts', 'my_enqueue' );

// redirect to primary blog
function redirect_to_primary_blog()  {
	if(!is_site_admin()){
   	global $current_user;
	get_currentuserinfo();
	$blog_details = get_blog_details(get_usermeta($current_user->ID,'primary_blog'));
	if($_SERVER['HTTP_HOST'] != $blog_details->domain){
		header( 'Location: http://'.$blog_details->domain.'/wp-admin/&#39;' ) ;
	}
	}
}

//add_action('admin_init', 'redirect_to_primary_blog');